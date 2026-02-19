<?php

namespace App\Http\Requests\Livestock;

use Illuminate\Validation\Rule;

class FeedTypeStoreRequest extends BaseLivestockRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('feed_types', 'name')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'category' => ['required', Rule::in(['concentrate', 'roughage', 'supplement'])],
            'unit' => ['required', 'string', 'max:100'],
            'cost_per_unit' => ['nullable', 'numeric', 'min:0'],
            'low_stock_threshold' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
