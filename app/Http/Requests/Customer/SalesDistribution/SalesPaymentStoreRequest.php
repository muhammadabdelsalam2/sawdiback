<?php

namespace App\Http\Requests\Customer\SalesDistribution;

use Illuminate\Foundation\Http\FormRequest;

class SalesPaymentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_at' => ['required', 'date'],
            'method' => ['required', 'in:cash,bank,other'],
            'reference' => ['nullable', 'string', 'max:190'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => __('sales_dist.validation.messages.required'),
            'string' => __('sales_dist.validation.messages.string'),
            'max' => __('sales_dist.validation.messages.max'),
            'in' => __('sales_dist.validation.messages.in'),
            'date' => __('sales_dist.validation.messages.date'),
            'numeric' => __('sales_dist.validation.messages.numeric'),
            'min' => __('sales_dist.validation.messages.min'),
        ];
    }

    public function attributes(): array
    {
        return [
            'amount' => __('sales_dist.validation.attributes.amount'),
            'paid_at' => __('sales_dist.validation.attributes.paid_at'),
            'method' => __('sales_dist.validation.attributes.method'),
            'reference' => __('sales_dist.validation.attributes.reference'),
            'notes' => __('sales_dist.validation.attributes.notes'),
        ];
    }
}
