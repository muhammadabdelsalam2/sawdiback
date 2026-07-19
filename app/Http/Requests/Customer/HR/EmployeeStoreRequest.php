<?php

namespace App\Http\Requests\Customer\HR;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'department_id' => ['nullable', 'integer'],
            'job_title_id' => ['nullable', 'integer'],
            'worker_number' => ['nullable', 'string', 'max:100'],
            'profession' => ['nullable', 'string', 'max:190'],
            'employment_status' => ['required', 'in:active,on_leave,contract_ended'],
            'operational_department' => ['nullable', 'in:poultry,crops,livestock'],

            'full_name' => ['required', 'string', 'max:190'],
            'email' => ['nullable', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:50'],
            'national_id' => ['nullable', 'string', 'max:100'],

            'hire_date' => ['nullable', 'date'],
            'passport_expiry_date' => ['nullable', 'date'],
            'iqama_expiry_date' => ['nullable', 'date'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'attachment_passport' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'attachment_iqama' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'attachment_identity' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }
}
