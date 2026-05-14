<?php

namespace App\Http\Filters;

class CategoryFilter extends QueryFilter
{
    public function ref($value)
    {
        return $this->builder->where('ref', 'like', '%' . $value . '%');
    }

    public function name($value)
    {
        return $this->builder->where('name', 'like', '%' . $value . '%');
    }

    public function description($value)
    {
        return $this->builder->where('description', 'like', '%' . $value . '%');
    }

    public function parent_ref($value)
    {
        return $this->builder->where('parent_ref', 'like', '%' . $value . '%');
    }

    public function created_at($value)
    {
        $dateRange = explode(',', $value);
        return $this->builder->whereBetween('created_at', [$dateRange[0], $dateRange[1]]);
    }

    public function updated_at($value)
    {
        $dateRange = explode(',', $value);
        return $this->builder->whereBetween('updated_at', [$dateRange[0], $dateRange[1]]);
    }
}