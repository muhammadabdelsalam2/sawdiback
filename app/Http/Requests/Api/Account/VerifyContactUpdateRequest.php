<?php

namespace App\Http\Requests\Api\Account;

use Illuminate\Foundation\Http\FormRequest;

class VerifyContactUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:phone,email'],
            'identifier' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $type = $this->input('type');

                    if ($type === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail('Please provide a valid email address.');
                    }

                    if ($type === 'phone' && !preg_match('/^\+?[0-9]{8,15}$/', $value)) {
                        $fail('Please provide a valid phone number.');
                    }
                }
            ],
            'code' => ['required', 'digits:6'],
        ];
    }
}
