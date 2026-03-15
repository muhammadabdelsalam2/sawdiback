<?php

namespace App\Http\Requests\Api\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class TopUpWalletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('sanctum')->check() || auth()->check();
    }

    public function rules(): array
    {
        $currentYear = (int) date('Y');

        return [
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_channel' => ['required', 'string', 'in:card,apple_pay'],
            'payment_method_id' => ['nullable', 'integer', 'exists:user_payment_methods,id'],

            'save_payment_method' => ['nullable', 'boolean'],
            'make_default' => ['nullable', 'boolean'],

            'brand' => ['nullable', 'string', 'max:50'],
            'holder_name' => ['nullable', 'string', 'max:255'],
            'card_number' => ['nullable', 'string', 'min:12', 'max:19'],
            'expiry_month' => ['nullable', 'integer', 'between:1,12'],
            'expiry_year' => ['nullable', 'integer', 'min:' . $currentYear, 'max:' . ($currentYear + 20)],
            'cvv' => ['nullable', 'digits_between:3,4'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $channel = $this->input('payment_channel');
            $paymentMethodId = $this->input('payment_method_id');

            if ($channel === 'card' && empty($paymentMethodId)) {
                if (!$this->filled('card_number')) {
                    $validator->errors()->add('card_number', 'The card number field is required when no saved payment method is selected.');
                }

                if (!$this->filled('expiry_month')) {
                    $validator->errors()->add('expiry_month', 'The expiry month field is required when no saved payment method is selected.');
                }

                if (!$this->filled('expiry_year')) {
                    $validator->errors()->add('expiry_year', 'The expiry year field is required when no saved payment method is selected.');
                }
            }

            if (!empty($paymentMethodId)) {
                $existsForUser = $this->user()
                    ->paymentMethods()
                    ->whereKey($paymentMethodId)
                    ->exists();

                if (!$existsForUser) {
                    $validator->errors()->add('payment_method_id', 'The selected payment method does not belong to the authenticated user.');
                }
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'save_payment_method' => filter_var($this->input('save_payment_method', false), FILTER_VALIDATE_BOOLEAN),
            'make_default' => filter_var($this->input('make_default', false), FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
