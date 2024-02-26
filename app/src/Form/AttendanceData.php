<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Intake;
use App\Entity\Subject;
use App\Entity\TeacherToSubjectToIntake;

class AttendanceData
{
    public ?Intake $intake = null;
    public ?Subject $subject = null;
    public ?Course $course = null;
    public ?\DateTimeImmutable $date = null;

    public static function fromTeacherToSubjectToIntake(
        TeacherToSubjectToIntake $teacherToSubjectToIntake
    ): static {
        $data = new static();
        $data->intake = $teacherToSubjectToIntake->getIntake();
        $data->subject = $teacherToSubjectToIntake->getSubject();
        $data->course = $teacherToSubjectToIntake->getSubject()->getCourse();

        return $data;
    }
}