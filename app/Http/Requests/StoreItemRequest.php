<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'mini_description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'limit_threshold' => ['nullable', 'integer', 'min:0'],
            'out_of_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:disponible,limite,rupture'],
            'slug' => ['nullable', 'string', 'max:255'],
            'search_slug' => ['nullable', 'string', 'max:255'],
            'search_slug_metaphone' => ['nullable', 'string', 'max:255'],
            'category_ref' => ['required', 'string', 'exists:categories,ref'],
            'promotion_ref' => ['required', 'string', 'exists:promotions,ref'],
            'promotion_pourcentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'promotion_discount' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
