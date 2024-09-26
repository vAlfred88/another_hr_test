<?php

namespace App\Filters;

use Closure;

abstract class AbstractFilter
{
    // Every filter should define its key (e.g., 'name', 'email')
    abstract protected function applyFilter($builder);

    public function handle($request, Closure $next)
    {
        if (! request()->has($this->filterName())) {
            return $next($request);
        }

        $builder = $next($request);

        return $this->applyFilter($builder);
    }

    // Helper function to get the filter value
    protected function filterValue()
    {
        return request($this->filterName());
    }

    // Define the name of the filter to be implemented in subclasses
    abstract protected function filterName(): string;
}
