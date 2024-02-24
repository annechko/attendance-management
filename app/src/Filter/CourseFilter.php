<?php

declare(strict_types=1);

namespace App\Filter;

use Symfony\Component\Validator\Constraints as Assert;

class CourseFilter
{
    // can filter by fields:
    public $id;
    public $name;
    public $duration;
}
