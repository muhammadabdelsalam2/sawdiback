<?php

namespace App\Http\Requests\Customer\Poultry;

class ChickenBreedEggLogStoreRequest extends BasePoultryRequest
{
    public function rules(): array
    {
        return [
            'production_date' => ['required', 'date'],
            'eggs_count' => ['required', 'integer', 'min:0'],
            'fertilized_count' => ['nullable', 'integer', 'min:0'],
            'unfertilized_count' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
