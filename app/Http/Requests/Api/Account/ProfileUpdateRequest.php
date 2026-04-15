<?php

namespace App\Http\Requests\Api\Account;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'avatar' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'preferred_language' => ['sometimes', 'nullable', 'string', 'in:ar,en'],
            'appearance_mode' => ['sometimes', 'nullable', 'string', 'in:system,light,dark'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (
                !$this->has('name') &&
                !$this->has('preferred_language') &&
                !$this->has('appearance_mode') &&
                !$this->hasFile('avatar')
            ) {
                $validator->errors()->add('profile', 'At least one field must be provided for update.');
            }
        });
    }
}
