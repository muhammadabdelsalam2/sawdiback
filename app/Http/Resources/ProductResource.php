<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'code' => $this->code,

            'name' => $this->name,
            'image' => $this->imageUrl,
            'category' => $this->category,
            'unit' => $this->unit,

            'price' => (float) $this->price,
            'last_price' => (float) $this->last_price,
            'unit_tax' => (float) ($this->tax ?? 0),

            'available_quantity' => (int) $this->available_quantity,

            'is_active' => (bool) $this->is_active,
            'track_expiry' => (bool) $this->track_expiry,

            'low_stock_threshold' => (float) $this->low_stock_threshold,

            'notes' => $this->notes,

            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
