<?php

namespace App\Http\Requests\Customer\HR;

use Illuminate\Foundation\Http\FormRequest;

class LeaveActionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [];
    }
}
