<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Routes already protected by middleware (role + permission).
        return true;
    }

    public function rules(): array
    {
        $tenantId = 1; // TODO: replace with tenant resolver later

        return [
            'country_id' => [
                'required', 'integer',
                Rule::exists('countries', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('cities', 'name')->where(function ($q) use ($tenantId) {
                    return $q->where('tenant_id', $tenantId)
                             ->where('country_id', $this->input('country_id'));
                }),
            ],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => (bool) $this->input('is_active', false),
        ]);
    }
}
