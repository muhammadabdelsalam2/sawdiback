<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SocialLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider' => 'required|string|in:google,facebook,instagram',
            'providerId' => 'required|string',
            'name' => 'required|string',
            'email' => 'nullable|email',
            'avatar' => 'nullable|url',
        ];
    }
}
