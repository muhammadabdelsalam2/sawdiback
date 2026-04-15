<?php

namespace App\Http\Requests\Api\Plus;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePlusSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        $frequencyValues = collect(config('plus.frequency_options', []))
            ->pluck('value')
            ->filter()
            ->values()
            ->all();

        return [
            'address_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('user_addresses', 'id')->where(fn ($query) => $query->where('user_id', $userId)),
            ],
            'payment_method_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('user_payment_methods', 'id')->where(fn ($query) => $query->where('user_id', $userId)),
            ],
            'frequency' => [
                'sometimes',
                'nullable',
                'string',
                Rule::in($frequencyValues),
            ],
            'delivery_days' => ['sometimes', 'nullable', 'array'],
            'delivery_days.*' => ['integer', 'between:0,6'],
            'start_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:today'],
            'category_ids' => ['sometimes', 'nullable', 'array'],
            'category_ids.*' => [
                'integer',
                Rule::exists('categories', 'id')->where(fn ($query) => $query->where('is_active', true)),
            ],
            'auto_renew' => ['sometimes', 'boolean'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $frequency = $this->input('frequency');
            $deliveryDays = $this->input('delivery_days', []);

            if (in_array($frequency, ['weekly', 'custom'], true)) {
                if (!is_array($deliveryDays) || count($deliveryDays) === 0) {
                    $validator->errors()->add('delivery_days', 'At least one delivery day is required for weekly/custom schedules.');
                }
            }
        });
    }
}
