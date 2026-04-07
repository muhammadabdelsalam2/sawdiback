<?php

namespace App\Http\Requests\Api\Ecommerce;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationAddressRequest extends FormRequest
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
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'landmark' => ['sometimes', 'nullable', 'string', 'max:255'],
            'building' => ['sometimes', 'nullable', 'string', 'max:100'],
            'floor' => ['sometimes', 'nullable', 'string', 'max:100'],
            'apartment' => ['sometimes', 'nullable', 'string', 'max:100'],
            'city' => ['sometimes', 'nullable', 'string', 'max:150'],
            'country' => ['sometimes', 'nullable', 'string', 'max:150'],
            'postal_code' => ['sometimes', 'nullable', 'string', 'max:50'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'recipient_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }

   protected function prepareForValidation(): void
{
    $this->merge([
        'title' => $this->input('title', ''),
        'label' => $this->input('label', $this->input('title', '')),
        'address_line_1' => $this->input('street_address', ''),

        'recipient_name' => $this->input('recipient_name', ''),
        'phone' => $this->input('phone', ''),
        'address_line_2' => $this->input('address_line_2', ''),
        'building' => $this->input('building', ''),
        'floor' => $this->input('floor', ''),
        'apartment' => $this->input('apartment', ''),
        'landmark' => $this->input('landmark', ''),
        'city' => $this->input('city', ''),
        'country' => $this->input('country', ''),
        'postal_code' => $this->input('postal_code', ''),
        'notes' => $this->input('notes', ''),

        'latitude' => $this->input('latitude', 0),
        'longitude' => $this->input('longitude', 0),

        'is_default' => $this->boolean('is_default'),
    ]);
}
}
