<?php

namespace App\Http\Requests\Customer\Procurement;

use Illuminate\Validation\Rule;

class PurchaseOrderUpdateRequest extends BaseProcurementRequest
{
    public function rules(): array
    {
        $orderId = $this->route('order')?->id;

        return [
            'po_number' => ['nullable', 'string', 'max:50', Rule::unique('purchase_orders', 'po_number')->ignore($orderId)],
            'supplier_id' => ['required', 'string', 'exists:suppliers,id'],
            'rfq_id' => ['nullable', 'string', 'exists:rfqs,id'],
            'quotation_id' => ['nullable', 'string', 'exists:quotations,id'],
            'status' => ['required', Rule::in(['draft', 'confirmed', 'partially_received', 'received', 'closed'])],
            'vat' => ['nullable', 'numeric', 'min:0'],
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
            'po_number' => __('procurement.validation.attributes.po_number'),
            'supplier_id' => __('procurement.validation.attributes.supplier_id'),
            'rfq_id' => __('procurement.validation.attributes.rfq_id'),
            'quotation_id' => __('procurement.validation.attributes.quotation_id'),
            'status' => __('procurement.validation.attributes.status'),
            'vat' => __('procurement.validation.attributes.vat'),
            'items' => __('procurement.validation.attributes.items'),
            'items.*.product_id' => __('procurement.validation.attributes.product_id'),
            'items.*.quantity' => __('procurement.validation.attributes.quantity'),
            'items.*.unit_price' => __('procurement.validation.attributes.unit_price'),
        ];
    }
}
