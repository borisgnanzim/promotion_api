<?php

namespace App\Http\Filters;

class ItemFilter extends QueryFilter
{
    public function ref($value)
    {
        return $this->builder->where('ref', 'like', '%' . $value . '%');
    }

    public function name($value)
    {
        return $this->builder->where('name', 'like', '%' . $value . '%');
    }

    public function status($value)
    {
        return $this->builder->where('status', $value);
    }

    public function category_ref($value)
    {
        return $this->builder->where('category_ref', $value);
    }

    public function promotion_ref($value)
    {
        return $this->builder->where('promotion_ref', $value);
    }

    public function price($value)
    {
        $range = explode(',', $value);
        if (count($range) === 2) {
            return $this->builder->whereBetween('price', [$range[0], $range[1]]);
        }
        return $this->builder->where('price', '<=', $value);
    }

    public function stock($value)
    {
        return $this->builder->where('stock', '>=', $value);
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