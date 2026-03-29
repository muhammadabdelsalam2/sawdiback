<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->building ? 'Building ' . $this->building : null,
            $this->floor ? 'Floor ' . $this->floor : null,
            $this->apartment ? 'Apartment ' . $this->apartment : null,
            $this->city,
            $this->country,
            $this->postal_code,
        ]);

        return [
            'id' => $this->id,
            'label' => $this->label,
            'title' => $this->label,
            'type' => $this->type,
            'recipient_name' => $this->recipient_name,
            'phone' => $this->phone,
            'address_line_1' => $this->address_line_1,
            'street_address' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'building' => $this->building,
            'floor' => $this->floor,
            'apartment' => $this->apartment,
            'landmark' => $this->landmark,
            'city' => $this->city,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
            'notes' => $this->notes,
            'latitude' => $this->latitude !== null ? (float) $this->latitude : null,
            'longitude' => $this->longitude !== null ? (float) $this->longitude : null,
            'is_default' => (bool) $this->is_default,
            'full_address' => implode(', ', $parts),
            'created_at' => optional($this->created_at)?->toISOString(),
            'updated_at' => optional($this->updated_at)?->toISOString(),
        ];
    }
}
