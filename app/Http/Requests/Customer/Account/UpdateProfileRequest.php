<?php

namespace App\Http\Requests\Customer\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->hasRole('Customer');
    }

    public function rules(): array
    {
        $userId = Auth::id();

        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email,' . $userId],
            'phone' => ['nullable', 'string', 'max:30'],
            'preferred_language' => ['nullable', 'string', 'in:en,ar'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
