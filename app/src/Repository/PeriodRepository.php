<?php

namespace App\Repository;

use App\Entity\Period;
use App\Entity\PeriodToSubject;
use App\Sort\AbstractSort;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * @extends ServiceEntityRepository<Period>
 *
 * @method Period|null find($id, $lockMode = null, $lockVersion = null)
 * @method Period|null findOneBy(array $criteria, array $orderBy = null)
 * @method Period[]    findAll()
 * @method Period[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PeriodRepository extends ServiceEntityRepository
{
    private const MAX_PER_PAGE = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Period::class);
    }

    public function findAllWithOrder(array $orderFields): array
    {
        $qb = $this->createQueryBuilder('p');
        foreach ($orderFields as $orderField) {
            $qb->addOrderBy("p.$orderField", 'asc');
        }

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function buildSortedFilteredPaginatedList(
        \App\Sort\SearchFilter $filter,
        AbstractSort $sort,
        \Knp\Component\Pager\PaginatorInterface $paginator
    ): PaginationInterface {
        $qb = $this->createQueryBuilder('e')
            ->innerJoin('e.intake', 'i')
            ->innerJoin('i.course', 'c')
            ->select(
                'e.id as id',
                'e.name as name',
                'e.start as start',
                'e.finish as finish',
                'c.name as course',
                '(SELECT COUNT(s.id) FROM ' . PeriodToSubject::class . ' s WHERE s.period = e.id) AS subjectsCount',
                'i.name as intake',
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
