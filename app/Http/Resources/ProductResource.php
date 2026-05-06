<?php

namespace App\Http\Resources;

use App\Support\LocaleResolver;
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
        $locale = LocaleResolver::resolve($request);
        $name = $this->getLocalizedTitleAttribute();
        $description = $this->getLocalizedDescriptionAttribute();

        return [
            'id' => $this->id,
            'code' => $this->code,

            'name' => $name,
            'title' => $name,
            'image' => $this->image_url,
            'category' => $this->category,
            'category_id' => $this->category_id,
            'unit' => $this->unit,

            'price' => $this->price ?? 0,
            'last_price' => $this->last_price ?? 0,
            'unit_tax' => (float) ($this->tax ?? 0),
            'description' => $description,
            'available_quantity' => (int) ($this->available_quantity ?? 0),
            'is_favorite' => $this->favoriteProducts->contains('inventory_product_id', $this->id),
            'favorite_count' => $this->favoriteProducts->count(),
            'is_active' => (bool) $this->is_active,
            'track_expiry' => (bool) $this->track_expiry,
            'is_best_selling' => (bool) $this->is_best_selling,

            'low_stock_threshold' => (float) ($this->low_stock_threshold ?? 0),
            'notes' => $description,
            'locale' => $locale,

            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
