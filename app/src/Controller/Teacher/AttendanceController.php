<?php

namespace App\Controller\Teacher;

use App\Entity\Attendance;
use App\Entity\Student;
use App\Sort\AttendanceSort;
use App\Sort\SearchFilter;
use App\Sort\SortLoader;
use App\Form\AttendanceData;
use App\Form\AttendanceDataForm;
use App\Form\AttendanceType;
use App\Form\SearchFilterForm;
use App\Repository\AttendanceRepository;
use App\Repository\StudentRepository;
use App\Repository\SubjectRepository;
use App\Repository\TeacherRepository;
use App\Repository\TeacherToSubjectToIntakeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/teacher/attendance')]
class AttendanceController extends AbstractTeacherController
{
    #[Route('/', name: 'teacher_attendance_index', methods: ['GET'])]
    public function index(
        AttendanceRepository $attendanceRepository,
        TeacherToSubjectToIntakeRepository $teacherToSubjectToIntakeRepository,
        Request $request,
    ): Response {
        $teacher = $this->getCurrentTeacher();
        $teacherToSubjectToIntakeList = $teacherToSubjectToIntakeRepository->findAllByTeacherId(
            $teacher->getId()
        );
        $intakeIds = [];
        $subjectIds = [];
        foreach ($teacherToSubjectToIntakeList as $teacherToSubjectToIntake) {
            $intakeIds[] = $teacherToSubjectToIntake->getIntake()->getId();
            $subjectIds[] = $teacherToSubjectToIntake->getSubject()->getId();
        }
        $attendanceData = new AttendanceData();
        $attendanceDataForm = $this->createForm(AttendanceDataForm::class, $attendanceData, [
            'intakeIds' => $intakeIds,
            'subjectIds' => $subjectIds,
        ]);
        $attendanceDataForm->handleRequest($request);


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

    #[Route('/history', name: 'teacher_attendance_history', methods: ['GET'])]
    public function history(
        AttendanceRepository $attendanceRepository,
        TeacherToSubjectToIntakeRepository $teacherToSubjectToIntakeRepository,
        Request $request,
        PaginatorInterface $paginator,
        ValidatorInterface $validator,
        SortLoader $sortLoader,
    ): Response {
        $filter = new SearchFilter();
        $form = $this->createForm(SearchFilterForm::class, $filter);
        $form->handleRequest($request);

        $sort = new AttendanceSort();
        $sortLoader->load($sort, $request);
        $errors = $validator->validate($sort);
        if (count($errors) > 0) {
            return $this->redirectToRoute('teacher_attendance_history');
        }
        $teacher = $this->getCurrentTeacher();
        $attendances = $attendanceRepository->getAllByTeacher($teacher, $filter, $sort, $paginator);

        return $this->render('teacher/attendance/history.html.twig', [
            'search_form' => $form,
            'attendances' => $attendances,
        ]);
    }


    #[Route('/percentage', name: 'teacher_attendance_percentage', methods: ['GET'])]
    public function percentage(
        AttendanceRepository $attendanceRepository,
        StudentRepository $studentRepository,
        Request $request,
    ): Response {
        $intakeId = (int) $request->get('intakeId');
        if ($intakeId > 0) {
            $students = $studentRepository->findByIntakeId($intakeId);
            $attendanceByStudent = $attendanceRepository->getCountForStatusByStudent(
                array_keys($students)
            );
        } else {
            $attendanceByStudent = $attendanceRepository->getCountForStatusByStudentForTeacher(
                $this->getCurrentTeacher()
            );
            $students = $studentRepository->findByIdList(array_keys($attendanceByStudent));
        }

        $percentageByStudent = [];
        foreach ($attendanceByStudent as $studentId => $attendanceCountByStatus) {

            $totalCount = array_sum(array_values($attendanceCountByStatus));

            $absentPercent = $this->calculatePercentage(
                $attendanceCountByStatus[Attendance::STATUS_ABSENT] ?? 0,
                $totalCount
            );
            $excusedPercent = $this->calculatePercentage(
                $attendanceCountByStatus[Attendance::STATUS_EXCUSED] ?? 0,
                $totalCount
            );
            $presentPercent = $totalCount > 0 ? 100 - $excusedPercent - $absentPercent : 0;
            $percentageByStudent[$studentId] = [
                'excusedPercent' => $excusedPercent,
                'absentPercent' => $absentPercent,
                'presentPercent' => $presentPercent,
                'student' => $students[$studentId],
            ];
        }

        return $this->render('teacher/attendance/percentage/index.html.twig', [
            'percentageByStudent' => $percentageByStudent,
        ]);
    }

    private function calculatePercentage(int $statusCount, int $totalCount): int
    {
        if ($totalCount === 0) {
            return 0;
        }
        return (int) (100 * $statusCount / $totalCount);
    }

    #[Route('/add', name: 'teacher_attendance_add', methods: ['POST'])]
    public function add(
        Request $request,
        EntityManagerInterface $entityManager,
        SubjectRepository $subjectRepository,
        StudentRepository $studentRepository,
        TeacherRepository $teacherRepository,
        AttendanceRepository $attendanceRepository,
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
            $existedAttendance = $attendanceRepository->findByAttendance($attendance);
            if ($existedAttendance !== null) {
                $existedAttendance->setComment($attendance->getComment());
                $existedAttendance->setStatus($attendance->getStatus());
            } else {
                $entityManager->persist($attendance);
            }
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
