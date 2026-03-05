<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\OtpType;
use Illuminate\Validation\Rules\Enum;
class VerifyOtpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
    return [
            'identifier' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (
                        !filter_var($value, FILTER_VALIDATE_EMAIL) &&
                        !preg_match('/^[0-9]{8,15}$/', $value)
                    ) {
                        $fail(__('validation.identifier_invalid'));
                    }
                }
            ],

            'code' => [
                'required',
                'digits:6',
            ],

        ];
    }

    public function messages(): array
    {
        return [
            'identifier.required' => __('validation.required'),
            'code.required' => __('validation.required'),
            'code.digits' => __('validation.digits', ['digits' => 6]),
            'type.required' => __('validation.required'),
            'type.in' => __('validation.invalid'),
        ];
    }

    /**
     * Clean input before validation
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'identifier' => trim($this->identifier),
            'code' => trim($this->code),
        ]);
    }
}
