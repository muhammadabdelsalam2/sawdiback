<?php

namespace App\Http\Requests\Customer\Poultry;

use Illuminate\Validation\Rule;

class HatcheryBatchUpdateRequest extends HatcheryBatchStoreRequest
{
    public function rules(): array
    {
        $batch = $this->route('hatchery_batch');
        $rules = parent::rules();
        $rules['batch_number'] = ['required', 'string', 'max:100', Rule::unique('poultry_hatchery_batches', 'batch_number')->ignore($batch?->id)->where(fn ($q) => $q->where('tenant_id', $this->tenantId()))];

        return $rules;
    }
}
