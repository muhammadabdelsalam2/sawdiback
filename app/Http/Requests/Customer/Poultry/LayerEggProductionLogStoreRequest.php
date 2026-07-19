<?php

namespace App\Http\Requests\Customer\Poultry;

class LayerEggProductionLogStoreRequest extends BasePoultryRequest
{
    public function rules(): array
    {
        return [
            'production_date' => ['required', 'date'],
            'eggs_count' => ['required', 'integer', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'daily_feed_cost' => ['required', 'numeric', 'min:0'],
            'damaged_count' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
