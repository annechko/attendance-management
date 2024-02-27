<?php

namespace App\Repository;

use App\Entity\Student;
use App\Filter\AbstractSort;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Student>
 *
 * @implements PasswordUpgraderInterface<Student>
 *
 * @method Student|null find($id, $lockMode = null, $lockVersion = null)
 * @method Student|null findOneBy(array $criteria, array $orderBy = null)
 * @method Student[]    findAll()
 * @method Student[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    private const MAX_PER_PAGE = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(
        PasswordAuthenticatedUserInterface $user,
        string $newHashedPassword
    ): void {
        if (!$user instanceof Student) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', $user::class)
            );
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @param array $ids
     * @return array<Student>
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function findByIdList(array $ids): array
    {
        $b = $this->createQueryBuilder('a');
        return $b
            ->andWhere($b->expr()->in('a.id', $ids))
            ->indexBy('a', 'a.id')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $intakeId
     * @return array<Student>
     * @throws \Doctrine\ORM\Query\QueryException
     */
    public function findByIntakeId(int $intakeId): array
    {
        $b = $this->createQueryBuilder('a');
        return $b
            ->andWhere($b->expr()->in('a.intake', $intakeId))
            ->indexBy('a', 'a.id')
            ->getQuery()
            ->getResult();
    }

    public function buildSortedFilteredPaginatedList(
        \App\Filter\SearchFilter $filter,
        AbstractSort $sort,
        \Knp\Component\Pager\PaginatorInterface $paginator
    ) {
        $qb = $this->createQueryBuilder('s')
            ->innerJoin('s.intake', 'i')
            ->innerJoin('i.course', 'c')
            ->innerJoin('c.institution', 'ins')
            ->select(
                's.id as id',
                'CONCAT(s.name,\' \',s.surname) as full_name',
                's.email as email',
                'i.name as intake_name',
                'i.id as intake_id',
                'c.name as course',
                'ins.name as institution',
            );
        if ($filter->search) {
            $qb->orWhere($qb->expr()->like('LOWER(s.name)', ':search'));
            $qb->orWhere($qb->expr()->like('LOWER(s.surname)', ':search'));
            $qb->orWhere($qb->expr()->like('LOWER(s.email)', ':search'));
            $qb->orWhere($qb->expr()->like('LOWER(c.name)', ':search'));
            $qb->orWhere($qb->expr()->like('LOWER(ins.name)', ':search'));
            $qb->orWhere($qb->expr()->like('LOWER(i.name)', ':search'));
            $qb->setParameter(':search', '%' . mb_strtolower($filter->search) . '%');
            if (is_numeric($filter->search)) {
                $qb->orWhere($qb->expr()->eq('s.id', ':search_num'));
                $qb->setParameter(':search_num', (int) $filter->search);
            }
        }

        return $paginator->paginate($qb, $sort->page, self::MAX_PER_PAGE);
    }
}
