<?php

namespace App\Http\Requests\Customer\SalesDistribution;

use Illuminate\Foundation\Http\FormRequest;

class SalesContractStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:sales_customers,id'],
            'contract_code' => ['required', 'string', 'max:100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'payment_terms' => ['required', 'string', 'max:255'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive,expired'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => __('sales_dist.validation.messages.required'),
            'string' => __('sales_dist.validation.messages.string'),
            'max' => __('sales_dist.validation.messages.max'),
            'in' => __('sales_dist.validation.messages.in'),
            'exists' => __('sales_dist.validation.messages.exists'),
            'date' => __('sales_dist.validation.messages.date'),
            'after_or_equal' => __('sales_dist.validation.messages.after_or_equal'),
            'numeric' => __('sales_dist.validation.messages.numeric'),
            'min' => __('sales_dist.validation.messages.min'),
            'integer' => __('sales_dist.validation.messages.integer'),
        ];
    }

    public function attributes(): array
    {
        return [
            'customer_id' => __('sales_dist.validation.attributes.customer_id'),
            'contract_code' => __('sales_dist.validation.attributes.contract_code'),
            'start_date' => __('sales_dist.validation.attributes.start_date'),
            'end_date' => __('sales_dist.validation.attributes.end_date'),
            'payment_terms' => __('sales_dist.validation.attributes.payment_terms'),
            'credit_limit' => __('sales_dist.validation.attributes.credit_limit'),
            'notes' => __('sales_dist.validation.attributes.notes'),
            'status' => __('sales_dist.validation.attributes.status'),
        ];
    }
}
