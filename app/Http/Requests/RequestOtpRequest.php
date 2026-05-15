<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RequestOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['nullable', 'email', 'required_without:phone_number', 'exists:users,email'],
            'phone_number' => ['nullable', 'string', 'required_without:email', 'exists:users,phone_number'],
        ];
    }
}
