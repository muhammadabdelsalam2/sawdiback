<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $product = $this->whenLoaded('product');

        return [
            'id' => $this->id,
            'product' => $product ? [
                'id' => $product->id,
                'name' => $product->name,
                'code' => $product->code,
                'image' => $product->image_url ?? null,
                'unit' => $product->unit,
                'category' => $product->category,
                'price' => (float) ($product->last_price ?? 0),
                'unit_tax' => (float) ($product->tax ?? 0),
                'available_quantity' => (float) ($product->available_quantity ?? 0),
            ] : null,
            'quantity' => (float) $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'unit_tax' => (float) $this->unit_tax,
            'line_subtotal' => (float) $this->line_subtotal,
            'line_tax' => (float) $this->line_tax,
            'line_total' => (float) $this->line_total,
        ];
    }
}
