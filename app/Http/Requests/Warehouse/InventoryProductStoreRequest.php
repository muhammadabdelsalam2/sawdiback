<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Validation\Rule;

class InventoryProductStoreRequest extends BaseWarehouseRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('inventory_products', 'code')->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'unit' => ['required', 'string', 'max:50'],
            'track_expiry' => ['nullable', 'boolean'],
            'low_stock_threshold' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_best_selling' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
