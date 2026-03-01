<?php

namespace App\Services\Warehouse;

use App\Models\InventoryBatch;
use App\Models\InventoryMovement;
use App\Models\InventoryProductionRecord;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RecordProductionService
{
    public function execute(array $data): InventoryProductionRecord
    {
        return DB::transaction(function () use ($data) {
            $quantity = (float) $data['quantity'];
            if ($quantity <= 0) {
                throw new RuntimeException('Quantity must be greater than zero.');
            }

            $unitCost = array_key_exists('unit_cost', $data) && $data['unit_cost'] !== null
                ? (float) $data['unit_cost']
                : null;

            $production = InventoryProductionRecord::query()->create([
                'tenant_id' => $data['tenant_id'] ?? null,
                'inventory_product_id' => $data['inventory_product_id'],
                'livestock_animal_id' => $data['livestock_animal_id'] ?? null,
                'production_date' => $data['production_date'],
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'notes' => $data['notes'] ?? null,
            ]);

            $batch = InventoryBatch::query()->create([
                'tenant_id' => $data['tenant_id'] ?? null,
                'inventory_product_id' => $data['inventory_product_id'],
                'batch_number' => $data['batch_number'],
                'production_date' => $data['production_date'],
                'expiry_date' => $data['expiry_date'] ?? null,
                'received_at' => $data['production_date'],
                'quantity_initial' => $quantity,
                'quantity_available' => $quantity,
                'unit_cost' => $unitCost,
                'source_type' => 'production',
                'source_id' => $production->id,
                'notes' => $data['notes'] ?? null,
            ]);

            InventoryMovement::query()->create([
                'tenant_id' => $data['tenant_id'] ?? null,
                'inventory_product_id' => $data['inventory_product_id'],
                'inventory_batch_id' => $batch->id,
                'movement_type' => 'in',
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $unitCost !== null ? round($quantity * $unitCost, 2) : null,
                'movement_date' => $data['production_date'],
                'reference_type' => 'production',
                'reference_id' => $production->id,
                'notes' => $data['notes'] ?? null,
            ]);

            return $production;
        });
    }
}

