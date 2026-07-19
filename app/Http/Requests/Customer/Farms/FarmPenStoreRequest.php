<?php

namespace App\Http\Requests\Customer\Farms;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FarmPenStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = session('tenant_id') ?? auth()->user()?->tenant_id;

        return [
            'farm_id' => ['required', 'integer', Rule::exists('farms', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'pen_number' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::in(['livestock', 'poultry', 'mixed'])],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
