<?php

namespace App\Http\Requests\CropsFeed;

use Illuminate\Validation\Rule;

class CropFeedAllocationStoreRequest extends BaseCropsFeedRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'crop_id' => ['required', 'integer', Rule::exists('crops', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'feed_type_id' => ['required', 'integer', Rule::exists('feed_types', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'quantity_tons' => ['required', 'numeric', 'min:0.01'],
            'allocation_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
