<?php

declare(strict_types=1);

namespace App\Sort;

use Symfony\Component\Validator\Constraints as Assert;

class SubjectSort extends AbstractSort
{
    #[Assert\Choice(['id', 'name', 'code', 'course'])]
    public $sort = 'id';
}
