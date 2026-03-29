<?php

namespace App\Http\Requests\Api\Ecommerce;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'nullable', 'string', 'max:100'],
            'type' => ['sometimes', 'nullable', 'string', 'max:50'],
            'street_address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'address_line_1' => ['sometimes', 'nullable', 'string', 'max:255'],
            'address_line_2' => ['sometimes', 'nullable', 'string', 'max:255'],
            'building' => ['sometimes', 'nullable', 'string', 'max:100'],
            'floor' => ['sometimes', 'nullable', 'string', 'max:100'],
            'apartment' => ['sometimes', 'nullable', 'string', 'max:100'],
            'landmark' => ['sometimes', 'nullable', 'string', 'max:255'],
            'city' => ['sometimes', 'nullable', 'string', 'max:150'],
            'country' => ['sometimes', 'nullable', 'string', 'max:150'],
            'postal_code' => ['sometimes', 'nullable', 'string', 'max:50'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'recipient_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $payload = [];

        if ($this->filled('title')) {
            $payload['label'] = $this->input('title');
        }

        if ($this->filled('street_address')) {
            $payload['address_line_1'] = $this->input('street_address');
        }

        if (!empty($payload)) {
            $this->merge($payload);
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (
                !$this->hasAny([
                    'title',
                    'label',
                    'type',
                    'street_address',
                    'address_line_1',
                    'address_line_2',
                    'building',
                    'floor',
                    'apartment',
                    'landmark',
                    'city',
                    'country',
                    'postal_code',
                    'notes',
                    'latitude',
                    'longitude',
                    'recipient_name',
                    'phone',
                    'is_default',
                ])
            ) {
                $validator->errors()->add('address', 'At least one field must be provided for update.');
            }
        });
    }
}
