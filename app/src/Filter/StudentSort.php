<?php

declare(strict_types=1);

namespace App\Filter;

use Symfony\Component\Validator\Constraints as Assert;

class StudentSort extends AbstractSort
{
    #[Assert\Choice(['id', 'name', 'institution', 'course', 'email'])]
    public $sort = 'id';
}
