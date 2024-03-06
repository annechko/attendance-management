<?php

namespace App\Repository;

use App\Entity\Teacher;
use App\Sort\AbstractSort;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Teacher>
 *
 * @implements PasswordUpgraderInterface<Teacher>
 *
 * @method Teacher|null find($id, $lockMode = null, $lockVersion = null)
 * @method Teacher|null findOneBy(array $criteria, array $orderBy = null)
 * @method Teacher[]    findAll()
 * @method Teacher[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeacherRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    private const MAX_PER_PAGE = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Teacher::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(
        PasswordAuthenticatedUserInterface $user,
        string $newHashedPassword
    ): void {
        if (!$user instanceof Teacher) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', $user::class)
            );
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @return array<Teacher>
     */
    public function findAllWithSort(string $sortField): array
    {
        return $this->createQueryBuilder('t')
            ->select('t')
            ->orderBy("t.$sortField")
            ->getQuery()
            ->getResult();
    }

    public function buildSortedFilteredPaginatedList(
        \App\Sort\SearchFilter $filter,
        AbstractSort $sort,
        \Knp\Component\Pager\PaginatorInterface $paginator
    ): PaginationInterface {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.teacherToSubjectToIntake', 'tsi')
            ->select(
                'e.id as id',
                'e.email as email',
                'CONCAT(e.name,\' \',e.surname) as full_name',
                'COUNT(tsi.subject) AS subjectsCount',
            )
            ->groupBy('e.id');
        if ($filter->search) {
            $qb->orWhere($qb->expr()->like('LOWER(e.name)', ':search'));
            $qb->orWhere($qb->expr()->like('LOWER(e.surname)', ':search'));
            $qb->orWhere($qb->expr()->like('LOWER(e.email)', ':search'));
            $qb->setParameter(':search', '%' . mb_strtolower($filter->search) . '%');
            if (is_numeric($filter->search)) {
                $qb->orWhere($qb->expr()->eq('e.id', ':search_num'));
                $qb->setParameter(':search_num', (int) $filter->search);
            }
        }

        return $paginator->paginate(
            $qb,
            $sort->page,
            self::MAX_PER_PAGE,
            ['defaultSortFieldName' => 'id', 'defaultSortDirection' => 'asc']
        );
    }
}
