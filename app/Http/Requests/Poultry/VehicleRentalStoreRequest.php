<?php

namespace App\Http\Requests\Poultry;

use Illuminate\Foundation\Http\FormRequest;

class VehicleRentalStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_id'        => ['required', 'integer', 'exists:poultry_transport_vehicles,id'],
            'rental_type'       => ['required', 'string', 'in:internal,external'],
            'payment_status'    => ['required', 'string', 'in:pending,paid,partially_paid'],
            'started_at'        => ['required', 'date'],
            'ended_at'          => ['nullable', 'date'],
            'customer_name'     => ['nullable', 'string', 'max:255'],
            'customer_phone'    => ['nullable', 'string', 'max:50'],
            'origin'            => ['nullable', 'string', 'max:255'],
            'destination'       => ['nullable', 'string', 'max:255'],
            'rental_fee'        => ['nullable', 'numeric', 'min:0'],
            'fuel_cost'         => ['nullable', 'numeric', 'min:0'],
            'driver_commission' => ['nullable', 'numeric', 'min:0'],
            'other_expenses'    => ['nullable', 'numeric', 'min:0'],
            'notes'             => ['nullable', 'string'],
        ];
    }
}
