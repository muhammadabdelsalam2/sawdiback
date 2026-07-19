<?php

namespace App\Http\Requests\Customer\Farms;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FarmUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = session('tenant_id') ?? auth()->user()?->tenant_id;
        $farm = $this->route('farm');

        return [
            'name' => ['required', 'string', 'max:190', Rule::unique('farms', 'name')->ignore($farm?->id)->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'type' => ['required', Rule::in(['owned', 'rented'])],
            'location' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
