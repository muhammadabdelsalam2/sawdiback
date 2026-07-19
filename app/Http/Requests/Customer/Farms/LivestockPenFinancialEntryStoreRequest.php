<?php

namespace App\Http\Requests\Customer\Farms;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LivestockPenFinancialEntryStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['sale', 'slaughter_packaging'])],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'entry_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
