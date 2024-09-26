<?php

namespace App\Filters;

class NameFilter extends AbstractFilter
{
    // The filter key in the request (e.g., 'name')
    protected function filterName(): string
    {
        return 'name';
    }

    // Apply the filter logic for 'name'
    protected function applyFilter($builder)
    {
        return $builder->where('name', 'like', '%'.$this->filterValue().'%');
    }
}
