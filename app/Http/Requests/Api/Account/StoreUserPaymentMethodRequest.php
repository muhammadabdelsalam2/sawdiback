<?php

namespace App\Http\Requests\Api\Account;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserPaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brand' => ['required', 'string', 'max:50'],
            'last_four' => ['required', 'string', 'regex:/^\d{4}$/'],
            'expiry_month' => ['required', 'integer', 'between:1,12'],
            'expiry_year' => ['required', 'integer', 'min:2000', 'max:9999'],
            'holder_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'gateway' => ['sometimes', 'nullable', 'string', 'max:100'],
            'gateway_reference' => ['sometimes', 'nullable', 'string', 'max:191'],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'last_four.regex' => 'The last_four field must contain exactly 4 digits.',
        ];
    }
}
