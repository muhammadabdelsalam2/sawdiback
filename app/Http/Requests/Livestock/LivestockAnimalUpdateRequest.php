<?php

namespace App\Http\Requests\Livestock;

use App\Models\LivestockAnimal;
use Illuminate\Validation\Rule;

class LivestockAnimalUpdateRequest extends BaseLivestockRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();
        /** @var LivestockAnimal|null $animal */
        $animal = $this->route('animal');

        return [
            'tag_number' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('livestock_animals', 'tag_number')
                    ->where(fn ($q) => $q->where('tenant_id', $tenantId))
                    ->ignore($animal?->id),
            ],
            'species_id' => ['sometimes', 'integer', Rule::exists('animal_species', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'breed_id' => ['nullable', 'integer', Rule::exists('animal_breeds', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'gender' => ['sometimes', Rule::in(['male', 'female'])],
            'birth_date' => ['nullable', 'date'],
            'source_type' => ['sometimes', Rule::in(['born', 'purchased'])],
            'purchase_date' => ['nullable', 'date'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['sometimes', Rule::in(['active', 'sold', 'dead', 'slaughtered'])],
            'health_status' => ['sometimes', Rule::in(['healthy', 'under_treatment', 'quarantined'])],
            'mother_id' => ['nullable', 'integer', Rule::exists('livestock_animals', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'father_id' => ['nullable', 'integer', Rule::exists('livestock_animals', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'notes' => ['nullable', 'string'],
        ];
    }
}
