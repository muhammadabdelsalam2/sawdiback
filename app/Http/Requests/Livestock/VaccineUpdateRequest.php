<?php

namespace App\Http\Requests\Livestock;

use App\Models\Vaccine;
use Illuminate\Validation\Rule;

class VaccineUpdateRequest extends BaseLivestockRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();
        /** @var Vaccine|null $vaccine */
        $vaccine = $this->route('vaccine');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('vaccines', 'name')
                    ->where(fn ($q) => $q->where('tenant_id', $tenantId))
                    ->ignore($vaccine?->id),
            ],
            'default_interval_days' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
