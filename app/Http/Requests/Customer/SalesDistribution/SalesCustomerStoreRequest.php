<?php

namespace App\Http\Requests\Customer\SalesDistribution;

use Illuminate\Foundation\Http\FormRequest;

class SalesCustomerStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:190'],
            'type' => ['required', 'in:trader,factory,shop'],
            'phones' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'tax_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => __('sales_dist.validation.messages.required'),
            'string' => __('sales_dist.validation.messages.string'),
            'max' => __('sales_dist.validation.messages.max'),
            'in' => __('sales_dist.validation.messages.in'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('sales_dist.validation.attributes.name'),
            'type' => __('sales_dist.validation.attributes.type'),
            'phones' => __('sales_dist.validation.attributes.phones'),
            'address' => __('sales_dist.validation.attributes.address'),
            'tax_number' => __('sales_dist.validation.attributes.tax_number'),
            'notes' => __('sales_dist.validation.attributes.notes'),
            'status' => __('sales_dist.validation.attributes.status'),
        ];
    }
}
