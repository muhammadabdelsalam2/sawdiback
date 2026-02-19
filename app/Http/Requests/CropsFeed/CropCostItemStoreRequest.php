<?php

namespace App\Http\Requests\CropsFeed;

use Illuminate\Validation\Rule;

class CropCostItemStoreRequest extends BaseCropsFeedRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'crop_id' => ['required', 'integer', Rule::exists('crops', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'item' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'cost_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
