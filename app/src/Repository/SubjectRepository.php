<?php

namespace App\Repository;

use App\Entity\Subject;
use App\Sort\AbstractSort;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * @extends ServiceEntityRepository<Subject>
 *
 * @method Subject|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subject|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subject[]    findAll()
 * @method Subject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubjectRepository extends ServiceEntityRepository
{
    private const MAX_PER_PAGE = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subject::class);
    }

    public function buildSortedFilteredPaginatedList(
        \App\Sort\SearchFilter $filter,
        AbstractSort $sort,
        \Knp\Component\Pager\PaginatorInterface $paginator
    ): PaginationInterface {
        $qb = $this->createQueryBuilder('e')
            ->innerJoin('e.course', 'c')
            ->select(
                'e.id as id',
                'e.name as name',
                'e.code as code',
                'c.name as course',
            );
        if ($filter->search) {
            $qb->orWhere($qb->expr()->like('LOWER(e.name)', ':search'));
            $qb->orWhere($qb->expr()->like('LOWER(e.code)', ':search'));
            $qb->orWhere($qb->expr()->like('LOWER(c.name)', ':search'));
            $qb->setParameter(':search', '%' . mb_strtolower($filter->search) . '%');
            if (is_numeric($filter->search)) {
                $qb->orWhere($qb->expr()->eq('e.id', ':search_num'));
                $qb->setParameter(':search_num', (int) $filter->search);
            }
        }

        return $paginator->paginate($qb, $sort->page, self::MAX_PER_PAGE);
    }
}
