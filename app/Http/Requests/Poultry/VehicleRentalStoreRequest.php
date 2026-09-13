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
            'vehicle_id'        => ['required', 'exists:poultry_transport_vehicles,id'],
            'rental_type'       => ['required', 'in:internal,external'],
            'customer_name'     => ['nullable', 'required_if:rental_type,external', 'string', 'max:150'],
            'customer_phone'    => ['nullable', 'string', 'max:50'],
            'started_at'        => ['required', 'date'],
            'ended_at'          => ['nullable', 'date', 'after_or_equal:started_at'],
            'origin'            => ['nullable', 'string', 'max:255'],
            'destination'       => ['nullable', 'string', 'max:255'],
            'rental_fee'        => ['nullable', 'numeric', 'min:0'],
            'fuel_cost'         => ['nullable', 'numeric', 'min:0'],
            'driver_commission' => ['nullable', 'numeric', 'min:0'],
            'other_expenses'    => ['nullable', 'numeric', 'min:0'],
            'payment_status'    => ['required', 'in:pending,paid,partially_paid'],
            'notes'             => ['nullable', 'string'],
        ];
    }
}