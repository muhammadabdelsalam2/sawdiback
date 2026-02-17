<?php

namespace App\Http\Requests\Livestock;

use Illuminate\Validation\Rule;

class VaccineStoreRequest extends BaseLivestockRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('vaccines', 'name')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'default_interval_days' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
