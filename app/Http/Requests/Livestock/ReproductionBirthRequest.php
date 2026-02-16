<?php

namespace App\Http\Requests\Livestock;

use Illuminate\Validation\Rule;

class ReproductionBirthRequest extends BaseLivestockRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'birth_date' => ['required', 'date'],
            'complications' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'offspring' => ['required', 'array', 'min:1'],
            'offspring.*.tag_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('livestock_animals', 'tag_number')->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            'offspring.*.species_id' => ['required', 'integer', Rule::exists('animal_species', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'offspring.*.breed_id' => ['nullable', 'integer', Rule::exists('animal_breeds', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'offspring.*.gender' => ['required', Rule::in(['male', 'female'])],
            'offspring.*.birth_weight' => ['nullable', 'numeric', 'min:0'],
            'offspring.*.notes' => ['nullable', 'string'],
        ];
    }
}
