<?php

namespace App\Http\Requests\Livestock;

use Illuminate\Validation\Rule;

class LivestockAnimalStoreRequest extends BaseLivestockRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'tag_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('livestock_animals', 'tag_number')->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            'species_id' => ['required', 'integer', Rule::exists('animal_species', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'breed_id' => ['nullable', 'integer', Rule::exists('animal_breeds', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'birth_date' => ['nullable', 'date'],
            'source_type' => ['required', Rule::in(['born', 'purchased'])],
            'purchase_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in(['active', 'sold', 'dead', 'slaughtered'])],
            'health_status' => ['nullable', Rule::in(['healthy', 'under_treatment', 'quarantined'])],
            'mother_id' => ['nullable', 'integer', Rule::exists('livestock_animals', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'father_id' => ['nullable', 'integer', Rule::exists('livestock_animals', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'notes' => ['nullable', 'string'],
            'capture_birth_event' => ['nullable', 'boolean'],
            'reproduction_cycle_id' => ['nullable', 'integer', Rule::exists('reproduction_cycles', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'initial_weight' => ['nullable', 'numeric', 'min:0'],
            'initial_weight_recorded_at' => ['nullable', 'date'],
        ];
    }
}
