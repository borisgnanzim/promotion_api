<?php

namespace App\Http\Filters;

class UserFilter extends QueryFilter
{
    public function ref($value)
    {
        return $this->builder->where('ref', 'like', '%' . $value . '%');
    }

    public function name($value)
    {
        return $this->builder->where('name', 'like', '%' . $value . '%');
    }

    public function email($value)
    {
        return $this->builder->where('email', 'like', '%' . $value . '%');
    }

    public function phone_number($value)
    {
        return $this->builder->where('phone_number', 'like', '%' . $value . '%');
    }

    public function is_active($value)
    {
        return $this->builder->where('is_active', $value);
    }

    public function store_ref($value)
    {
        return $this->builder->where('store_ref', 'like', '%' . $value . '%');
    }

    public function created_at($value)
    {
        $dateRange = explode(',', $value);
        if (count($dateRange) === 2) {
            return $this->builder->whereBetween('created_at', [$dateRange[0], $dateRange[1]]);
        }
        return $this->builder->whereDate('created_at', $value);
    }

    public function updated_at($value)
    {
        $dateRange = explode(',', $value);
        if (count($dateRange) === 2) {
            return $this->builder->whereBetween('updated_at', [$dateRange[0], $dateRange[1]]);
        }
        return $this->builder->whereDate('updated_at', $value);
    }
}