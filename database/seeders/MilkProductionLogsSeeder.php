<?php

namespace Database\Seeders;

use App\Models\LivestockAnimal;
use App\Models\MilkProductionLog;
use Illuminate\Database\Seeder;

class MilkProductionLogsSeeder extends Seeder
{
    public function run(): void
    {
        $animals = LivestockAnimal::withoutGlobalScopes()->where('gender', 'female')->get();

        foreach ($animals as $animal) {
            for ($i = 0; $i < 3; $i++) {
                $date = now()->subDays($i)->toDateString();

                MilkProductionLog::withoutGlobalScopes()->updateOrCreate(
                    [
                        'tenant_id' => $animal->tenant_id,
                        'animal_id' => $animal->id,
                        'production_date' => $date,
                    ],
                    [
                        'tenant_id' => $animal->tenant_id,
                        'animal_id' => $animal->id,
                        'production_date' => $date,
                        'quantity_liters' => 10.00 + $i,
                        'fat_percentage' => 3.50 + ($i * 0.1),
                        'quality_grade' => 'A',
                        'notes' => 'Seeded milk production log',
                    ]
                );
            }
        }
    }
}
