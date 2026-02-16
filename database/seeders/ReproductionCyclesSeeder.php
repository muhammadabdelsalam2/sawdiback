<?php

namespace Database\Seeders;

use App\Models\LivestockAnimal;
use App\Models\ReproductionCycle;
use Illuminate\Database\Seeder;

class ReproductionCyclesSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = LivestockAnimal::withoutGlobalScopes()->select('tenant_id')->distinct()->pluck('tenant_id');

        foreach ($tenants as $tenantId) {
            $female = LivestockAnimal::withoutGlobalScopes()
                ->where('tenant_id', $tenantId)
                ->where('gender', 'female')
                ->first();

            $male = LivestockAnimal::withoutGlobalScopes()
                ->where('tenant_id', $tenantId)
                ->where('gender', 'male')
                ->first();

            if (!$female) {
                continue;
            }

            $inseminationDate = now()->subMonths(9)->toDateString();

            ReproductionCycle::withoutGlobalScopes()->updateOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'female_animal_id' => $female->id,
                    'insemination_date' => $inseminationDate,
                ],
                [
                    'tenant_id' => $tenantId,
                    'female_animal_id' => $female->id,
                    'heat_date' => now()->subMonths(9)->subDays(1)->toDateString(),
                    'insemination_date' => $inseminationDate,
                    'insemination_type' => $male ? 'natural' : 'artificial',
                    'male_animal_id' => $male?->id,
                    'pregnancy_confirmed' => true,
                    'pregnancy_check_date' => now()->subMonths(8)->toDateString(),
                    'expected_delivery_date' => now()->subWeeks(1)->toDateString(),
                    'status' => 'delivered',
                    'notes' => 'Seeded reproduction cycle',
                ]
            );
        }
    }
}
