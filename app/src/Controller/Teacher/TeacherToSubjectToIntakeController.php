<?php

namespace App\Controller\Teacher;

use App\Form\AttendanceData;
use App\Form\AttendanceDataForm;
use App\Repository\TeacherRepository;
use App\Repository\TeacherToSubjectToIntakeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/teacher')]
class TeacherToSubjectToIntakeController extends AbstractTeacherController
{
    #[Route('/', name: 'teacher_to_subject_to_intake_index', methods: ['GET'])]
    public function index(
        TeacherToSubjectToIntakeRepository $teacherToSubjectToIntakeRepository,
        TeacherRepository $teacherRepository,
        Request $request
    ): Response {
        $teacherId = $this->getCurrentTeacher()->getId();

        $list = $teacherToSubjectToIntakeRepository->findAllByTeacherId($teacherId);
        $teacher = $teacherRepository->find($teacherId);
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

        return $this->render('teacher/teacher_to_subject_to_intake/index.html.twig', [
            'teacher_to_subject_to_intakes' => $list,
            'attendanceDataForms' => $attendanceDataForms,
            'teacherId' => $teacherId,
            'teacherName' => $teacherName,
        ]);
    }
}
