<?php

namespace App\Http\Requests\Customer\Poultry;

class BroilerSaleStoreRequest extends BasePoultryRequest
{
    public function rules(): array
    {
        return [
            'sale_date' => ['required', 'date'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'customer_name' => ['nullable', 'string', 'max:190'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
