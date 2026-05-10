<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'ref' => $this->ref,
            'name' => $this->name,
            'description' => $this->description,
            'mini_description' => $this->mini_description,
            'price' => $this->price,
            'stock' => $this->stock,
            'limit_threshold' => $this->limit_threshold,
            'out_of_stock_threshold' => $this->out_of_stock_threshold,
            'status' => $this->status,
            'slug' => $this->slug,
            'search_slug' => $this->search_slug,
            'search_slug_metaphone' => $this->search_slug_metaphone,
            'category_ref' => $this->category_ref,
            'promotion_ref' => $this->promotion_ref,
            'promotion_pourcentage' => $this->promotion_pourcentage,
            'promotion_discount' => $this->promotion_discount,
            'category' => $this->whenLoaded('category'),
            'promotion' => $this->whenLoaded('promotion'),
            'images' => $this->whenLoaded('images'),
            'images_count' => $this->whenCounted('images'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
