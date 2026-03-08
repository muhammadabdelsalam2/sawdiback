<?php

namespace App\Http\Requests\Api\Account;

use Illuminate\Foundation\Http\FormRequest;

class RequestContactUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:phone,email'],
            'value' => [
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
        ];
    }
}
