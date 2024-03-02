<?php

namespace App\Repository;

use App\Entity\Attendance;
use App\Entity\Student;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Filter\AbstractSort;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;

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
    private const MAX_PER_PAGE = 20;

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
    public function getStudentCountGroupByStatus(\App\Entity\Student $student): array
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

    /**
     * @param \App\Entity\Student $student
     * @return array<int, int>
     */
    public function getCountForStatusByStudent(array $studentIds): array
    {
        $studentIds = count($studentIds) === 0 ? [0] : $studentIds;
        $b = $this->createQueryBuilder('a');
        $result = $b
            ->select('max(a.status) AS status')
            ->addSelect('max(a.student) AS student')
            ->addSelect('count(a.status) AS count')
            ->andWhere($b->expr()->in('a.student', $studentIds))
            ->groupBy('a.student', 'a.status')
            ->getQuery()
            ->getResult();
        $statusToCount = [];
        foreach ($result as $item) {
            $statusToCount[$item['student']][$item['status']] = $item['count'];
        }
        return $statusToCount;
    }

    public function getCountForStatusByStudentForTeacher(Teacher $teacher)
    {
        $b = $this->createQueryBuilder('a');
        $result = $b
            ->select('max(a.status) AS status')
            ->addSelect('max(a.student) AS student')
            ->addSelect('count(a.status) AS count')
            ->andWhere('a.teacher = :teacher')
            ->setParameter('teacher', $teacher)
            ->groupBy('a.student', 'a.status')
            ->getQuery()
            ->getResult();
        $statusToCount = [];
        foreach ($result as $item) {
            $statusToCount[$item['student']][$item['status']] = $item['count'];
        }
        return $statusToCount;
    }

    public function getAttendanceCount(Student $student, Subject $subject): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->andWhere('a.student = :student')
            ->andWhere('a.subject = :subject')
            ->setParameter('student', $student)
            ->setParameter('subject', $subject)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getAttendanceCountPresent(Student $student, Subject $subject): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->andWhere('a.student = :student')
            ->andWhere('a.subject = :subject')
            ->andWhere('a.status = :statusPresent')
            ->setParameter('student', $student)
            ->setParameter('subject', $subject)
            ->setParameter('statusPresent', Attendance::STATUS_PRESENT)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getAttendanceCountAbsent(Student $student, Subject $subject): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->andWhere('a.student = :student')
            ->andWhere('a.subject = :subject')
            ->andWhere('a.status = :statusPresent')
            ->setParameter('student', $student)
            ->setParameter('subject', $subject)
            ->setParameter('statusPresent', Attendance::STATUS_ABSENT)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getAttendanceCountExcuse(Student $student, Subject $subject): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->andWhere('a.student = :student')
            ->andWhere('a.subject = :subject')
            ->andWhere('a.status = :statusPresent')
            ->setParameter('student', $student)
            ->setParameter('subject', $subject)
            ->setParameter('statusPresent', Attendance::STATUS_EXCUSED)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getAllByTeacher(
        Teacher $teacher,
        \App\Filter\SearchFilter $filter,
        AbstractSort $sort,
        \Knp\Component\Pager\PaginatorInterface $paginator
    ): PaginationInterface {
        $qb = $this->createQueryBuilder('a')
            ->innerJoin('a.student', 'st')
            ->innerJoin('a.subject', 'su')
            ->select(
                'a.id as id',
                'a.date as date',
                'a.status as status',
                'a.comment as comment',
                'su.name as subject_name',
                'st.email as student_email',
            )
            ->andWhere('a.teacher = :teacher')
            ->setParameter(':teacher', $teacher);

        return $paginator->paginate($qb, $sort->page, self::MAX_PER_PAGE);
    }
}
