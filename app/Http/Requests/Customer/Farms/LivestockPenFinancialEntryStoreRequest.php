<?php

namespace App\Http\Requests\Customer\Farms;

use Illuminate\Foundation\Http\FormRequest;

class LivestockPenFinancialEntryStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // تأكد أنها true
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:feed_costs,slaughter_packaging,sale,other'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'entry_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}