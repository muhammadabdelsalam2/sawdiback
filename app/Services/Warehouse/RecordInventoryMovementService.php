<?php

namespace App\Services\Warehouse;

use App\Models\InventoryBatch;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RecordInventoryMovementService
{
    public function execute(array $data): void
    {
        DB::transaction(function () use ($data) {
            $quantity = (float) $data['quantity'];
            if ($quantity <= 0) {
                throw new RuntimeException('Quantity must be greater than zero.');
            }

            $unitCost = array_key_exists('unit_cost', $data) && $data['unit_cost'] !== null
                ? (float) $data['unit_cost']
                : null;

            if ($data['movement_type'] === 'in') {
                $batchId = $data['inventory_batch_id'] ?? null;
                if ($batchId) {
                    $batch = InventoryBatch::query()->lockForUpdate()->findOrFail($batchId);
                    $batch->update([
                        'quantity_available' => round((float) $batch->quantity_available + $quantity, 2),
                    ]);
                }

                InventoryMovement::query()->create([
                    'tenant_id' => $data['tenant_id'] ?? null,
                    'inventory_product_id' => $data['inventory_product_id'],
                    'inventory_batch_id' => $batchId,
                    'movement_type' => 'in',
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'total_cost' => $unitCost !== null ? round($unitCost * $quantity, 2) : null,
                    'movement_date' => $data['movement_date'],
                    'reference_type' => 'manual',
                    'reference_id' => null,
                    'notes' => $data['notes'] ?? null,
                ]);

                return;
            }

            if ($data['movement_type'] === 'out') {
                $batchId = $data['inventory_batch_id'] ?? null;
                if ($batchId) {
                    $this->deductFromBatch(
                        $batchId,
                        $data['inventory_product_id'],
                        $quantity,
                        $data['movement_date'],
                        $unitCost,
                        $data['notes'] ?? null,
                        $data['tenant_id'] ?? null
                    );
                } else {
                    $this->deductByFifo(
                        $data['inventory_product_id'],
                        $quantity,
                        $data['movement_date'],
                        $unitCost,
                        $data['notes'] ?? null,
                        $data['tenant_id'] ?? null
                    );
                }

                return;
            }

            InventoryMovement::query()->create([
                'tenant_id' => $data['tenant_id'] ?? null,
                'inventory_product_id' => $data['inventory_product_id'],
                'inventory_batch_id' => $data['inventory_batch_id'] ?? null,
                'movement_type' => 'adjustment',
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $unitCost !== null ? round($unitCost * $quantity, 2) : null,
                'movement_date' => $data['movement_date'],
                'reference_type' => 'manual_adjustment',
                'reference_id' => null,
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }

    private function deductFromBatch(
        int $batchId,
        int $productId,
        float $quantity,
        string $movementDate,
        ?float $unitCost,
        ?string $notes,
        ?string $tenantId
    ): void {
        $batch = InventoryBatch::query()
            ->where('inventory_product_id', $productId)
            ->lockForUpdate()
            ->findOrFail($batchId);

        if ((float) $batch->quantity_available < $quantity) {
            throw new RuntimeException('Insufficient stock in selected batch.');
        }

        $batch->update([
            'quantity_available' => round((float) $batch->quantity_available - $quantity, 2),
        ]);

        InventoryMovement::query()->create([
            'tenant_id' => $tenantId,
            'inventory_product_id' => $productId,
            'inventory_batch_id' => $batch->id,
            'movement_type' => 'out',
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'total_cost' => $unitCost !== null ? round($unitCost * $quantity, 2) : null,
            'movement_date' => $movementDate,
            'reference_type' => 'manual',
            'reference_id' => null,
            'notes' => $notes,
        ]);
    }

    private function deductByFifo(
        int $productId,
        float $quantity,
        string $movementDate,
        ?float $unitCost,
        ?string $notes,
        ?string $tenantId
    ): void {
        $remaining = $quantity;
        $batches = InventoryBatch::query()
            ->where('inventory_product_id', $productId)
            ->where('quantity_available', '>', 0)
            ->orderByRaw("COALESCE(expiry_date, '9999-12-31') asc")
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        $available = (float) $batches->sum('quantity_available');
        if ($available < $quantity) {
            throw new RuntimeException('Insufficient stock for this operation.');
        }

        foreach ($batches as $batch) {
            if ($remaining <= 0) {
                break;
            }

            $take = min((float) $batch->quantity_available, $remaining);
            if ($take <= 0) {
                continue;
            }

            $batch->update([
                'quantity_available' => round((float) $batch->quantity_available - $take, 2),
            ]);

            InventoryMovement::query()->create([
                'tenant_id' => $tenantId,
                'inventory_product_id' => $productId,
                'inventory_batch_id' => $batch->id,
                'movement_type' => 'out',
                'quantity' => $take,
                'unit_cost' => $unitCost,
                'total_cost' => $unitCost !== null ? round($unitCost * $take, 2) : null,
                'movement_date' => $movementDate,
                'reference_type' => 'manual_fifo',
                'reference_id' => null,
                'notes' => $notes,
            ]);

            $remaining = round($remaining - $take, 2);
        }
    }
}
