<?php

declare(strict_types=1);

namespace App\Sort;

use Symfony\Component\Validator\Constraints as Assert;

class TeacherSort extends AbstractSort
{
    #[Assert\Choice(['id', 'full_name', 'email', 'subjectsCount'])]
    public $sort = 'id';
}
