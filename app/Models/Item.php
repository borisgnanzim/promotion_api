<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description', 'mini_description', 'price', 'stock', 'limit_threshold', 'out_of_stock_threshold', 'status', 'slug', 'search_slug', 'search_slug_metaphone', 'promotion_pourcentage', 'promotion_discount', 'category_ref'])]
#[Table(key: 'ref',  keyType: 'string')]

class Item extends Model
{
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory, HasUuids;

    //protected $fillable = ['name', 'description', 'mini_description', 'price', 'stock', 'limit_threshold', 'out_of_stock_threshold', 'status', 'slug', 'search_slug', 'search_slug_metaphone', 'promotion_pourcentage', 'promotion_discount', 'category_id'];

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'stock' => 'integer',
            'limit_threshold' => 'integer',
            'out_of_stock_threshold' => 'integer',
            'promotion_pourcentage' => 'decimal:2',
            'promotion_discount' => 'float',
        ];
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_ref', 'ref');
    }

}
