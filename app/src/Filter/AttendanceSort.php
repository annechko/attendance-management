<?php

declare(strict_types=1);

namespace App\Filter;

use Symfony\Component\Validator\Constraints as Assert;

class AttendanceSort extends AbstractSort
{
    #[Assert\Choice(['id', 'date', 'status', 'comment', 'student_email', 'student_fullname'])]
    public $sort = 'id';
}
