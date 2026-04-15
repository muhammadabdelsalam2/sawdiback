<?php

namespace App\Http\Requests\Customer\Finance;

use Illuminate\Validation\Rule;

class ExpenseStoreRequest extends BaseFinanceRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'expense_no' => [
                'required',
                'string',
                'max:100',
                Rule::unique('expenses', 'expense_no')->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            'expense_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency_id' => ['nullable', 'integer', 'exists:currencies,id'],
            'expense_account_id' => [
                'required',
                'integer',
                Rule::exists('accounts', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            'payment_account_id' => [
                'required',
                'integer',
                Rule::exists('accounts', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            'payment_method' => ['required', Rule::in(['cash', 'bank', 'other'])],
            'vendor_name' => ['nullable', 'string', 'max:190'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'posted', 'cancelled'])],
            'source_type' => ['nullable', 'string', 'max:100'],
            'source_id' => ['nullable', 'integer'],
        ];
    }
}
