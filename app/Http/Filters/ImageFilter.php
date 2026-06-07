<?php

namespace App\Http\Filters;

class ImageFilter extends QueryFilter
{
    public function ref($value)
    {
        return $this->builder->where('ref', 'like', '%' . $value . '%');
    }

    public function title($value)
    {
        return $this->builder->where('title', 'like', '%' . $value . '%');
    }

    public function item_ref($value)
    {
        return $this->builder->where('item_ref', $value);
    }

    public function item_type($value)
    {
        return $this->builder->where('item_type', 'like', '%' . $value . '%');
    }

    public function created_at($value)
    {
        $dateRange = explode(',', $value);
        if (count($dateRange) === 2) return $this->builder->whereBetween('created_at', [$dateRange[0], $dateRange[1]]);
        return $this->builder->whereDate('created_at', $value);
    }
}