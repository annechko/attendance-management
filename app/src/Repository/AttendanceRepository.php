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

    /**
     * @param \App\Entity\Student $student
     * @return array<Attendance>
     */
    public function findByStudent(\App\Entity\Student $student): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.student = :student')
            ->setParameter('student', $student)
            ->orderBy('a.date')
            ->getQuery()
            ->getResult();
    }

    public function findByAttendance(Attendance $attendance): ?Attendance
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.teacher = :teacher')
            ->andWhere('a.subject = :subject')
            ->andWhere('a.student = :student')
            ->andWhere('a.date = :date')
            ->setParameter('student', $attendance->getStudent())
            ->setParameter('subject', $attendance->getSubject())
            ->setParameter('teacher', $attendance->getTeacher())
            ->setParameter('date', $attendance->getDate())
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param \App\Entity\Student $student
     * @return array<int, int>
     */
    public function getCountGroupByStatus(\App\Entity\Student $student): array
    {
        $result = $this->createQueryBuilder('a')
            ->select('max(a.status) AS status')
            ->addSelect('count(a.status) AS count')
            ->andWhere('a.student = :student')
            ->setParameter('student', $student)
            ->groupBy('a.status')
            ->getQuery()
            ->getResult();
        $statusToCount = [];
        foreach ($result as $item) {
            $statusToCount[$item['status']] = $item['count'];
        }
        return $statusToCount;
    }
}
