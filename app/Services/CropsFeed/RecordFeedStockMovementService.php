<?php

namespace App\Services\CropsFeed;

use App\Models\FeedStockMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RecordFeedStockMovementService
{
    public function __construct(private readonly FeedStockService $stockService)
    {
    }

    public function execute(array $data): FeedStockMovement
    {
        return DB::transaction(function () use ($data) {
            $quantity = (float) $data['quantity']; // تأكد من أن الكمية موجبة
            if ($quantity < 0) {
                throw new RuntimeException('Feed quantity must be a positive number.');
            }
            $unitCost = array_key_exists('unit_cost', $data) && $data['unit_cost'] !== null
                ? (float) $data['unit_cost']
                : null;
            $totalCost = $unitCost !== null ? round($unitCost * $quantity, 2) : null;

            if ($data['movement_type'] === 'out') {
                $stock = $this->stockService->stockOnHand((int) $data['feed_type_id']);
                if ($stock < $quantity) {
                    throw new RuntimeException('Insufficient feed stock for this operation.');
                }
            }

            return FeedStockMovement::query()->create([
                'tenant_id' => $data['tenant_id'] ?? null,
                'feed_type_id' => $data['feed_type_id'],
                'movement_type' => $data['movement_type'],
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
                'movement_date' => $data['movement_date'],
                'source_type' => 'manual',
                'source_id' => null,
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }
}
