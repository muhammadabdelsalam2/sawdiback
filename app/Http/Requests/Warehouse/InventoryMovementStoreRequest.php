<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Validation\Rule;

class InventoryMovementStoreRequest extends BaseWarehouseRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'inventory_product_id' => ['required', 'integer', Rule::exists('inventory_products', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'inventory_batch_id' => ['nullable', 'integer', Rule::exists('inventory_batches', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'movement_type' => ['required', Rule::in(['in', 'out', 'adjustment'])],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'movement_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}

