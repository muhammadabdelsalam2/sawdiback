<?php

namespace App\Http\Requests\Livestock;

use Illuminate\Validation\Rule;

class HealthRecordStoreRequest extends BaseLivestockRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'animal_id' => ['required', 'integer', Rule::exists('livestock_animals', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'record_type' => ['required', Rule::in(['checkup', 'illness', 'injury'])],
            'diagnosis' => ['required', 'string'],
            'treatment' => ['required', 'string'],
            'vet_employee_id' => ['nullable', 'integer', 'exists:users,id'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'next_followup_date' => ['nullable', 'date'],
            'set_animal_under_treatment' => ['nullable', 'boolean'],
        ];
    }
}
