<?php

namespace App\Services\Livestock;

use App\Models\AnimalFeedingLog;
use App\Models\FeedType;
use App\Models\LivestockAnimal;
use App\Models\LivestockPenFinancialEntry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RecordFeedingService
{
    public function execute(array $data): Collection
    {
        return DB::transaction(function () use ($data) {
            $tenantId = $data['tenant_id'] ?? (string) auth()->user()->tenant_id;

            $feedType = FeedType::query()
                ->where('tenant_id', $tenantId)
                ->findOrFail($data['feed_type_id']);

            $unitCost = array_key_exists('unit_cost', $data) && $data['unit_cost'] !== null
                ? (float) $data['unit_cost']
                : (float) ($feedType->cost_per_unit ?? 0);

            $animalIds = $data['animal_ids'] ?? [$data['animal_id']];
            $rows = collect();

            $animals = LivestockAnimal::query()
                ->where('tenant_id', $tenantId)
                ->whereIn('id', $animalIds)
                ->get()
                ->keyBy('id');

            foreach ($animalIds as $animalId) {
                $animal = $animals->get($animalId);
                if (!$animal) {
                    continue;
                }

                $quantity = (float) $data['quantity'];
                $totalCost = $unitCost > 0 ? round($quantity * $unitCost, 2) : 0;

                $log = AnimalFeedingLog::query()->create([
                    'tenant_id'    => $tenantId,
                    'animal_id'    => $animalId,
                    'feed_type_id' => $feedType->id,
                    'feeding_date' => $data['feeding_date'],
                    'quantity'     => $quantity,
                    'unit_cost'    => $unitCost,
                    'total_cost'   => $totalCost,
                    'notes'        => $data['notes'] ?? null,
                ]);

                // تسجيل التكلفة تلقائياً في الحظيرة كقيد مالي إذا كان الحيوان مخصصاً لحظيرة
                if ($animal->pen_id && $totalCost > 0) {
                    LivestockPenFinancialEntry::query()->create([
                        'tenant_id'  => $tenantId,
                        'pen_id'     => $animal->pen_id,
                        'type'       => 'feed_costs',
                        'amount'     => $totalCost,
                        'entry_date' => $data['feeding_date'],
                        'notes'      => 'تغذية الحيوان #' . ($animal->tag_number ?? $animal->id) . ' - ' . $feedType->name,
                    ]);
                }

                $rows->push($log);
            }

            return $rows;
        });
    }
}