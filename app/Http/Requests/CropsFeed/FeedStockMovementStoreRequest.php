<?php

namespace App\Http\Requests\CropsFeed;

use Illuminate\Validation\Rule;

class FeedStockMovementStoreRequest extends BaseCropsFeedRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'feed_type_id' => ['required', 'integer', Rule::exists('feed_types', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'movement_type' => ['required', Rule::in(['in', 'out'])],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'movement_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
