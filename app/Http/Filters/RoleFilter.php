<?php

namespace App\Http\Filters;

class RoleFilter extends QueryFilter
{
    public function ref($value)
    {
        return $this->builder->where('ref', 'like', '%' . $value . '%');
    }

    public function name($value)
    {
        return $this->builder->where('name', 'like', '%' . $value . '%');
    }

    public function assignable($value)
    {
        // Convertit 'true'/'false' ou 1/0 en boolean
        $boolValue = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        return $this->builder->where('assignable', $boolValue);
    }
}