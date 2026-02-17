<?php

namespace App\Http\Requests\Customer\Subscriptions;

use Illuminate\Foundation\Http\FormRequest;

class CancelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Customer');
    }

    public function rules(): array
    {
        return [];
    }
}
