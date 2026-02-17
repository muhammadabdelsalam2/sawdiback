<?php

namespace App\Http\Requests\Livestock;

use App\Models\AnimalSpecies;
use Illuminate\Validation\Rule;

class AnimalSpeciesUpdateRequest extends BaseLivestockRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();
        /** @var AnimalSpecies|null $species */
        $species = $this->route('species');

        return [
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('animal_species', 'code')
                    ->where(fn ($q) => $q->where('tenant_id', $tenantId))
                    ->ignore($species?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
