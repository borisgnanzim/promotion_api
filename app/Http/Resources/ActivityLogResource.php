<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'action' => $this->action,
            'target_type' => $this->target_type,
            'target_ref' => $this->target_ref,
            'description' => $this->description,
            'user' => $this->user ? [
                'ref' => $this->user->ref,
                'name' => $this->user->name,
                'matricule' => $this->user->matricule,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
            ] : null,
            'role' => $this->role,
            'changes' => $this->changes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}