<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Validation\Rule;

class InventoryDeliveryStoreRequest extends BaseWarehouseRequest
{
    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'delivery_number' => [
                'required',
                'string',
                'max:100',
                Rule::unique('inventory_deliveries', 'delivery_number')->where(fn ($q) => $q->where('tenant_id', $tenantId)),
            ],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'delivered_at' => ['required', 'date'],
            'status' => ['required', Rule::in(['draft', 'shipped', 'delivered', 'cancelled'])],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.inventory_product_id' => ['required', 'integer', Rule::exists('inventory_products', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'items.*.inventory_batch_id' => ['required', 'integer', Rule::exists('inventory_batches', 'id')->where(fn ($q) => $q->where('tenant_id', $tenantId))],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.notes' => ['nullable', 'string'],
        ];
    }
}
