<?php

namespace App\Http\Requests\Livestock;

use Illuminate\Validation\Rule;

class VaccineBatchStoreRequest extends BaseLivestockRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'vaccine_id' => ['required', 'integer', Rule::exists('vaccines', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'batch_number' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'expiry_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
