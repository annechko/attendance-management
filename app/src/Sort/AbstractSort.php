<?php

declare(strict_types=1);

namespace App\Sort;

use Symfony\Component\Validator\Constraints as Assert;

class AbstractSort
{
    public $sort = null;

    #[Assert\Choice(['desc', 'asc'])]
    public $direction = 'asc';

    #[Assert\Range(min: 1, max: 1000)]
    #[Assert\Type('integer')]
    public $page = 1;
}
