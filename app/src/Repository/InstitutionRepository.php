<?php

namespace App\Repository;

use App\Entity\Institution;
use App\Filter\InstitutionFilter;
use App\Filter\InstitutionSort;
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
        InstitutionFilter $filter,
        InstitutionSort $sort,
        PaginatorInterface $paginator,
        int $size,
    ): PaginationInterface {
        $qb = $this->createQueryBuilder('i')
            ->addSelect(
                'i.id as id',
                'i.name as name',
                'i.location as location',
            );

        return $paginator->paginate($qb, $sort->page, $size);
    }
}
