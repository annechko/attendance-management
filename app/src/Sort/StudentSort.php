<?php

declare(strict_types=1);

namespace App\Sort;

use Symfony\Component\Validator\Constraints as Assert;

class StudentSort extends AbstractSort
{
    #[Assert\Choice(['id', 'full_name', 'email', 'institution', 'course', 'intake_name'])]
    public $sort = 'id';
}
