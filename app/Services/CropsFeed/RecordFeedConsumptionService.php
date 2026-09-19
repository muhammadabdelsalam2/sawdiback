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
            $tenantId = $data['tenant_id'] ?? (string) auth()->user()->tenant_id;
            $feedType = FeedType::query()->where('tenant_id', $tenantId)->findOrFail($data['feed_type_id']);
            $quantity = (float) $data['quantity'];

            if ($quantity <= 0) {
                throw new RuntimeException(__('crops_feed.messages.validation.quantity_positive') ?? 'يجب أن تكون كمية الاستهلاك أكبر من الصفر.');
            }

            $stock = $this->stockService->stockOnHand($feedType->id);

            if ($stock < $quantity) {
                throw new RuntimeException(__('crops_feed.messages.validation.insufficient_stock') ?? 'رصيد العلف الحالي غير كافٍ لتسجيل هذا الاستهلاك.');
            }

            $unitCost = array_key_exists('unit_cost', $data) && $data['unit_cost'] !== null
                ? (float) $data['unit_cost']
                : (float) ($feedType->cost_per_unit ?? 0);

            $totalCost = round($quantity * $unitCost, 2);

            $consumption = FeedConsumption::query()->create([
                'tenant_id'        => $tenantId,
                'feed_type_id'     => $feedType->id,
                'target_section'   => $data['target_section'] ?? null,
                'pen_id'           => $data['pen_id'] ?? null,
                'animal_id'        => $data['animal_id'] ?? null,
                'group_name'       => $data['group_name'] ?? null,
                'consumption_date' => $data['consumption_date'],
                'quantity'         => $quantity,
                'unit_cost'        => $unitCost,
                'total_cost'       => $totalCost,
                'notes'            => $data['notes'] ?? null,
            ]);

            FeedStockMovement::query()->create([
                'tenant_id'     => $tenantId,
                'feed_type_id'  => $feedType->id,
                'movement_type' => 'out',
                'quantity'      => $quantity,
                'unit_cost'     => $unitCost,
                'total_cost'    => $totalCost,
                'movement_date' => $data['consumption_date'],
                'source_type'   => 'feed_consumption',
                'source_id'     => $consumption->id,
                'notes'         => $data['notes'] ?? ('استهلاك - ' . ($data['target_section'] ?? 'عام')),
            ]);

            return $consumption;
        });
    }
}
