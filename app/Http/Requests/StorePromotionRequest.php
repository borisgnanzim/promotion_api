<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePromotionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'pourcentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'discount' => ['required', 'numeric', 'min:0'],
            'max_discount' => ['required', 'numeric', 'min:0'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after_or_equal:start_at'],
            'store_ref' => ['required', 'string', 'exists:stores,ref'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
