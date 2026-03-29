<?php

namespace App\Http\Requests\Api\Account;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label' => ['sometimes', 'nullable', 'string', 'max:100'],
            'title' => ['sometimes', 'nullable', 'string', 'max:100'],
            'type' => ['sometimes', 'nullable', 'string', 'max:50'],
            'recipient_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'street_address' => ['sometimes', 'nullable', 'string', 'max:255'],
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
            'is_default' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $payload = [];

        if ($this->filled('title') && !$this->filled('label')) {
            $payload['label'] = $this->input('title');
        }

        if ($this->filled('street_address') && !$this->filled('address_line_1')) {
            $payload['address_line_1'] = $this->input('street_address');
        }

        if (!empty($payload)) {
            $this->merge($payload);
        }
    }
}
