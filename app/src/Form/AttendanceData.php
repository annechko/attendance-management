<?php

namespace App\Form;

use App\Entity\Course;
use App\Entity\Intake;
use App\Entity\Subject;

class AttendanceData
{
    public ?Intake $intake = null;
    public ?\DateTimeImmutable $date = null;
    public ?Subject $subject = null;
    public ?Course $course = null;
}