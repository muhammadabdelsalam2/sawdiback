<?php

namespace Database\Seeders;

use App\Models\AnimalHealthRecord;
use App\Models\LivestockAnimal;
use Illuminate\Database\Seeder;

class AnimalHealthRecordsSeeder extends Seeder
{
    public function run(): void
    {
        $animals = LivestockAnimal::withoutGlobalScopes()->get();

        foreach ($animals as $animal) {
            AnimalHealthRecord::withoutGlobalScopes()->updateOrCreate(
                [
                    'tenant_id' => $animal->tenant_id,
                    'animal_id' => $animal->id,
                    'record_type' => 'checkup',
                ],
                [
                    'tenant_id' => $animal->tenant_id,
                    'animal_id' => $animal->id,
                    'record_type' => 'checkup',
                    'diagnosis' => 'Routine periodic checkup',
                    'treatment' => 'Vitamins and hydration guidance',
                    'vet_employee_id' => null,
                    'cost' => 120.00,
                    'next_followup_date' => now()->addMonth()->toDateString(),
                ]
            );
        }
    }
}
