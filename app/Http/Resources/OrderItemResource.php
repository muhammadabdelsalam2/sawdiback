<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $snapshot = $this->snapshot ?? [];

        return [
            'id' => $this->id,
            'product_id' => $this->inventory_product_id,
            'name' => $this->product_name,
            'code' => $this->product_code,
            'unit' => $this->unit,
            'quantity' => (float) $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'unit_tax' => (float) $this->unit_tax,
            'line_subtotal' => (float) $this->line_subtotal,
            'line_tax' => (float) $this->line_tax,
            'discount' => (float) $this->discount,
            'line_total' => (float) $this->line_total,
            'image' => $snapshot['image'] ?? null,
        ];
    }
}
