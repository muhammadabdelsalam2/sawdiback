<?php

namespace App\Http\Requests\Customer\Finance;

use Illuminate\Validation\Rule;

class AccountStoreRequest extends BaseFinanceRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('accounts', 'code')->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['asset', 'liability', 'equity', 'revenue', 'expense'])],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('accounts', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
