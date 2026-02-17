<?php

namespace App\Http\Requests\Livestock;

use Illuminate\Validation\Rule;

class VaccinationStoreRequest extends BaseLivestockRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'animal_id' => ['required', 'integer', Rule::exists('livestock_animals', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'vaccine_id' => ['required', 'integer', Rule::exists('vaccines', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'dose_number' => ['required', 'integer', 'min:1'],
            'vaccination_date' => ['required', 'date'],
            'next_due_date' => ['nullable', 'date', 'after_or_equal:vaccination_date'],
            'administered_by_employee_id' => ['nullable', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
