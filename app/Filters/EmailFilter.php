<?php

namespace App\Filters;

class EmailFilter extends AbstractFilter
{
    // The filter key in the request (e.g., 'name')
    protected function filterName(): string
    {
        return 'email';
    }

    // Apply the filter logic for 'email'
    protected function applyFilter($builder)
    {
        return $builder->where('email', 'like', '%'.$this->filterValue().'%');
    }
}
