<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'parent_ref' => $this->parent_ref,
            'parent' => $this->whenLoaded('parent'),
            'childrens' => $this->whenLoaded('childrens'),
            'items_count' => $this->whenCounted('items'),
            //'items' => $this->whenLoaded('items'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
