<?php

declare(strict_types=1);

namespace App\Sort;

use Symfony\Component\Validator\Constraints as Assert;

class CourseSort extends AbstractSort
{
    #[Assert\Choice(['id', 'name', 'duration', 'institution'])]
    public $sort = 'id';
}
