<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class ContactInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:190'],
            'address_ar' => ['nullable', 'string', 'max:500'],
            'address_en' => ['nullable', 'string', 'max:500'],
            'working_hours_ar' => ['nullable', 'string', 'max:500'],
            'working_hours_en' => ['nullable', 'string', 'max:500'],
            'description_ar' => ['nullable', 'string', 'max:2000'],
            'description_en' => ['nullable', 'string', 'max:2000'],
            'whatsapp_url' => ['nullable', 'url', 'max:500'],
            'facebook_url' => ['nullable', 'url', 'max:500'],
            'instagram_url' => ['nullable', 'url', 'max:500'],
            'x_url' => ['nullable', 'url', 'max:500'],
        ];
    }
}
