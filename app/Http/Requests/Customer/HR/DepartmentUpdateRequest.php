<?php

namespace App\Http\Requests\Customer\HR;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:190'],
            'code' => ['nullable', 'string', 'max:50'],
        ];
    }
}
