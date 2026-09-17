<?php

namespace App\Http\Requests\Customer\Poultry;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PoultryVehicleUpdateRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
 public function rules(): array
    {
        return [
            'plate_number'     => ['required', 'string', 'max:50'],
            'farm_id'          => ['nullable', 'exists:farms,id'],
            'ownership_type'   => ['required', 'in:owned,leased'],
            'status'           => ['required', 'in:available,on_trip,maintenance,out_of_service'],
            'lessor_name'      => ['nullable', 'required_if:ownership_type,leased', 'string', 'max:150'],
            'lessor_phone'     => ['nullable', 'string', 'max:50'],
            'lease_cost'       => ['nullable', 'numeric', 'min:0'],
            'lease_period'     => ['nullable', 'in:monthly,daily,per_trip'],
            'lease_start_date' => ['nullable', 'date'],
            'lease_end_date'   => ['nullable', 'date', 'after_or_equal:lease_start_date'],
            'driver_name'      => ['nullable', 'string', 'max:150'],
            'driver_phone'     => ['nullable', 'string', 'max:50'],
            'capacity_birds'   => ['nullable', 'integer', 'min:0'],
            'capacity_crates'  => ['nullable', 'integer', 'min:0'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ];
    }
}
