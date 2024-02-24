<?php

namespace App\Repository;

use App\Entity\Institution;
use App\Filter\InstitutionSort;
use App\Filter\SearchFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Institution>
 *
 * @method Institution|null find($id, $lockMode = null, $lockVersion = null)
 * @method Institution|null findOneBy(array $criteria, array $orderBy = null)
 * @method Institution[]    findAll()
 * @method Institution[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstitutionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Institution::class);
    }

    public function buildSortedFilteredPaginatedList(
        SearchFilter $filter,
        InstitutionSort $sort,
        PaginatorInterface $paginator,
        int $size,
    ): PaginationInterface {
        $qb = $this->createQueryBuilder('e')
            ->addSelect(
                'e.id as id',
                'e.name as name',
                'e.location as location',
            );
        if ($filter->search) {
            $qb->orWhere($qb->expr()->like('LOWER(e.name)', ':search'));
            $qb->orWhere($qb->expr()->like('LOWER(e.location)', ':search'));
            $qb->setParameter(':search', '%' . mb_strtolower($filter->search) . '%');
            if (is_numeric($filter->search)) {
                $qb->orWhere($qb->expr()->eq('(e.id)', ':search_num'));
                $qb->setParameter(':search_num', (int) $filter->search);
            }
        }

        return $paginator->paginate($qb, $sort->page, $size);
    }
}
