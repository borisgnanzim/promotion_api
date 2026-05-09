<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'path' => ['nullable', 'string', 'max:255'],
            'item_ref' => ['sometimes', 'required', 'string', 'exists:items,ref'],
            'item_type' => ['nullable', 'string', 'max:255'],
        ];
    }
}
