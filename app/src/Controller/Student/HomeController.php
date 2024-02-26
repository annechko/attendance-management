<?php

declare(strict_types=1);

namespace App\Controller\Student;

use App\Entity\Attendance;
use App\Repository\AttendanceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/student')]
class HomeController extends AbstractStudentController
{
    #[Route('', name: 'student_home')]
    public function index(AttendanceRepository $attendanceRepository): Response
    {
        $currentStudent = $this->getCurrentStudent();
        $attendanceCountByStatus = $attendanceRepository->getStudentCountGroupByStatus($currentStudent);

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

        return $this->render('student/index.html.twig', [
            'excusedPercent' => $excusedPercent,
            'absentPercent' => $absentPercent,
            'presentPercent' => $presentPercent,
        ]);

    }

    private function calculatePercentage(int $statusCount, int $totalCount): int
    {
        if ($totalCount === 0) {
            return 0;
        }
        return (int) (100 * $statusCount / $totalCount);
    }
}
