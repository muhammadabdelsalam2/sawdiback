<?php

namespace App\Http\Requests\Poultry;

use Illuminate\Foundation\Http\FormRequest;

class TransportVehicleStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'farm_id'         => ['nullable', 'exists:farms,id'],
            'plate_number'    => ['required', 'string', 'max:50'],
            'driver_name'     => ['nullable', 'string', 'max:150'],
            'driver_phone'    => ['nullable', 'string', 'max:50'],
            'capacity_birds'  => ['nullable', 'integer', 'min:0'],
            'capacity_crates' => ['nullable', 'integer', 'min:0'],
            'status'          => ['required', 'in:available,on_trip,maintenance,out_of_service'],
            'notes'           => ['nullable', 'string'],
        ];
    }
}