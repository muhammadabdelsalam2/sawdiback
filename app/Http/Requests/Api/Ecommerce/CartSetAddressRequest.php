<?php

namespace App\Http\Requests\Api\Ecommerce;

use Illuminate\Foundation\Http\FormRequest;

class CartSetAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'address_id' => ['required', 'integer', 'exists:user_addresses,id'],
        ];
    }
}
