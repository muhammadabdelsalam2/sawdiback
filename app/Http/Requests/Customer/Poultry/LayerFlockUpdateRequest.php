<?php

namespace App\Http\Requests\Customer\Poultry;

use Illuminate\Validation\Rule;

class LayerFlockUpdateRequest extends BasePoultryRequest
{
    public function rules(): array
    {
        $flock = $this->route('layer_flock');

        return [
            'pen_id' => ['nullable', 'integer', Rule::exists('farm_pens', 'id')->where(fn ($q) => $q->where('tenant_id', $this->tenantId()))],
            'flock_number' => ['required', 'string', 'max:100', Rule::unique('poultry_layer_flocks', 'flock_number')->ignore($flock?->id)->where(fn ($q) => $q->where('tenant_id', $this->tenantId()))],
            'chicken_count' => ['required', 'integer', 'min:1'],
            'purchase_cost' => ['required', 'numeric', 'min:0'],
            'started_at' => ['required', 'date'],
            'status' => ['required', Rule::in(['active', 'finished'])],
            'notes' => ['nullable', 'string'],
        ];
    }
}
