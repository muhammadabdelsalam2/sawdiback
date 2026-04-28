<?php

namespace App\Http\Requests\Farmer;

use App\DTOs\Farmer\FarmerDTO;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFarmerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:farmers,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'opening_balance' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'phone.required' => 'The phone field is required.',
            'address.required' => 'The address field is required.',
            'opening_balance.required' => 'The opening balance field is required.',
        ];
    }
    // Default function automatic return Validation Error response if validation fails, so we don't need to handle it manually in the controller
    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        //redirect back with error message and old input
        return redirect()
            ->back()
            ->withErrors($validator)
            ->withInput();
    }
    public function toDTO(): FarmerDTO
    {
        return new FarmerDTO(
            name: $this->input('name'),
            email: $this->input('email'),
            phone: $this->input('phone'),
            address: $this->input('address'),
            opening_balance: $this->input('opening_balance'),
            is_active: $this->boolean('is_active', true),
        );
    }
}
