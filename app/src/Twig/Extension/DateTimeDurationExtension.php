<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DateTimeDurationExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('duration', [$this, 'duration'], ['is_safe' => ['html']]),
        ];
    }

    public function duration(?\DateInterval $interval): string
    {
        $result = "";
        if ($interval === null) {
            return $result;
        }
        if ($interval->y > 0) {
            $result .= $interval->format('%y year(s)');
        }
        if ($interval->m > 0) {
            $result .= $interval->format(' %m month(s)');
        }
        if ($interval->d > 0) {
            $result .= $interval->format(' %d day(s)');
        }
        return $result;
    }
}
