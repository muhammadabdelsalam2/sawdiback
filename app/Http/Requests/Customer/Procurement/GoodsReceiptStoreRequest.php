<?php

namespace App\Http\Requests\Customer\Procurement;

use Illuminate\Validation\Rule;

class GoodsReceiptStoreRequest extends BaseProcurementRequest
{
    public function rules(): array
    {
        return [
            'grn_number' => ['nullable', 'string', 'max:50', Rule::unique('goods_receipts', 'grn_number')],
            'purchase_order_id' => ['required', 'string', 'exists:purchase_orders,id'],
            'received_by' => ['required', 'integer', 'exists:users,id'],
            'status' => ['required', Rule::in(['partial', 'completed'])],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:inventory_products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => __('procurement.validation.messages.required'),
            'string' => __('procurement.validation.messages.string'),
            'max' => __('procurement.validation.messages.max'),
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
            'grn_number' => __('procurement.validation.attributes.grn_number'),
            'purchase_order_id' => __('procurement.validation.attributes.purchase_order_id'),
            'received_by' => __('procurement.validation.attributes.received_by'),
            'status' => __('procurement.validation.attributes.status'),
            'items' => __('procurement.validation.attributes.items'),
            'items.*.product_id' => __('procurement.validation.attributes.product_id'),
            'items.*.quantity' => __('procurement.validation.attributes.quantity'),
        ];
    }
}
