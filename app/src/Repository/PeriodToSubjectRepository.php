<?php

namespace App\Repository;

use App\Entity\PeriodToSubject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PeriodToSubject>
 *
 * @method PeriodToSubject|null find($id, $lockMode = null, $lockVersion = null)
 * @method PeriodToSubject|null findOneBy(array $criteria, array $orderBy = null)
 * @method PeriodToSubject[]    findAll()
 * @method PeriodToSubject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PeriodToSubjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PeriodToSubject::class);
    }

    /**
     * @return array<PeriodToSubject>
     */
    public function findAllByPeriodId(int $periodId): array
    {
        return $this->createQueryBuilder('ps')
            ->where('ps.period = :id')
            ->setParameter(':id', $periodId)
            ->getQuery()
            ->getResult();
    }
}
