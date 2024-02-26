<?php

namespace App\Controller\Teacher;

use App\Entity\Attendance;
use App\Entity\Student;
use App\Form\AttendanceData;
use App\Form\AttendanceDataForm;
use App\Form\AttendanceType;
use App\Repository\AttendanceRepository;
use App\Repository\StudentRepository;
use App\Repository\SubjectRepository;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/teacher/attendance')]
class AttendanceController extends AbstractTeacherController
{
    #[Route('/', name: 'teacher_attendance_index', methods: ['GET'])]
    public function index(
        AttendanceRepository $attendanceRepository,
        Request $request,
    ): Response {
        $attendanceData = new AttendanceData();
        $attendanceDataForm = $this->createForm(AttendanceDataForm::class, $attendanceData);
        $attendanceDataForm->handleRequest($request);

        $teacher = $this->getCurrentTeacher();

        $intake = $attendanceData->intake;
        $forms = [];
        $date = $attendanceData->date;
        $subject = $attendanceData->subject;
        $students = $intake?->getStudents() ?? [];
        if ($intake?->getCourse()?->getId() !== $subject?->getCourse()?->getId()) {
            $attendanceDataForm->addError(
                new FormError('Subject and intake should relate to the same course.')
            );
        }
        if ($subject && $date) {
            $attendances = $attendanceRepository->getAllForList($subject, $date, $teacher);
        }
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
            'attendanceData' => $attendanceDataForm,
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
}
