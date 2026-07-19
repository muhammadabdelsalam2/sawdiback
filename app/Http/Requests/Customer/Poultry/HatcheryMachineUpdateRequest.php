<?php

namespace App\Http\Requests\Customer\Poultry;

use Illuminate\Validation\Rule;

class HatcheryMachineUpdateRequest extends BasePoultryRequest
{
    public function rules(): array
    {
        $machine = $this->route('hatchery_machine');

        return [
            'machine_number' => ['required', 'string', 'max:100', Rule::unique('poultry_hatchery_machines', 'machine_number')->ignore($machine?->id)->where(fn ($q) => $q->where('tenant_id', $this->tenantId()))],
            'capacity' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
