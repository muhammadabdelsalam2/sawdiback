<?php

namespace App\Services\Livestock;

use App\Models\AnimalFeedingLog;
use App\Models\FeedType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class RecordFeedingService
{
    public function execute(array $data): Collection
    {
        return DB::transaction(function () use ($data) {
            $feedType = FeedType::query()->findOrFail($data['feed_type_id']);
            $unitCost = array_key_exists('unit_cost', $data)
                ? $data['unit_cost']
                : $feedType->cost_per_unit;

            $animalIds = $data['animal_ids'] ?? [$data['animal_id']];
            $rows = collect();

            foreach ($animalIds as $animalId) {
                $quantity = (float) $data['quantity'];
                $totalCost = $unitCost !== null ? round($quantity * (float) $unitCost, 2) : null;

                $rows->push(AnimalFeedingLog::query()->create([
                    'tenant_id' => $data['tenant_id'] ?? null,
                    'animal_id' => $animalId,
                    'feed_type_id' => $feedType->id,
                    'feeding_date' => $data['feeding_date'],
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'total_cost' => $totalCost,
                    'notes' => $data['notes'] ?? null,
                ]));
            }

            return $rows;
        });
    }
}
