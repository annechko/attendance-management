<?php

namespace App\Repository;

use App\Entity\Intake;
use App\Sort\AbstractSort;
use App\Sort\SearchFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Intake>
 *
 * @method Intake|null find($id, $lockMode = null, $lockVersion = null)
 * @method Intake|null findOneBy(array $criteria, array $orderBy = null)
 * @method Intake[]    findAll()
 * @method Intake[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntakeRepository extends ServiceEntityRepository
{
    private const MAX_PER_PAGE = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Intake::class);
    }

    public function buildSortedFilteredPaginatedList(
        SearchFilter $filter,
        AbstractSort $sort,
        PaginatorInterface $paginator,
    ): PaginationInterface {
        $qb = $this->createQueryBuilder('e')
            ->innerJoin('e.course', 'c')
            ->innerJoin('c.institution', 'i')
            ->select(
                'e.id as id',
                'e.name as name',
                'e.start as start',
                'e.finish as finish',
                'c.name as course',
                'i.name as institution',
            );
        if ($filter->search) {
            $qb->orWhere($qb->expr()->like('LOWER(e.name)', ':search'));
            $qb->orWhere($qb->expr()->like('LOWER(c.name)', ':search'));
            $qb->orWhere($qb->expr()->like('LOWER(i.name)', ':search'));
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
