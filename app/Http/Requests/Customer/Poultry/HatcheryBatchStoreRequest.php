<?php

namespace App\Http\Requests\Customer\Poultry;

use Illuminate\Validation\Rule;

class HatcheryBatchStoreRequest extends BasePoultryRequest
{
    public function rules(): array
    {
        return [
            'hatchery_machine_id' => ['required', 'integer', Rule::exists('poultry_hatchery_machines', 'id')->where(fn ($q) => $q->where('tenant_id', $this->tenantId()))],
            'batch_number' => ['required', 'string', 'max:100', Rule::unique('poultry_hatchery_batches', 'batch_number')->where(fn ($q) => $q->where('tenant_id', $this->tenantId()))],
            'loaded_at' => ['required', 'date'],
            'expected_hatch_at' => ['required', 'date'],
            'actual_hatch_at' => ['nullable', 'date'],
            'eggs_loaded' => ['required', 'integer', 'min:1'],
            'chicks_produced' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
