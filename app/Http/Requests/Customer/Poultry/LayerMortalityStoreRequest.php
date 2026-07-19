<?php

namespace App\Http\Requests\Customer\Poultry;

class LayerMortalityStoreRequest extends BasePoultryRequest
{
    public function rules(): array
    {
        return [
            'mortality_date' => ['required', 'date'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
