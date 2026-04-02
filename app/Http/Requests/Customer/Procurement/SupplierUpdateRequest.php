<?php

namespace App\Http\Requests\Customer\Procurement;

class SupplierUpdateRequest extends BaseProcurementRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:190'],
            'email' => ['nullable', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:190'],
            'address' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => __('procurement.validation.messages.required'),
            'string' => __('procurement.validation.messages.string'),
            'max' => __('procurement.validation.messages.max'),
            'email' => __('procurement.validation.messages.email'),
            'boolean' => __('procurement.validation.messages.boolean'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('procurement.validation.attributes.name'),
            'email' => __('procurement.validation.attributes.email'),
            'phone' => __('procurement.validation.attributes.phone'),
            'address' => __('procurement.validation.attributes.address'),
            'is_active' => __('procurement.validation.attributes.is_active'),
        ];
    }
}
