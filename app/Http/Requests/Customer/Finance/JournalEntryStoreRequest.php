<?php

namespace App\Http\Requests\Customer\Finance;

use Illuminate\Validation\Rule;

class JournalEntryStoreRequest extends BaseFinanceRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'entry_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'lines' => ['required', 'array', 'min:2'],
            'lines.*.account_id' => [
                'required',
                'integer',
                Rule::exists('accounts', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            'lines.*.debit' => ['nullable', 'numeric', 'min:0'],
            'lines.*.credit' => ['nullable', 'numeric', 'min:0'],
            'lines.*.memo' => ['nullable', 'string', 'max:255'],
        ];
    }
}
