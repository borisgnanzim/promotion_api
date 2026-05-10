<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserRoleResource extends JsonResource
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
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'is_active' => $this->is_active,
            'assign_by' => $this->assign_by,
            'update_by' => $this->update_by,
            'disabled_at' => $this->disabled_at,
            'user_ref' => $this->user_ref,
            'role_ref' => $this->role_ref,
            'user' => $this->whenLoaded('user'),
            'role' => $this->whenLoaded('role'),
            'assignBy' => $this->whenLoaded('assignBy'),
            'updateBy' => $this->whenLoaded('updateBy'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
