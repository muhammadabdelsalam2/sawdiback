<?php

namespace App\Http\Requests\Customer\Procurement;

use Illuminate\Validation\Rule;

class RfqUpdateRequest extends BaseProcurementRequest
{
    public function rules(): array
    {
        $rfqId = $this->route('rfq')?->id;

        return [
            'code' => ['nullable', 'string', 'max:50', Rule::unique('rfqs', 'code')->ignore($rfqId)],
            'purchase_requisition_id' => ['required', 'string', 'exists:purchase_requisitions,id'],
            'status' => ['required', Rule::in(['open', 'sent', 'closed', 'awarded'])],
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
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => __('procurement.validation.attributes.code'),
            'purchase_requisition_id' => __('procurement.validation.attributes.purchase_requisition_id'),
            'status' => __('procurement.validation.attributes.status'),
        ];
    }
}
