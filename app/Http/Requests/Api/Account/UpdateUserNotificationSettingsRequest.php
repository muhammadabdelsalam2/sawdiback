<?php

namespace App\Http\Requests\Api\Account;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserNotificationSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_updates' => ['sometimes', 'boolean'],
            'sms_updates' => ['sometimes', 'boolean'],
            'promotions_deals' => ['sometimes', 'boolean'],
            'new_products' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (
                !$this->has('order_updates') &&
                !$this->has('sms_updates') &&
                !$this->has('promotions_deals') &&
                !$this->has('new_products')
            ) {
                $validator->errors()->add('notification_settings', 'At least one setting must be provided for update.');
            }
        });
    }
}
