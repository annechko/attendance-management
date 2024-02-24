<?php

namespace App\Filter;

use Symfony\Component\HttpFoundation\Request;

class SortLoader
{
    public function load(
        AbstractSort $sort,
        Request $request
    ): void {
        $sort->sort = $request->query->get('sort', 'id');
        $sort->direction = $request->query->get('direction', 'asc');
        $sort->page = (int) $request->query->get('page', 1);
    }
}