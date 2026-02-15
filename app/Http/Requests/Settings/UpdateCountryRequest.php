<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = 1; // TODO: replace with tenant resolver later
        $countryId = $this->route('country')?->id ?? $this->route('country'); // supports model binding

        return [
            'name' => [
                'required','string','max:255',
                Rule::unique('countries', 'name')
                    ->ignore($countryId)
                    ->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            'iso2' => [
                'nullable','string','size:2',
                Rule::unique('countries', 'iso2')
                    ->ignore($countryId)
                    ->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            'iso3' => [
                'nullable','string','size:3',
                Rule::unique('countries', 'iso3')
                    ->ignore($countryId)
                    ->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            'phone_code' => ['nullable','string','max:10'],
            'is_active' => ['nullable','boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => (bool) $this->input('is_active', false),
            'iso2' => $this->input('iso2') ? strtoupper(trim($this->input('iso2'))) : null,
            'iso3' => $this->input('iso3') ? strtoupper(trim($this->input('iso3'))) : null,
        ]);
    }
}
