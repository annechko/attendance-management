<?php

declare(strict_types=1);

namespace App\Filter;

use Symfony\Component\Validator\Constraints as Assert;

class InstitutionFilter
{
    public $id;
    public $name;
    public $location;

    #[Assert\Choice(['id', 'name', 'location'])]
    public $sort = 'id';

    #[Assert\Choice(['desc', 'asc'])]
    public $direction = 'asc';

    #[Assert\Range(min: 1, max: 1000)]
    #[Assert\Type('integer')]
    public $page = 1;
}
