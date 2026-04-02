<?php

namespace App\Http\Requests\Customer\Procurement;

use Illuminate\Validation\Rule;

class QuotationStoreRequest extends BaseProcurementRequest
{
    public function rules(): array
    {
        return [
            'rfq_id' => ['required', 'string', 'exists:rfqs,id'],
            'supplier_id' => ['required', 'string', 'exists:suppliers,id'],
            'status' => ['required', Rule::in(['submitted', 'selected', 'rejected'])],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:inventory_products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => __('procurement.validation.messages.required'),
            'string' => __('procurement.validation.messages.string'),
            'in' => __('procurement.validation.messages.in'),
            'exists' => __('procurement.validation.messages.exists'),
            'array' => __('procurement.validation.messages.array'),
            'numeric' => __('procurement.validation.messages.numeric'),
            'min' => __('procurement.validation.messages.min'),
        ];
    }

    public function attributes(): array
    {
        return [
            'rfq_id' => __('procurement.validation.attributes.rfq_id'),
            'supplier_id' => __('procurement.validation.attributes.supplier_id'),
            'status' => __('procurement.validation.attributes.status'),
            'items' => __('procurement.validation.attributes.items'),
            'items.*.product_id' => __('procurement.validation.attributes.product_id'),
            'items.*.quantity' => __('procurement.validation.attributes.quantity'),
            'items.*.unit_price' => __('procurement.validation.attributes.unit_price'),
        ];
    }
}
