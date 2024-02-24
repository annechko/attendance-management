<?php

namespace App\Repository;

use App\Entity\Course;
use App\Filter\SearchFilter;
use App\Filter\CourseSort;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Course>
 *
 * @method Course|null find($id, $lockMode = null, $lockVersion = null)
 * @method Course|null findOneBy(array $criteria, array $orderBy = null)
 * @method Course[]    findAll()
 * @method Course[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourseRepository extends ServiceEntityRepository
{
    private const MAX_PER_PAGE = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    public function buildSortedFilteredPaginatedList(
        SearchFilter $filter,
        CourseSort $sort,
        PaginatorInterface $paginator,
    ): PaginationInterface {
        $qb = $this->createQueryBuilder('e')
            ->innerJoin('e.institution', 'i')
            ->select(
                'e.id as id',
                'e.name as name',
                'e.duration as duration',
                'i.name as institution',
            );
        if ($filter->search) {
            $qb->orWhere($qb->expr()->like('LOWER(e.name)', ':search'));
            $qb->orWhere($qb->expr()->like('LOWER(i.name)', ':search'));
            $qb->setParameter(':search', '%' . mb_strtolower($filter->search) . '%');
            if (is_numeric($filter->search)) {
                $qb->orWhere($qb->expr()->eq('(e.id)', ':search_num'));
                $qb->setParameter(':search_num', (int) $filter->search);
            }
        }

        return $paginator->paginate($qb, $sort->page, self::MAX_PER_PAGE);
    }
}
