<?php

namespace App\Http\Requests\Livestock;

use Illuminate\Validation\Rule;

class WeightLogStoreRequest extends BaseLivestockRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'animal_id' => ['required', 'integer', Rule::exists('livestock_animals', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'recorded_at' => ['required', 'date'],
            'weight' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
