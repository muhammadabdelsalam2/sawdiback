<?php

namespace App\Services\Warehouse;

use App\Models\InventoryBatch;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ReceiveInventoryBatchService
{
    public function execute(array $data): InventoryBatch
    {
        return DB::transaction(function () use ($data) {
            $quantity = (float) $data['quantity'];
            if ($quantity <= 0) {
                throw new RuntimeException('Quantity must be greater than zero.');
            }

            $unitCost = array_key_exists('unit_cost', $data) && $data['unit_cost'] !== null
                ? (float) $data['unit_cost']
                : null;

            $sourceType = $data['source_type'] ?? 'manual_receive';
            $sourceId = $this->normalizeNumericReference($data['source_id'] ?? null);
            $referenceType = $data['reference_type'] ?? 'inventory_batch';
            $referenceId = $this->normalizeNumericReference($data['reference_id'] ?? null);

            $batch = InventoryBatch::query()->create([
                'tenant_id' => $data['tenant_id'] ?? null,
                'inventory_product_id' => $data['inventory_product_id'],
                'batch_number' => $data['batch_number'],
                'production_date' => $data['production_date'] ?? null,
                'expiry_date' => $data['expiry_date'] ?? null,
                'received_at' => $data['received_at'] ?? now()->toDateString(),
                'quantity_initial' => $quantity,
                'quantity_available' => $quantity,
                'unit_cost' => $unitCost,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'notes' => $data['notes'] ?? null,
            ]);

            if (!$referenceId) {
                $referenceId = $batch->id;
            }

            InventoryMovement::query()->create([
                'tenant_id' => $data['tenant_id'] ?? null,
                'inventory_product_id' => $data['inventory_product_id'],
                'inventory_batch_id' => $batch->id,
                'movement_type' => 'in',
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $unitCost !== null ? round($quantity * $unitCost, 2) : null,
                'movement_date' => $data['received_at'] ?? now()->toDateString(),
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $data['notes'] ?? null,
            ]);

            return $batch;
        });
    }

    private function normalizeNumericReference(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return null;
    }
}
