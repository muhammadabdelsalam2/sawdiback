<?php

namespace Database\Seeders;

use App\Models\AnimalVaccination;
use App\Models\LivestockAnimal;
use App\Models\Vaccine;
use Illuminate\Database\Seeder;

class AnimalVaccinationsSeeder extends Seeder
{
    public function run(): void
    {
        $vaccines = Vaccine::withoutGlobalScopes()->get()->groupBy('tenant_id');
        $animals = LivestockAnimal::withoutGlobalScopes()->get();

        foreach ($animals as $animal) {
            $tenantVaccines = $vaccines->get($animal->tenant_id, collect());
            $vaccine = $tenantVaccines->first();
            if (!$vaccine) {
                continue;
            }

            AnimalVaccination::withoutGlobalScopes()->updateOrCreate(
                [
                    'tenant_id' => $animal->tenant_id,
                    'animal_id' => $animal->id,
                    'vaccine_id' => $vaccine->id,
                    'dose_number' => 1,
                ],
                [
                    'tenant_id' => $animal->tenant_id,
                    'animal_id' => $animal->id,
                    'vaccine_id' => $vaccine->id,
                    'dose_number' => 1,
                    'vaccination_date' => now()->subDays(15)->toDateString(),
                    'next_due_date' => now()->addDays((int) ($vaccine->default_interval_days ?? 180))->toDateString(),
                    'administered_by_employee_id' => null,
                    'notes' => 'Seeded vaccination entry',
                ]
            );
        }
    }
}
