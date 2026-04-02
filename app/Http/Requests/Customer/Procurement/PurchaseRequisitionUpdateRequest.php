<?php

namespace App\Http\Requests\Customer\Procurement;

use Illuminate\Validation\Rule;

class PurchaseRequisitionUpdateRequest extends BaseProcurementRequest
{
    public function rules(): array
    {
        $requisitionId = $this->route('requisition')?->id;

        return [
            'code' => ['nullable', 'string', 'max:50', Rule::unique('purchase_requisitions', 'code')->ignore($requisitionId)],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'requested_by' => ['required', 'integer', 'exists:users,id'],
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected', 'converted_to_po'])],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:inventory_products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.estimated_price' => ['nullable', 'numeric', 'min:0'],
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
            'code' => __('procurement.validation.attributes.code'),
            'department_id' => __('procurement.validation.attributes.department_id'),
            'requested_by' => __('procurement.validation.attributes.requested_by'),
            'status' => __('procurement.validation.attributes.status'),
            'notes' => __('procurement.validation.attributes.notes'),
            'items' => __('procurement.validation.attributes.items'),
            'items.*.product_id' => __('procurement.validation.attributes.product_id'),
            'items.*.quantity' => __('procurement.validation.attributes.quantity'),
            'items.*.estimated_price' => __('procurement.validation.attributes.estimated_price'),
        ];
    }
}
