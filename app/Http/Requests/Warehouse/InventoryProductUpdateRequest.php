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
            'category' => ['required', Rule::in(['feed', 'vet_medicine', 'equipment', 'animal_product'])],
            'unit' => ['required', 'string', 'max:50'],
            'track_expiry' => ['nullable', 'boolean'],
            'low_stock_threshold' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}

