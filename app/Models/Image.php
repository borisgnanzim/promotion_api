<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    /** @use HasFactory<\Database\Factories\ImageFactory> */
    use HasFactory;

    protected $fillable = ['title', 'description', 'path', 'item_id', 'item_type'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
