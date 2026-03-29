<?php

namespace App\Http\Requests\Api\Ecommerce;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutPlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'address_id' => ['sometimes', 'integer', 'exists:user_addresses,id'],
            'payment_method' => ['required', 'string', 'in:card,apple_pay,wallet,cash'],
            'payment_method_id' => ['sometimes', 'nullable', 'integer', 'exists:user_payment_methods,id'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }
}
