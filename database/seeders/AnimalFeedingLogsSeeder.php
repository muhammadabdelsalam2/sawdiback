<?php

namespace Database\Seeders;

use App\Models\AnimalFeedingLog;
use App\Models\FeedType;
use App\Models\LivestockAnimal;
use Illuminate\Database\Seeder;

class AnimalFeedingLogsSeeder extends Seeder
{
    public function run(): void
    {
        $feedTypes = FeedType::withoutGlobalScopes()->get()->groupBy('tenant_id');
        $animals = LivestockAnimal::withoutGlobalScopes()->get();

        foreach ($animals as $animal) {
            $feedType = $feedTypes->get($animal->tenant_id, collect())->first();
            if (!$feedType) {
                continue;
            }

            $quantity = 5.50;
            $unitCost = (float) ($feedType->cost_per_unit ?? 0.0);

            AnimalFeedingLog::withoutGlobalScopes()->updateOrCreate(
                [
                    'tenant_id' => $animal->tenant_id,
                    'animal_id' => $animal->id,
                    'feed_type_id' => $feedType->id,
                    'feeding_date' => now()->toDateString(),
                ],
                [
                    'tenant_id' => $animal->tenant_id,
                    'animal_id' => $animal->id,
                    'feed_type_id' => $feedType->id,
                    'feeding_date' => now()->toDateString(),
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'total_cost' => $unitCost > 0 ? round($quantity * $unitCost, 2) : null,
                    'notes' => 'Seeded feeding log',
                ]
            );
        }
    }
}
