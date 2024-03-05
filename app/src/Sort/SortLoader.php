<?php

namespace App\Sort;

use Symfony\Component\HttpFoundation\Request;

class SortLoader
{
    public function load(
        AbstractSort $sort,
        Request $request
    ): void {
        $sort->sort = $request->query->get('sort');
        $sort->direction = $request->query->get('direction');
        $sort->page = (int) $request->query->get('page', 1);
    }
}