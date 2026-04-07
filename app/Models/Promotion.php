<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    /** @use HasFactory<\Database\Factories\PromotionFactory> */
    use HasFactory;

    protected $fillable = ['title', 'description', 'pourcentage', 'discount', 'max_discount', 'start_at', 'end_at', 'is_active'];

    protected function casts(): array
    {
        return [
            'pourcentage' => 'decimal:2',
            'discount' => 'float',
            'max_discount' => 'float',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }
}
