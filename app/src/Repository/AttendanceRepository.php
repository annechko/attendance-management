<?php

namespace App\Repository;

use App\Entity\Attendance;
use App\Entity\Subject;
use App\Entity\Teacher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Attendance>
 *
 * @method Attendance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Attendance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Attendance[]    findAll()
 * @method Attendance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttendanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attendance::class);
    }

    public function getAllForList(
        Subject $subject,
        \DateTimeImmutable $date,
        Teacher $teacher
    ) {
        return $this->createQueryBuilder('a')
            ->andWhere('a.teacher = :teacher')
            ->andWhere('a.subject = :subject')
            ->andWhere('a.date = :date')
            ->setParameter('subject', $subject)
            ->setParameter('teacher', $teacher)
            ->setParameter('date', $date)
            ->indexBy('a', 'a.student')
            ->getQuery()
            ->getResult();
    }
}
