<?php

namespace App\Controller\Student;

use App\Entity\Attendance;
use App\Repository\AttendanceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/student/attendance')]
class AttendanceController extends AbstractStudentController
{
    #[Route('/', name: 'student_attendance_index', methods: ['GET'])]
    public function index(AttendanceRepository $attendanceRepository): Response
    {
        $currentStudent = $this->getCurrentStudent();
        return $this->render('student/attendance/index.html.twig', [
            'attendances' => $attendanceRepository->findByStudent($currentStudent),
        ]);
    }

    #[Route('/{id}', name: 'student_attendance_show', methods: ['GET'])]
    public function show(Attendance $attendance): Response
    {
        return $this->render('student/attendance/show.html.twig', [
            'attendance' => $attendance,
        ]);
    }
}
