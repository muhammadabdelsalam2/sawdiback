<?php

namespace Database\Seeders;

use App\Models\AnimalBreed;
use App\Models\AnimalSpecies;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class AnimalBreedsSeeder extends Seeder
{
    public function run(): void
    {
        $tenantIds = Tenant::query()->pluck('id')->values();
        if ($tenantIds->isEmpty()) {
            return;
        }

        $tenantA = $tenantIds->get(0);
        $tenantB = $tenantIds->get(1, $tenantA);
        $species = AnimalSpecies::withoutGlobalScopes()->get()->keyBy(fn ($row) => $row->tenant_id . ':' . strtoupper($row->code));

        $rows = [
            ['tenant_id' => $tenantA, 'species_code' => 'CATTLE', 'name' => 'Holstein'],
            ['tenant_id' => $tenantA, 'species_code' => 'CATTLE', 'name' => 'Jersey'],
            ['tenant_id' => $tenantA, 'species_code' => 'GOAT', 'name' => 'Boer'],
        ];

        if ($tenantB !== $tenantA) {
            $rows[] = ['tenant_id' => $tenantB, 'species_code' => 'CATTLE', 'name' => 'Angus'];
            $rows[] = ['tenant_id' => $tenantB, 'species_code' => 'SHEEP', 'name' => 'Merino'];
        }

        foreach ($rows as $row) {
            $speciesId = optional($species->get($row['tenant_id'] . ':' . $row['species_code']))->id;
            if (!$speciesId) {
                continue;
            }

            AnimalBreed::withoutGlobalScopes()->updateOrCreate(
                [
                    'tenant_id' => $row['tenant_id'],
                    'species_id' => $speciesId,
                    'name' => $row['name'],
                ],
                [
                    'tenant_id' => $row['tenant_id'],
                    'species_id' => $speciesId,
                    'name' => $row['name'],
                ]
            );
        }
    }
}
