<?php

namespace Database\Seeders;

use App\Models\AnimalSpecies;
use App\Models\AnimalWeightLog;
use App\Models\LivestockAnimal;
use Illuminate\Database\Seeder;

class AnimalWeightLogsSeeder extends Seeder
{
    public function run(): void
    {
        $species = AnimalSpecies::withoutGlobalScopes()->get()->keyBy('id');
        $animals = LivestockAnimal::withoutGlobalScopes()->get();

        foreach ($animals as $animal) {
            $speciesCode = strtoupper((string) optional($species->get($animal->species_id))->code);
            $weight = match ($speciesCode) {
                'CATTLE' => 420.00,
                'GOAT' => 65.00,
                'SHEEP' => 78.00,
                default => 120.00,
            };

            AnimalWeightLog::withoutGlobalScopes()->updateOrCreate(
                [
                    'tenant_id' => $animal->tenant_id,
                    'animal_id' => $animal->id,
                    'recorded_at' => now()->subDay()->format('Y-m-d H:i:s'),
                ],
                [
                    'tenant_id' => $animal->tenant_id,
                    'animal_id' => $animal->id,
                    'recorded_at' => now()->subDay()->format('Y-m-d H:i:s'),
                    'weight' => $weight,
                    'notes' => 'Seeded weight log',
                ]
            );
        }
    }
}
