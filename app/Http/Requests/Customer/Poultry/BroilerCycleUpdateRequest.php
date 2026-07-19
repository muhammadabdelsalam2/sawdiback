<?php

namespace App\Http\Requests\Customer\Poultry;

use Illuminate\Validation\Rule;

class BroilerCycleUpdateRequest extends BasePoultryRequest
{
    public function rules(): array
    {
        $cycle = $this->route('broiler_cycle');

        return [
            'pen_id' => ['nullable', 'integer', Rule::exists('farm_pens', 'id')->where(fn ($q) => $q->where('tenant_id', $this->tenantId()))],
            'cycle_number' => ['required', 'string', 'max:100', Rule::unique('poultry_broiler_cycles', 'cycle_number')->ignore($cycle?->id)->where(fn ($q) => $q->where('tenant_id', $this->tenantId()))],
            'chick_count' => ['required', 'integer', 'min:1'],
            'started_at' => ['required', 'date'],
            'status' => ['required', Rule::in(['active', 'finished'])],
            'notes' => ['nullable', 'string'],
        ];
    }
}
