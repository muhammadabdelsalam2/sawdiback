<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,

            'label' => $this->label,
            'title' => $this->title,
            'type' => $this->type,

            'contact' => [
                'name' => $this->recipient_name,
                'phone' => $this->phone,
            ],

            'address' => [
                'line_1' => $this->address_line_1,
                'line_2' => $this->address_line_2,
                'building' => $this->building,
                'floor' => $this->floor,
                'apartment' => $this->apartment,
                'landmark' => $this->landmark,
                'city' => $this->city,
                'country' => $this->country,
                'postal_code' => $this->postal_code,
            ],

            'coordinates' => [
                'lat' => $this->latitude,
                'lng' => $this->longitude,
            ],

            'is_default' => $this->is_default,

            'created_at' => optional($this->created_at)->toDateTimeString(),
        ];
    }
}
