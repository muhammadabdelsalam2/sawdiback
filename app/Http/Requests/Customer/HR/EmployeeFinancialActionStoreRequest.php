<?php

namespace App\Http\Requests\Customer\HR;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeFinancialActionStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => [
                'required',
                'in:advance_payment,monthly_deduction,salary_increase_fixed,salary_increase_percent,financial_bonus,in_kind_gift'
            ],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'gift_description' => ['nullable', 'string', 'max:255'],
            'action_date' => ['required', 'date'],
            'effective_month' => ['nullable', 'date'],
            'installments_count' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ];
    }
}