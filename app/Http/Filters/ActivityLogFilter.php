<?php

namespace App\Http\Filters;

class ActivityLogFilter extends QueryFilter
{
    public function include($value)
    {
        return $this->builder->with($value);
    }

    public function user_ref($value)
    {
        return $this->builder->where('user_ref', $value);
    }

    public function role($value)
    {
        return $this->builder->where('role', $value);
    }

    public function action($value)
    {
        return $this->builder->where('action', $value);
    }

    public function target_type($value)
    {
        return $this->builder->where('target_type', $value);
    }

    public function target_ref($value)
    {
        return $this->builder->where('target_ref', $value);
    }

    public function created_at($value)
    {
        return $this->builder->whereDate('created_at', $value);
    }

}