<?php

namespace App\Services\CropsFeed;

use App\Models\Crop;
use App\Models\CropFeedAllocation;
use App\Models\FeedStockMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AllocateCropToFeedService
{
    public function execute(array $data): CropFeedAllocation
    {
        return DB::transaction(function () use ($data) {
            $crop = Crop::query()->lockForUpdate()->findOrFail($data['crop_id']);
            $quantity = (float) $data['quantity_tons'];
            $available = (float) $crop->available_for_feed_tons;

            if ($available < $quantity) {
                throw new RuntimeException('Crop feed quantity exceeds available amount.');
            }

            $allocation = CropFeedAllocation::query()->create([
                'tenant_id' => $data['tenant_id'] ?? null,
                'crop_id' => $crop->id,
                'feed_type_id' => $data['feed_type_id'],
                'quantity_tons' => $quantity,
                'allocation_date' => $data['allocation_date'],
                'notes' => $data['notes'] ?? null,
            ]);

            $crop->update([
                'available_for_feed_tons' => round($available - $quantity, 2),
            ]);

            FeedStockMovement::query()->create([
                'tenant_id' => $data['tenant_id'] ?? null,
                'feed_type_id' => $data['feed_type_id'],
                'movement_type' => 'in',
                'quantity' => $quantity,
                'unit_cost' => null,
                'total_cost' => null,
                'movement_date' => $data['allocation_date'],
                'source_type' => 'crop_feed_allocation',
                'source_id' => $allocation->id,
                'notes' => $data['notes'] ?? null,
            ]);

            return $allocation;
        });
    }
}
