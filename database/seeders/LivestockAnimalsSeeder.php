<?php

namespace Database\Seeders;

use App\Models\AnimalBreed;
use App\Models\AnimalSpecies;
use App\Models\LivestockAnimal;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class LivestockAnimalsSeeder extends Seeder
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
        $breeds = AnimalBreed::withoutGlobalScopes()->get()->keyBy(fn ($row) => $row->tenant_id . ':' . strtoupper($row->name));

        $seedRows = [
            ['tenant_id' => $tenantA, 'tag_number' => 'T1-BULL-001', 'species_code' => 'CATTLE', 'breed_name' => 'Holstein', 'gender' => 'male', 'source_type' => 'purchased', 'purchase_price' => 4500, 'status' => 'active', 'health_status' => 'healthy'],
            ['tenant_id' => $tenantA, 'tag_number' => 'T1-COW-001', 'species_code' => 'CATTLE', 'breed_name' => 'Holstein', 'gender' => 'female', 'source_type' => 'purchased', 'purchase_price' => 3800, 'status' => 'active', 'health_status' => 'healthy'],
            ['tenant_id' => $tenantA, 'tag_number' => 'T1-COW-002', 'species_code' => 'CATTLE', 'breed_name' => 'Jersey', 'gender' => 'female', 'source_type' => 'purchased', 'purchase_price' => 3600, 'status' => 'active', 'health_status' => 'under_treatment'],
            ['tenant_id' => $tenantA, 'tag_number' => 'T1-GOAT-001', 'species_code' => 'GOAT', 'breed_name' => 'Boer', 'gender' => 'female', 'source_type' => 'purchased', 'purchase_price' => 900, 'status' => 'active', 'health_status' => 'healthy'],
            ['tenant_id' => $tenantB, 'tag_number' => 'T2-BULL-001', 'species_code' => 'CATTLE', 'breed_name' => 'Angus', 'gender' => 'male', 'source_type' => 'purchased', 'purchase_price' => 5000, 'status' => 'active', 'health_status' => 'healthy'],
            ['tenant_id' => $tenantB, 'tag_number' => 'T2-EWE-001', 'species_code' => 'SHEEP', 'breed_name' => 'Merino', 'gender' => 'female', 'source_type' => 'purchased', 'purchase_price' => 650, 'status' => 'active', 'health_status' => 'healthy'],
        ];

        foreach ($seedRows as $row) {
            $speciesId = optional($species->get($row['tenant_id'] . ':' . $row['species_code']))->id;
            if (!$speciesId) {
                continue;
            }

            $breedId = optional($breeds->get($row['tenant_id'] . ':' . strtoupper($row['breed_name'] ?? '')))->id;

            LivestockAnimal::withoutGlobalScopes()->updateOrCreate(
                [
                    'tenant_id' => $row['tenant_id'],
                    'tag_number' => $row['tag_number'],
                ],
                [
                    'tenant_id' => $row['tenant_id'],
                    'tag_number' => $row['tag_number'],
                    'species_id' => $speciesId,
                    'breed_id' => $breedId,
                    'gender' => $row['gender'],
                    'birth_date' => now()->subYears(rand(2, 5))->toDateString(),
                    'source_type' => $row['source_type'],
                    'purchase_date' => now()->subYears(rand(1, 3))->toDateString(),
                    'purchase_price' => $row['purchase_price'],
                    'status' => $row['status'],
                    'health_status' => $row['health_status'],
                    'notes' => 'Seeded adult animal',
                ]
            );
        }

        $parentMap = LivestockAnimal::withoutGlobalScopes()->get()->keyBy(fn ($animal) => $animal->tenant_id . ':' . $animal->tag_number);
        $calves = [
            ['tenant_id' => $tenantA, 'tag_number' => 'T1-CALF-001', 'species_code' => 'CATTLE', 'breed_name' => 'Holstein', 'gender' => 'female', 'mother_tag' => 'T1-COW-001', 'father_tag' => 'T1-BULL-001'],
            ['tenant_id' => $tenantB, 'tag_number' => 'T2-LAMB-001', 'species_code' => 'SHEEP', 'breed_name' => 'Merino', 'gender' => 'male', 'mother_tag' => 'T2-EWE-001', 'father_tag' => null],
        ];

        foreach ($calves as $row) {
            $speciesId = optional($species->get($row['tenant_id'] . ':' . $row['species_code']))->id;
            if (!$speciesId) {
                continue;
            }

            $breedId = optional($breeds->get($row['tenant_id'] . ':' . strtoupper($row['breed_name'] ?? '')))->id;
            $motherId = optional($parentMap->get($row['tenant_id'] . ':' . $row['mother_tag']))->id;
            $fatherId = $row['father_tag'] ? optional($parentMap->get($row['tenant_id'] . ':' . $row['father_tag']))->id : null;

            LivestockAnimal::withoutGlobalScopes()->updateOrCreate(
                [
                    'tenant_id' => $row['tenant_id'],
                    'tag_number' => $row['tag_number'],
                ],
                [
                    'tenant_id' => $row['tenant_id'],
                    'tag_number' => $row['tag_number'],
                    'species_id' => $speciesId,
                    'breed_id' => $breedId,
                    'gender' => $row['gender'],
                    'birth_date' => now()->subMonths(4)->toDateString(),
                    'source_type' => 'born',
                    'purchase_date' => null,
                    'purchase_price' => null,
                    'status' => 'active',
                    'health_status' => 'healthy',
                    'mother_id' => $motherId,
                    'father_id' => $fatherId,
                    'notes' => 'Seeded offspring animal',
                ]
            );
        }
    }
}
