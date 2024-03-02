<?php

namespace App\Controller\Teacher;

use App\Form\AttendanceData;
use App\Form\AttendanceDataForm;
use App\Repository\TeacherToSubjectToIntakeRepository;
use App\Repository\TeacherRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/teacher')]
class HomeController extends AbstractTeacherController
{
    #[Route('/', name: 'teacher_home', methods: ['GET'])]
    public function index(
        TeacherToSubjectToIntakeRepository $teacherToSubjectToIntakeRepository,
        TeacherRepository $teacherRepository,
        Request $request
    ): Response {

        $teacher = $this->getCurrentTeacher();
        $teacherId = $teacher->getId();
        $list = $teacherToSubjectToIntakeRepository->findAllByTeacherId($teacherId);
        $teacherName = $teacher->getName() . ' ' . $teacher->getSurname();
        $attendanceDataForms = [];
        $today = new \DateTimeImmutable();
        foreach ($list as $teacherToSubjectToIntake) {
            $attendanceData = AttendanceData::fromTeacherToSubjectToIntake(
                $teacherToSubjectToIntake
            );
            $attendanceData->date = $today;
            $attendanceDataForm = $this->createForm(AttendanceDataForm::class, $attendanceData);
            $attendanceDataForms[$teacherToSubjectToIntake->getId(
            )] = $attendanceDataForm->createView();
        }
        return $this->render('teacher/index.html.twig', [
            'teacher' => $teacher,
            'teacher_to_subject_to_intakes' => $list,
            'attendanceDataForms' => $attendanceDataForms,
            'teacherId' => $teacherId,
            'teacherName' => $teacherName,
        ]);
    }
}
