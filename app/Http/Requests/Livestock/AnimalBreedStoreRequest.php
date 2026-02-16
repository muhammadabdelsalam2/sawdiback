<?php

namespace App\Http\Requests\Livestock;

use Illuminate\Validation\Rule;

class AnimalBreedStoreRequest extends BaseLivestockRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'species_id' => ['required', 'integer', Rule::exists('animal_species', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('animal_breeds', 'name')
                    ->where(fn ($q) => $q->where('tenant_id', $tenantId)->where('species_id', $this->input('species_id'))),
            ],
        ];
    }
}
