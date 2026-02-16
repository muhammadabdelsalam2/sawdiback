<?php

namespace App\Http\Requests\Livestock;

use Illuminate\Validation\Rule;

class AnimalSpeciesStoreRequest extends BaseLivestockRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('animal_species', 'code')->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
