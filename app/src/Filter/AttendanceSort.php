<?php

declare(strict_types=1);

namespace App\Filter;

use Symfony\Component\Validator\Constraints as Assert;

class AttendanceSort extends AbstractSort
{
    #[Assert\Choice(['id'])]
    public $sort = 'id';
}
