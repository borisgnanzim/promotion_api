<?php

namespace App\Http\Filters;

class PromotionFilter extends QueryFilter
{
    public function ref($value)
    {
        return $this->builder->where('ref', 'like', '%' . $value . '%');
    }

    public function title($value)
    {
        return $this->builder->where('title', 'like', '%' . $value . '%');
    }

    public function is_active($value)
    {
        return $this->builder->where('is_active', $value);
    }

    public function store_ref($value)
    {
        return $this->builder->where('store_ref', 'like', '%' . $value . '%');
    }

    public function start_at($value)
    {
        $dateRange = explode(',', $value);
        if (count($dateRange) === 2) {
            return $this->builder->whereBetween('start_at', [$dateRange[0], $dateRange[1]]);
        }
        return $this->builder->whereDate('start_at', '>=', $value);
    }

    public function end_at($value)
    {
        $dateRange = explode(',', $value);
        if (count($dateRange) === 2) {
            return $this->builder->whereBetween('end_at', [$dateRange[0], $dateRange[1]]);
        }
        return $this->builder->whereDate('end_at', '<=', $value);
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