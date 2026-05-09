<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePromotionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'pourcentage' => ['sometimes', 'required', 'numeric', 'min:0', 'max:100'],
            'discount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'max_discount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'start_at' => ['sometimes', 'required', 'date'],
            'end_at' => ['sometimes', 'required', 'date', 'after_or_equal:start_at'],
            'store_ref' => ['sometimes', 'required', 'string', 'exists:stores,ref'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
