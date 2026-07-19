<?php

namespace App\Http\Requests\Warehouse;

use App\Models\InventoryProduct;
use Illuminate\Validation\Rule;

class InventoryProductUpdateRequest extends BaseWarehouseRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();
        /** @var InventoryProduct|null $product */
        $product = $this->route('product');

        return [
            'code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('inventory_products', 'code')
                    ->where(fn ($q) => $q->where('tenant_id', $tenantId))
                    ->ignore($product?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'asset_category' => ['required', 'in:feed,seed,equipment,other'],
            'farm_location' => ['nullable', 'string', 'max:255'],
            'farm_id' => ['nullable', 'integer', Rule::exists('farms', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'unit' => ['required', 'string', 'max:50'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'track_expiry' => ['nullable', 'boolean'],
            'low_stock_threshold' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_best_selling' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'last_price' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
