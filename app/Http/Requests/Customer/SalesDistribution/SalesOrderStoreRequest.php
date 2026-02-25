<?php

namespace App\Http\Requests\Customer\SalesDistribution;

use Illuminate\Foundation\Http\FormRequest;

class SalesOrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_no' => ['required', 'string', 'max:100'],
            'customer_id' => ['required', 'integer', 'exists:sales_customers,id'],
            'contract_id' => ['nullable', 'integer', 'exists:sales_contracts,id'],
            'order_date' => ['required', 'date'],
            'status' => ['required', 'in:draft,confirmed,fulfilled,cancelled'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'min:1'],
            'items.*.qty' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
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
            'numeric' => __('sales_dist.validation.messages.numeric'),
            'min' => __('sales_dist.validation.messages.min'),
            'array' => __('sales_dist.validation.messages.array'),
            'integer' => __('sales_dist.validation.messages.integer'),
        ];
    }

    public function attributes(): array
    {
        return [
            'order_no' => __('sales_dist.validation.attributes.order_no'),
            'customer_id' => __('sales_dist.validation.attributes.customer_id'),
            'contract_id' => __('sales_dist.validation.attributes.contract_id'),
            'order_date' => __('sales_dist.validation.attributes.order_date'),
            'status' => __('sales_dist.validation.attributes.status'),
            'notes' => __('sales_dist.validation.attributes.notes'),
            'items' => __('sales_dist.validation.attributes.items'),
            'items.*.product_id' => __('sales_dist.validation.attributes.item_product_id'),
            'items.*.qty' => __('sales_dist.validation.attributes.item_qty'),
            'items.*.unit_price' => __('sales_dist.validation.attributes.item_unit_price'),
            'items.*.discount' => __('sales_dist.validation.attributes.item_discount'),
        ];
    }
}
