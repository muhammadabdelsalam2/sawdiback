<?php

namespace Database\Seeders;

use App\Models\AnimalBirth;
use App\Models\ReproductionCycle;
use Illuminate\Database\Seeder;

class AnimalBirthsSeeder extends Seeder
{
    public function run(): void
    {
        $cycles = ReproductionCycle::withoutGlobalScopes()->where('status', 'delivered')->get();

        foreach ($cycles as $cycle) {
            AnimalBirth::withoutGlobalScopes()->updateOrCreate(
                [
                    'tenant_id' => $cycle->tenant_id,
                    'mother_id' => $cycle->female_animal_id,
                    'reproduction_cycle_id' => $cycle->id,
                ],
                [
                    'tenant_id' => $cycle->tenant_id,
                    'mother_id' => $cycle->female_animal_id,
                    'reproduction_cycle_id' => $cycle->id,
                    'birth_date' => optional($cycle->expected_delivery_date)->toDateString() ?? now()->subDays(7)->toDateString(),
                    'complications' => null,
                    'notes' => 'Seeded birth record',
                ]
            );
        }
    }
}
