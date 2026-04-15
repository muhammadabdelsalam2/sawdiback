<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $items = $this->whenLoaded('items');

        return [
            'id' => $this->id,
            'coupon_code' => $this->coupon_code,
            'weekly_delivery' => (bool) $this->weekly_delivery,
            'items' => CartItemResource::collection($items),
            'items_count' => $items ? $items->count() : 0,
            'is_empty' => $items ? $items->isEmpty() : true,
            'totals' => [
                'subtotal' => (float) $this->subtotal,
                'shipping' => (float) $this->shipping,
                'taxes' => (float) $this->taxes,
                'vat' => (float) $this->vat,
                'discount' => (float) $this->discount,
                'total' => (float) $this->total,
            ],
            'address' => $this->whenLoaded('address', fn () => new UserAddressResource($this->address)),
            'updated_at' => optional($this->updated_at)?->toISOString(),
        ];
    }
}
