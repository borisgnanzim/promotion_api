<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Table(key: 'ref',  keyType: 'string')]
#[Fillable(['title', 'description', 'path', 'item_ref', 'item_type'])]

class Image extends Model
{
    /** @use HasFactory<\Database\Factories\ImageFactory> */
    use HasFactory, HasUuids;

    //protected $fillable = ['title', 'description', 'path', 'item_ref', 'item_type'];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_ref', 'ref');
    }
}
