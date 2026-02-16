<?php

namespace Database\Seeders;

use App\Models\AnimalStatusHistory;
use App\Models\LivestockAnimal;
use Illuminate\Database\Seeder;

class AnimalStatusHistorySeeder extends Seeder
{
    public function run(): void
    {
        $animals = LivestockAnimal::withoutGlobalScopes()->get();

        foreach ($animals as $animal) {
            AnimalStatusHistory::withoutGlobalScopes()->updateOrCreate(
                [
                    'tenant_id' => $animal->tenant_id,
                    'animal_id' => $animal->id,
                    'changed_at' => now()->subMonths(2)->format('Y-m-d H:i:s'),
                ],
                [
                    'tenant_id' => $animal->tenant_id,
                    'animal_id' => $animal->id,
                    'old_status' => 'active',
                    'new_status' => $animal->status,
                    'change_reason' => 'Initial seeded lifecycle state',
                    'changed_at' => now()->subMonths(2)->format('Y-m-d H:i:s'),
                ]
            );
        }
    }
}
