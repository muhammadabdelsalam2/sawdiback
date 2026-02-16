<?php

namespace Database\Seeders;

use App\Models\AnimalBirth;
use App\Models\BirthOffspring;
use App\Models\LivestockAnimal;
use Illuminate\Database\Seeder;

class BirthOffspringSeeder extends Seeder
{
    public function run(): void
    {
        $births = AnimalBirth::withoutGlobalScopes()->get();

        foreach ($births as $birth) {
            $offspring = LivestockAnimal::withoutGlobalScopes()
                ->where('tenant_id', $birth->tenant_id)
                ->where('mother_id', $birth->mother_id)
                ->whereDate('birth_date', $birth->birth_date)
                ->first();

            if (!$offspring) {
                $offspring = LivestockAnimal::withoutGlobalScopes()
                    ->where('tenant_id', $birth->tenant_id)
                    ->where('mother_id', $birth->mother_id)
                    ->orderByDesc('id')
                    ->first();
            }

            if (!$offspring) {
                continue;
            }

            BirthOffspring::withoutGlobalScopes()->updateOrCreate(
                [
                    'tenant_id' => $birth->tenant_id,
                    'birth_id' => $birth->id,
                    'offspring_animal_id' => $offspring->id,
                ],
                [
                    'tenant_id' => $birth->tenant_id,
                    'birth_id' => $birth->id,
                    'offspring_animal_id' => $offspring->id,
                    'birth_weight' => 28.50,
                    'notes' => 'Seeded offspring linkage',
                ]
            );
        }
    }
}
