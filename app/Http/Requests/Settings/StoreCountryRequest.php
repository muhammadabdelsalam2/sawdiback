<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCountryRequest extends FormRequest
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
            'name' => [
                'required','string','max:255',
                Rule::unique('countries', 'name')->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            'iso2' => [
                'nullable','string','size:2',
                Rule::unique('countries', 'iso2')->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            'iso3' => [
                'nullable','string','size:3',
                Rule::unique('countries', 'iso3')->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            'phone_code' => ['nullable','string','max:10'],
            'is_active' => ['nullable','boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Checkbox normalization
        $this->merge([
            'is_active' => (bool) $this->input('is_active', false),
            'iso2' => $this->input('iso2') ? strtoupper(trim($this->input('iso2'))) : null,
            'iso3' => $this->input('iso3') ? strtoupper(trim($this->input('iso3'))) : null,
        ]);
    }
}
