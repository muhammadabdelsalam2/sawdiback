<?php

namespace App\Http\Requests\CropsFeed;

use Illuminate\Validation\Rule;

class CropMaterialUsageStoreRequest extends BaseCropsFeedRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'crop_id' => ['required', 'integer', Rule::exists('crops', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'material_type' => ['required', Rule::in(['fertilizer', 'vitamins', 'pesticide', 'other'])],
            'name' => ['required', 'string', 'max:255'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'used_on' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
