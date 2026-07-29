<?php

namespace App\Http\Requests\CropsFeed;

use Illuminate\Validation\Rule;

class CropUpdateRequest extends BaseCropsFeedRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'farm_id' => ['nullable', 'integer', Rule::exists('farms', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'greenhouse_type' => ['nullable', 'string', 'max:255'],
            'greenhouse_number' => ['nullable', 'string', 'max:255'],
            'greenhouse_location' => ['nullable', 'string', 'max:255'],
            'irrigation_type' => ['nullable', Rule::in(['towers', 'seedlings', 'ground'])],
            'name' => ['required', 'string', 'max:255'],
            'land_area' => ['required', 'numeric', 'min:0.01'],
            'planting_date' => ['required', 'date'],
            'expected_harvest_date' => ['nullable', 'date', 'after_or_equal:planting_date'],
            'yield_tons' => ['nullable', 'numeric', 'min:0'],
            'wasted_tons' => ['nullable', 'numeric', 'min:0'],
            'available_for_feed_tons' => ['nullable', 'numeric', 'min:0'],
            'sale_price_per_ton' => ['nullable', 'numeric', 'min:0'],
            'water_cost' => ['nullable', 'numeric', 'min:0'],
            'labor_cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
