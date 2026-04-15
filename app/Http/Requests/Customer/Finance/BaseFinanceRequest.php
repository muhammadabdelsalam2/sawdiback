<?php

namespace App\Http\Requests\Customer\Finance;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseFinanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function tenantId(): ?string
    {
        return session('tenant_id') ?? auth()->user()?->tenant_id;
    }
}
