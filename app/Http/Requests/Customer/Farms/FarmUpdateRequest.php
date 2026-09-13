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
        $farmId = $this->route('farm') instanceof \App\Models\Farm 
            ? $this->route('farm')->id 
            : $this->route('farm');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('farms', 'name')
                    ->where(fn ($query) => $query->where('tenant_id', auth()->user()->tenant_id))
                    ->ignore($farmId),
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('farms', 'code')
                    ->where(fn ($query) => $query->where('tenant_id', auth()->user()->tenant_id))
                    ->ignore($farmId),
            ],
            'ownership_type' => ['nullable', 'string', 'in:owned,rented'],
            'location'       => ['nullable', 'string', 'max:255'],
            'area_sqm'       => ['nullable', 'numeric', 'min:0'],
            'is_active'      => ['sometimes', 'boolean'],
            'notes'          => ['nullable', 'string'],
        ];
    }
}