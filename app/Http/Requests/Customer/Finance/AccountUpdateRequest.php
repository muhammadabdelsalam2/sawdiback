<?php

namespace App\Http\Requests\Customer\Finance;

use Illuminate\Validation\Rule;

class AccountUpdateRequest extends BaseFinanceRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();
        $accountId = $this->route('account')?->id ?? null;

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('accounts', 'code')->where(fn ($q) => $q->where('tenant_id', $tenantId))->ignore($accountId),
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
