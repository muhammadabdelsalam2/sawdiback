<?php

namespace App\Http\Requests\Livestock;

use Illuminate\Validation\Rule;

class ReproductionInseminationRequest extends BaseLivestockRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'insemination_date' => ['required', 'date'],
            'insemination_type' => ['required', Rule::in(['natural', 'artificial'])],
            'male_animal_id' => ['nullable', 'integer', Rule::exists('livestock_animals', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId)->where('gender', 'male'))],
            'notes' => ['nullable', 'string'],
        ];
    }
}
