<?php

namespace App\Http\Requests\Customer\Farms;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FarmPenStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

   public function rules(): array
{
    return [
        'farm_id' => ['required', 'exists:farms,id'],
        'pen_number' => ['required', 'string', 'max:50'],
        'name' => ['nullable', 'string', 'max:255'],
        'type' => ['required', 'string', 'in:goat,cattle,poultry,fish,rabbit,other'],
        'capacity' => ['nullable', 'integer', 'min:0'],
        'current_count' => ['nullable', 'integer', 'min:0'],
        'status' => ['required', 'string', 'in:active,maintenance,quarantine,empty'],
        'notes' => ['nullable', 'string'],
    ];
}
}
