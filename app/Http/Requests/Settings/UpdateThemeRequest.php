<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateThemeRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Protected by middleware already (role + permission)
        return true;
    }

    public function rules(): array
    {
        return [
            'rtl_enabled' => ['nullable', 'boolean'],
            'app_name' => ['nullable', 'string', 'max:100'],
            'primary_color' => ['nullable', 'string', 'max:20'], // keep simple MVP
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'rtl_enabled' => (bool) $this->input('rtl_enabled', false),
            'app_name' => $this->input('app_name') ? trim($this->input('app_name')) : null,
            'primary_color' => $this->input('primary_color') ? trim($this->input('primary_color')) : null,
        ]);
    }
}
