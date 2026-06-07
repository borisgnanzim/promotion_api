<?php

namespace App\Http\Filters;

class StoreFilter extends QueryFilter
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

    public function address($value)
    {
        return $this->builder->where('address', 'like', '%' . $value . '%');
    }

    public function phone_number($value)
    {
        return $this->builder->where('phone_number', 'like', '%' . $value . '%');
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