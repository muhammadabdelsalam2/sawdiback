<?php

namespace App\Http\Requests\CropsFeed;

use Illuminate\Validation\Rule;

class CropSeedlingStockStoreRequest extends BaseCropsFeedRequest
{
    public function rules(): array
    {
        return [
            'item_type' => ['required', Rule::in(['seed', 'seedling'])],
            'name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:50'],
            'low_stock_threshold' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
