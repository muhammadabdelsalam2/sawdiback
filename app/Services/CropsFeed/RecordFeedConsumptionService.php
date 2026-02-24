<?php

namespace App\Services\CropsFeed;

use App\Models\FeedConsumption;
use App\Models\FeedStockMovement;
use App\Models\FeedType;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RecordFeedConsumptionService
{
    public function __construct(private readonly FeedStockService $stockService)
    {
    }

    public function execute(array $data): FeedConsumption
    {
        return DB::transaction(function () use ($data) {
            $feedType = FeedType::query()->findOrFail($data['feed_type_id']);
            $quantity = (float) $data['quantity'];
            $stock = $this->stockService->stockOnHand($feedType->id);

            if ($stock < $quantity) {
                throw new RuntimeException('Insufficient feed stock for this operation.');
            }

            $unitCost = array_key_exists('unit_cost', $data) && $data['unit_cost'] !== null
                ? (float) $data['unit_cost']
                : (float) ($feedType->cost_per_unit ?? 0);

            $totalCost = round($quantity * $unitCost, 2);

            $consumption = FeedConsumption::query()->create([
                'tenant_id' => $data['tenant_id'] ?? null,
                'feed_type_id' => $feedType->id,
                'animal_id' => $data['animal_id'] ?? null,
                'group_name' => $data['group_name'] ?? null,
                'consumption_date' => $data['consumption_date'],
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
                'notes' => $data['notes'] ?? null,
            ]);

            FeedStockMovement::query()->create([
                'tenant_id' => $data['tenant_id'] ?? null,
                'feed_type_id' => $feedType->id,
                'movement_type' => 'out',
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
                'movement_date' => $data['consumption_date'],
                'source_type' => 'feed_consumption',
                'source_id' => $consumption->id,
                'notes' => $data['notes'] ?? null,
            ]);

            return $consumption;
        });
    }
}
