<?php

namespace App\Controller\Teacher;

use App\Entity\Attendance;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Form\AttendanceType;
use App\Repository\AttendanceRepository;
use App\Repository\IntakeRepository;
use App\Repository\StudentRepository;
use App\Repository\SubjectRepository;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/teacher/attendance')]
class AttendanceController extends AbstractController
{
    public function __construct(private readonly TeacherRepository $teacherRepository)
    {
    }

    #[Route('/', name: 'teacher_attendance_index', methods: ['GET'])]
    public function index(
        AttendanceRepository $attendanceRepository,
        IntakeRepository $intakeRepository,
        SubjectRepository $subjectRepository,
    ): Response {
        $intake = $intakeRepository->createQueryBuilder('c')
            ->orderBy('c.id')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        $teacher = $this->getCurrentTeacher();
        $forms = [];
        $date = new \DateTimeImmutable();
        $subject = $intake->getCourse()->getSubjects()->first();
        $students = $intake->getStudents();
        $attendances = $attendanceRepository->getAllForList($subject, $date, $teacher);
        foreach ($students as $student) {
            /** @var Student $student */
            $attendance = $attendances[$student->getId()] ?? null;
            if ($attendance === null) {
                $attendance = new Attendance();
                $attendance->setTeacher($teacher);
                $attendance->setStudent($student);
                $attendance->setDate($date);
                $attendance->setSubject($subject);
            }

            $form = $this->createForm(AttendanceType::class, $attendance);
            $forms[] = $form->createView();
        }

        return $this->render('teacher/attendance/index.html.twig', [
            'attendances' => $forms,
        ]);
    }

    #[Route('/new', name: 'teacher_attendance_new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SubjectRepository $subjectRepository,
        StudentRepository $studentRepository,
        TeacherRepository $teacherRepository,

    ): Response {
        $attendance = new Attendance();
        $form = $this->createForm(AttendanceType::class, $attendance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $student = $studentRepository->find((int) $form->get('studentId')->getData());
            $teacher = $teacherRepository->find((int) $form->get('teacherId')->getData());
            $subject = $subjectRepository->find((int) $form->get('subjectId')->getData());
            $attendance->setStudent($student);
            $attendance->setTeacher($teacher);
            $attendance->setSubject($subject);
            $entityManager->persist($attendance);
            $entityManager->flush();

            return $this->json([]);
        }

        return $this->json([]);
    }

    #[Route('/{id}', name: 'teacher_attendance_show', methods: ['GET'])]
    public function show(Attendance $attendance): Response
    {
        return $this->render('teacher/attendance/show.html.twig', [
            'attendance' => $attendance,
        ]);
    }

    #[Route('/{id}/edit', name: 'teacher_attendance_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Attendance $attendance,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(AttendanceType::class, $attendance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('teacher_attendance_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('teacher/attendance/edit.html.twig', [
            'attendance' => $attendance,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'teacher_attendance_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Attendance $attendance,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid(
            'delete' . $attendance->getId(),
            $request->request->get('_token')
        )) {
            $entityManager->remove($attendance);
            $entityManager->flush();
        }

        return $this->redirectToRoute('teacher_attendance_index', [], Response::HTTP_SEE_OTHER);
    }

    private function getCurrentTeacher(): Teacher
    {
        return $this->teacherRepository->findOneBy(
            ['email' => $this->getUser()->getUserIdentifier()]
        );
    }
}
