<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Validation\Rule;

class InventoryBatchStoreRequest extends BaseWarehouseRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'inventory_product_id' => ['required', 'integer', Rule::exists('inventory_products', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'batch_number' => ['required', 'string', 'max:100'],
            'production_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date'],
            'received_at' => ['nullable', 'date'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}

