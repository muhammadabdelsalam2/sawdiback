<?php

namespace App\Http\Requests\Api\Ecommerce;

use Illuminate\Foundation\Http\FormRequest;

class StoreManualAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:100'],
            'type' => ['required', 'string', 'max:50'],
            'street_address' => ['required', 'string', 'max:255'],
            'address_line_1' => ['sometimes', 'string', 'max:255'],
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

        $this->merge($payload);
    }
}
