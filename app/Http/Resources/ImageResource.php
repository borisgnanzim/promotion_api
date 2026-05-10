<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'path' => $this->path,
            'item_ref' => $this->item_ref,
            'item_type' => $this->item_type,
            'item' => $this->whenLoaded('item'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
