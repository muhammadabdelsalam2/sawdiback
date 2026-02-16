<?php

namespace App\Http\Requests\Livestock;

use Illuminate\Validation\Rule;

class MilkProductionStoreRequest extends BaseLivestockRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'animal_id' => ['required', 'integer', Rule::exists('livestock_animals', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'production_date' => ['required', 'date'],
            'quantity_liters' => ['required', 'numeric', 'min:0.01'],
            'fat_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'quality_grade' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
