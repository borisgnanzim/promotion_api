<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_at' => ['nullable', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'is_active' => ['nullable', 'boolean'],
            'assign_by' => ['nullable', 'string', 'exists:users,ref'],
            'update_by' => ['nullable', 'string', 'exists:users,ref'],
            'disabled_at' => ['nullable', 'date'],
            'user_ref' => ['sometimes', 'required', 'string', 'exists:users,ref'],
            'role_ref' => ['sometimes', 'required', 'string', 'exists:roles,ref'],
        ];
    }
}
