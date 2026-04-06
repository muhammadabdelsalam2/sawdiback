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
        return [
            'id' => $this->id,
            'code' => $this->code,

            'name' => $this->name,
            'image' => $this->image_url,
            'category' => $this->category,
            'category_id' => $this->category_id,
            'unit' => $this->unit,

            'price' => (float) ($this->price ?? 0),
            'last_price' => (float) ($this->last_price ?? 0),
            'unit_tax' => (float) ($this->tax ?? 0),
            'description' => $this->notes,
            'available_quantity' => (int) ($this->available_quantity ?? 0),

            'is_active' => (bool) $this->is_active,
            'track_expiry' => (bool) $this->track_expiry,
            'is_best_selling' => (bool) $this->is_best_selling,

            'low_stock_threshold' => (float) ($this->low_stock_threshold ?? 0),
            'notes' => $this->notes,

            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}