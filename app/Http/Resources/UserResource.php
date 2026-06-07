<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'is_active' => $this->is_active,
            'store_ref' => $this->store_ref,
            'email_verified_at' => $this->email_verified_at,
            'roles' => $this->whenLoaded('roles'),
            'store' => $this->whenLoaded('store'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
