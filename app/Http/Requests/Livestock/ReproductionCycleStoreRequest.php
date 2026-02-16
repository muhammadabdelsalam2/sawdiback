<?php

namespace App\Http\Requests\Livestock;

use Illuminate\Validation\Rule;

class ReproductionCycleStoreRequest extends BaseLivestockRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'female_animal_id' => ['required', 'integer', Rule::exists('livestock_animals', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId)->where('gender', 'female'))],
            'heat_date' => ['nullable', 'date'],
            'insemination_type' => ['nullable', Rule::in(['natural', 'artificial'])],
            'notes' => ['nullable', 'string'],
        ];
    }
}
