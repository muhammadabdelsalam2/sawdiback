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
    $merge = [];

    // فقط لو مش موجود أصلاً
    if (!$this->has('label')) {
        $merge['label'] = $this->input('title', '');
    }

    if (!$this->has('address_line_1')) {
        $merge['address_line_1'] = $this->input('street_address', '');
    }

    // باقي القيم الافتراضية بس لو مش موجودة
    $defaults = [
        'recipient_name' => '',
        'phone'          => '',
        'address_line_2' => '',
        'building'       => '',
        'floor'          => '',
        'apartment'      => '',
        'landmark'       => '',
        'city'           => '',
        'country'        => '',
        'postal_code'    => '',
        'notes'          => '',
        'latitude'       => 0,
        'longitude'      => 0,
        'is_default'     => false,
    ];

    foreach ($defaults as $key => $default) {
        if (!$this->has($key)) {
            $merge[$key] = $key === 'is_default'
                ? $this->boolean($key)
                : $this->input($key, $default);
        }
    }

    if (!empty($merge)) {
        $this->merge($merge);
    }
}
}
