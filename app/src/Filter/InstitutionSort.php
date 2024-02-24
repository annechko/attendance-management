<?php

declare(strict_types=1);

namespace App\Filter;

use Symfony\Component\Validator\Constraints as Assert;

class InstitutionSort extends AbstractSort
{
    #[Assert\Choice(['id', 'name', 'location'])]
    public $sort = 'id';
}
