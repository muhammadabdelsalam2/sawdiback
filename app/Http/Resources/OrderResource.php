<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_no' => $this->order_no,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'totals' => [
                'subtotal' => (float) $this->subtotal,
                'shipping' => (float) $this->shipping,
                'taxes' => (float) $this->taxes,
                'vat' => (float) $this->vat,
                'discount' => (float) $this->discount,
                'total' => (float) $this->total,
                'currency' => $this->currency,
            ],
            'placed_at' => $this->placed_at?->toISOString(),
            'estimated_delivery_at' => $this->estimated_delivery_at?->toISOString(),
            'address' => $this->whenLoaded('address', fn () => new UserAddressResource($this->address)),
            'items' => $this->whenLoaded('items', fn () => OrderItemResource::collection($this->items)),
            'items_count' => $this->whenLoaded('items') ? $this->items->count() : null,
            'can_review' => $this->getAttribute('can_review'),
            'reviewed' => $this->getAttribute('reviewed'),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
