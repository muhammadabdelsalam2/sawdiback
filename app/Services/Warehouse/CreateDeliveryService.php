<?php

namespace App\Services\Warehouse;

use App\Models\InventoryBatch;
use App\Models\InventoryDelivery;
use App\Models\InventoryDeliveryItem;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CreateDeliveryService
{
    public function execute(array $data): InventoryDelivery
    {
        return DB::transaction(function () use ($data) {
            $delivery = InventoryDelivery::query()->create([
                'tenant_id' => $data['tenant_id'] ?? null,
                'delivery_number' => $data['delivery_number'],
                'customer_name' => $data['customer_name'] ?? null,
                'delivered_at' => $data['delivered_at'],
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $batch = InventoryBatch::query()
                    ->where('inventory_product_id', $item['inventory_product_id'])
                    ->lockForUpdate()
                    ->findOrFail($item['inventory_batch_id']);

                $quantity = (float) $item['quantity'];
                if ((float) $batch->quantity_available < $quantity) {
                    throw new RuntimeException('Insufficient stock in one of delivery batches.');
                }

                $unitPrice = array_key_exists('unit_price', $item) && $item['unit_price'] !== null
                    ? (float) $item['unit_price']
                    : null;

                $deliveryItem = InventoryDeliveryItem::query()->create([
                    'tenant_id' => $data['tenant_id'] ?? null,
                    'inventory_delivery_id' => $delivery->id,
                    'inventory_product_id' => $item['inventory_product_id'],
                    'inventory_batch_id' => $item['inventory_batch_id'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $unitPrice !== null ? round($unitPrice * $quantity, 2) : null,
                    'notes' => $item['notes'] ?? null,
                ]);

                $batch->update([
                    'quantity_available' => round((float) $batch->quantity_available - $quantity, 2),
                ]);

                InventoryMovement::query()->create([
                    'tenant_id' => $data['tenant_id'] ?? null,
                    'inventory_product_id' => $item['inventory_product_id'],
                    'inventory_batch_id' => $item['inventory_batch_id'],
                    'movement_type' => 'out',
                    'quantity' => $quantity,
                    'unit_cost' => null,
                    'total_cost' => null,
                    'movement_date' => date('Y-m-d', strtotime($data['delivered_at'])),
                    'reference_type' => 'delivery',
                    'reference_id' => $deliveryItem->id,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            return $delivery;
        });
    }
}

