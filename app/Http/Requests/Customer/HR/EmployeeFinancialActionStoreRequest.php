<?php

namespace App\Http\Requests\Customer\HR;

use App\Models\EmployeeFinancialAction;
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
                'string',
                'in:' . implode(',', [
                    EmployeeFinancialAction::TYPE_ADVANCE_PAYMENT,
                    EmployeeFinancialAction::TYPE_MONTHLY_DEDUCTION,
                    EmployeeFinancialAction::TYPE_SALARY_INCREASE_FIXED,
                    EmployeeFinancialAction::TYPE_SALARY_INCREASE_PERCENT,
                    EmployeeFinancialAction::TYPE_FINANCIAL_BONUS,
                    EmployeeFinancialAction::TYPE_IN_KIND_GIFT,
                ]),
            ],
            'amount' => [
                'nullable',
                'numeric',
                'min:0',
                'required_if:type,' . implode(',', [
                    EmployeeFinancialAction::TYPE_ADVANCE_PAYMENT,
                    EmployeeFinancialAction::TYPE_MONTHLY_DEDUCTION,
                    EmployeeFinancialAction::TYPE_SALARY_INCREASE_FIXED,
                    EmployeeFinancialAction::TYPE_FINANCIAL_BONUS,
                ]),
            ],
            'percentage' => [
                'nullable',
                'numeric',
                'min:0.01',
                'max:100',
                'required_if:type,' . EmployeeFinancialAction::TYPE_SALARY_INCREASE_PERCENT,
            ],
            'gift_description' => [
                'nullable',
                'string',
                'max:255',
                'required_if:type,' . EmployeeFinancialAction::TYPE_IN_KIND_GIFT,
            ],
            'action_date' => ['required', 'date'],
            'effective_month' => ['nullable', 'date'],
            'installments_count' => [
                'nullable',
                'integer',
                'min:1',
                'required_if:type,' . EmployeeFinancialAction::TYPE_ADVANCE_PAYMENT,
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}