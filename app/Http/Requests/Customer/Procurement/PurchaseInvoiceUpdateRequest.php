<?php

namespace App\Http\Requests\Customer\Procurement;

use Illuminate\Validation\Rule;

class PurchaseInvoiceUpdateRequest extends BaseProcurementRequest
{
    public function rules(): array
    {
        $invoiceId = $this->route('invoice')?->id;

        return [
            'number' => ['nullable', 'string', 'max:50', Rule::unique('invoices', 'number')->ignore($invoiceId)],
            'supplier_id' => ['required', 'string', 'exists:suppliers,id'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'purchase_order_id' => ['nullable', 'string', 'exists:purchase_orders,id'],
            'goods_receipt_id' => ['nullable', 'string', 'exists:goods_receipts,id'],
            'status' => ['required', Rule::in(['draft', 'posted', 'paid', 'cancelled'])],
            'invoice_date' => ['required', 'date'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
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
            'date' => __('procurement.validation.messages.date'),
            'numeric' => __('procurement.validation.messages.numeric'),
            'min' => __('procurement.validation.messages.min'),
        ];
    }

    public function attributes(): array
    {
        return [
            'number' => __('procurement.validation.attributes.number'),
            'supplier_id' => __('procurement.validation.attributes.supplier_id'),
            'department_id' => __('procurement.validation.attributes.department_id'),
            'purchase_order_id' => __('procurement.validation.attributes.purchase_order_id'),
            'goods_receipt_id' => __('procurement.validation.attributes.goods_receipt_id'),
            'status' => __('procurement.validation.attributes.status'),
            'invoice_date' => __('procurement.validation.attributes.invoice_date'),
            'subtotal' => __('procurement.validation.attributes.subtotal'),
            'tax' => __('procurement.validation.attributes.tax'),
            'discount' => __('procurement.validation.attributes.discount'),
            'notes' => __('procurement.validation.attributes.notes'),
        ];
    }
}
