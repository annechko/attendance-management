<?php

declare(strict_types=1);

namespace App\Sort;

use Symfony\Component\Validator\Constraints as Assert;

class IntakeSort extends AbstractSort
{
    #[Assert\Choice(['id', 'name', 'start', 'finish', 'course', 'institution'])]
    public $sort = 'id';
}
