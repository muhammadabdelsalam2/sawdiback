<?php

namespace App\Http\Requests\CropsFeed;

class CropUpdateRequest extends BaseCropsFeedRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'land_area' => ['required', 'numeric', 'min:0.01'],
            'planting_date' => ['required', 'date'],
            'yield_tons' => ['nullable', 'numeric', 'min:0'],
            'available_for_feed_tons' => ['nullable', 'numeric', 'min:0'],
            'sale_price_per_ton' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
