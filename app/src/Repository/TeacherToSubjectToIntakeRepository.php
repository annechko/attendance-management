<?php

namespace App\Repository;

use App\Entity\TeacherToSubjectToIntake;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TeacherToSubjectToIntake>
 *
 * @method TeacherToSubjectToIntake|null find($id, $lockMode = null, $lockVersion = null)
 * @method TeacherToSubjectToIntake|null findOneBy(array $criteria, array $orderBy = null)
 * @method TeacherToSubjectToIntake[]    findAll()
 * @method TeacherToSubjectToIntake[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeacherToSubjectToIntakeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TeacherToSubjectToIntake::class);
    }

    /**
     * @param int $teacherId
     * @return array<TeacherToSubjectToIntake>
     */
    public function findAllByTeacherId(int $teacherId): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.teacher = :id')
            ->setParameter(':id', $teacherId)
            ->getQuery()
            ->getResult();
    }
}
