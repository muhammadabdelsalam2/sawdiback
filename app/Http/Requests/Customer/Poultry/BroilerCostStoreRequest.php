<?php

namespace App\Http\Requests\Customer\Poultry;

use Illuminate\Validation\Rule;

class BroilerCostStoreRequest extends BasePoultryRequest
{
    public function rules(): array
    {
        return [
            'cost_type' => ['required', Rule::in(['chicks_purchase', 'feed', 'slaughter_packaging'])],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'cost_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
