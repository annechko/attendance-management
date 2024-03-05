<?php

declare(strict_types=1);

namespace App\Sort;

use Symfony\Component\Validator\Constraints as Assert;

class PeriodSort extends AbstractSort
{
    #[Assert\Choice(['id', 'name', 'start', 'finish', 'course', 'intake', 'subjectsCount'])]
    public $sort = 'id';
}
