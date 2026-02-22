<?php

namespace App\Http\Requests\CropsFeed;

use Illuminate\Validation\Rule;

class CropGrowthStageStoreRequest extends BaseCropsFeedRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'crop_id' => ['required', 'integer', Rule::exists('crops', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'stage_name' => ['required', 'string', 'max:255'],
            'recorded_on' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
