<?php

namespace Database\Seeders;

use App\Models\AnimalBirth;
use App\Models\AnimalBreed;
use App\Models\AnimalFeedingLog;
use App\Models\AnimalHealthRecord;
use App\Models\AnimalSpecies;
use App\Models\AnimalStatusHistory;
use App\Models\AnimalVaccination;
use App\Models\AnimalWeightLog;
use App\Models\FeedType;
use App\Models\LivestockAnimal;
use App\Models\ReproductionCycle;
use App\Models\Tenant;
use App\Models\Vaccine;
use Illuminate\Database\Seeder;

class FullLivestockSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::query()->pluck('id')->values();
        if ($tenants->isEmpty()) {
            $this->command->warn('No tenants found. Skipping.');
            return;
        }

        foreach ($tenants as $tenantId) {
            $this->command->info("Seeding tenant: $tenantId");

            $species   = $this->seedSpecies($tenantId);
            $breeds    = $this->seedBreeds($tenantId, $species);
            $feedTypes = $this->seedFeedTypes($tenantId);
            $vaccines  = $this->seedVaccines($tenantId);
            $animals   = $this->seedAnimals($tenantId, $species, $breeds);

            $this->seedFeedingLogs($tenantId, $animals, $feedTypes);
            $this->seedHealthRecords($tenantId, $animals);
            $this->seedWeightLogs($tenantId, $animals);
            $this->seedVaccinations($tenantId, $animals, $vaccines);
            $this->seedStatusHistory($tenantId, $animals);
            $this->seedReproductionAndBirths($tenantId, $animals);
        }

        $this->command->info('FullLivestockSeeder done.');
    }

    // ─────────────────────────────────────────────
    // Species
    // ─────────────────────────────────────────────
    private function seedSpecies(string $tenantId): \Illuminate\Support\Collection
    {
        $data = [
            ['code' => 'CATTLE', 'name' => 'Cattle'],
            ['code' => 'GOAT',   'name' => 'Goat'],
            ['code' => 'SHEEP',  'name' => 'Sheep'],
            ['code' => 'CAMEL',  'name' => 'Camel'],
            ['code' => 'HORSE',  'name' => 'Horse'],
        ];

        foreach ($data as $row) {
            AnimalSpecies::withoutGlobalScopes()->updateOrCreate(
                ['tenant_id' => $tenantId, 'code' => $row['code']],
                ['tenant_id' => $tenantId, 'name' => $row['name'], 'code' => $row['code']]
            );
        }

        return AnimalSpecies::withoutGlobalScopes()->where('tenant_id', $tenantId)->get();
    }

    // ─────────────────────────────────────────────
    // Breeds
    // ─────────────────────────────────────────────
    private function seedBreeds(string $tenantId, $species): \Illuminate\Support\Collection
    {
        $byCode = $species->keyBy('code');

        $data = [
            ['species' => 'CATTLE', 'name' => 'Holstein'],
            ['species' => 'CATTLE', 'name' => 'Jersey'],
            ['species' => 'CATTLE', 'name' => 'Angus'],
            ['species' => 'CATTLE', 'name' => 'Friesian'],
            ['species' => 'GOAT',   'name' => 'Boer'],
            ['species' => 'GOAT',   'name' => 'Nubian'],
            ['species' => 'GOAT',   'name' => 'Alpine'],
            ['species' => 'SHEEP',  'name' => 'Merino'],
            ['species' => 'SHEEP',  'name' => 'Awassi'],
            ['species' => 'CAMEL',  'name' => 'Dromedary'],
            ['species' => 'HORSE',  'name' => 'Arabian'],
        ];

        foreach ($data as $row) {
            $speciesId = optional($byCode->get($row['species']))->id;
            if (!$speciesId) continue;

            AnimalBreed::withoutGlobalScopes()->updateOrCreate(
                ['tenant_id' => $tenantId, 'species_id' => $speciesId, 'name' => $row['name']],
                ['tenant_id' => $tenantId, 'species_id' => $speciesId, 'name' => $row['name']]
            );
        }

        return AnimalBreed::withoutGlobalScopes()->where('tenant_id', $tenantId)->get();
    }

    // ─────────────────────────────────────────────
    // Feed Types
    // ─────────────────────────────────────────────
    private function seedFeedTypes(string $tenantId): \Illuminate\Support\Collection
    {
        $data = [
            ['name' => 'Alfalfa Hay',      'unit' => 'kg',  'cost_per_unit' => 2.50,  'low_stock_threshold' => 100],
            ['name' => 'Corn Silage',       'unit' => 'kg',  'cost_per_unit' => 1.80,  'low_stock_threshold' => 200],
            ['name' => 'Wheat Bran',        'unit' => 'kg',  'cost_per_unit' => 1.20,  'low_stock_threshold' => 150],
            ['name' => 'Barley Grain',      'unit' => 'kg',  'cost_per_unit' => 2.00,  'low_stock_threshold' => 120],
            ['name' => 'Mineral Mix',       'unit' => 'kg',  'cost_per_unit' => 5.00,  'low_stock_threshold' => 50],
            ['name' => 'Protein Pellets',   'unit' => 'kg',  'cost_per_unit' => 3.50,  'low_stock_threshold' => 80],
        ];

        foreach ($data as $row) {
            FeedType::withoutGlobalScopes()->updateOrCreate(
                ['tenant_id' => $tenantId, 'name' => $row['name']],
                array_merge($row, ['tenant_id' => $tenantId])
            );
        }

        return FeedType::withoutGlobalScopes()->where('tenant_id', $tenantId)->get();
    }

    // ─────────────────────────────────────────────
    // Vaccines
    // ─────────────────────────────────────────────
    private function seedVaccines(string $tenantId): \Illuminate\Support\Collection
    {
        $data = [
            ['name' => 'FMD Vaccine',         'default_interval_days' => 180],
            ['name' => 'Brucellosis Vaccine',  'default_interval_days' => 365],
            ['name' => 'Anthrax Vaccine',      'default_interval_days' => 365],
            ['name' => 'Rabies Vaccine',       'default_interval_days' => 365],
            ['name' => 'PPR Vaccine',          'default_interval_days' => 180],
        ];

        foreach ($data as $row) {
            Vaccine::withoutGlobalScopes()->updateOrCreate(
                ['tenant_id' => $tenantId, 'name' => $row['name']],
                array_merge($row, ['tenant_id' => $tenantId])
            );
        }

        return Vaccine::withoutGlobalScopes()->where('tenant_id', $tenantId)->get();
    }

    // ─────────────────────────────────────────────
    // Animals
    // ─────────────────────────────────────────────
   private function seedAnimals(string $tenantId, $species, $breeds): \Illuminate\Support\Collection
{
    $byCode    = $species->keyBy('code');
    $bySpecies = $breeds->groupBy('species_id');

    $data = [
        ['tag' => 'TAG-001', 'species' => 'CATTLE', 'gender' => 'female', 'status' => 'active',      'health' => 'healthy',         'dob' => '2020-03-15', 'source' => 'born',      'price' => 0],
        ['tag' => 'TAG-002', 'species' => 'CATTLE', 'gender' => 'male',   'status' => 'active',      'health' => 'healthy',         'dob' => '2019-07-20', 'source' => 'purchased', 'price' => 3500],
        ['tag' => 'TAG-003', 'species' => 'GOAT',   'gender' => 'female', 'status' => 'active',      'health' => 'healthy',         'dob' => '2021-01-10', 'source' => 'born',      'price' => 0],
        ['tag' => 'TAG-004', 'species' => 'GOAT',   'gender' => 'male',   'status' => 'active',      'health' => 'healthy',         'dob' => '2021-05-05', 'source' => 'purchased', 'price' => 800],
        ['tag' => 'TAG-005', 'species' => 'SHEEP',  'gender' => 'female', 'status' => 'active',      'health' => 'healthy',         'dob' => '2022-02-14', 'source' => 'born',      'price' => 0],
        ['tag' => 'TAG-006', 'species' => 'SHEEP',  'gender' => 'male',   'status' => 'active',      'health' => 'healthy',         'dob' => '2021-11-30', 'source' => 'purchased', 'price' => 600],
        ['tag' => 'TAG-007', 'species' => 'CATTLE', 'gender' => 'female', 'status' => 'active',      'health' => 'under_treatment', 'dob' => '2020-08-22', 'source' => 'born',      'price' => 0],
        ['tag' => 'TAG-008', 'species' => 'CAMEL',  'gender' => 'male',   'status' => 'active',      'health' => 'healthy',         'dob' => '2018-04-01', 'source' => 'purchased', 'price' => 12000],
        ['tag' => 'TAG-009', 'species' => 'CAMEL',  'gender' => 'female', 'status' => 'active',      'health' => 'healthy',         'dob' => '2019-09-15', 'source' => 'purchased', 'price' => 10000],
        ['tag' => 'TAG-010', 'species' => 'HORSE',  'gender' => 'male',   'status' => 'active',      'health' => 'healthy',         'dob' => '2017-06-10', 'source' => 'purchased', 'price' => 25000],
        ['tag' => 'TAG-011', 'species' => 'CATTLE', 'gender' => 'female', 'status' => 'active',      'health' => 'healthy',         'dob' => '2021-03-18', 'source' => 'born',      'price' => 0],
        ['tag' => 'TAG-012', 'species' => 'CATTLE', 'gender' => 'male',   'status' => 'sold',        'health' => 'healthy',         'dob' => '2020-12-01', 'source' => 'born',      'price' => 0],
    ];

    foreach ($data as $row) {
        $sp = $byCode->get($row['species']);
        if (!$sp) continue;

        $breed = $bySpecies->get($sp->id)?->first();

        LivestockAnimal::withoutGlobalScopes()->updateOrCreate(
            ['tenant_id' => $tenantId, 'tag_number' => $row['tag']],
            [
                'tenant_id'      => $tenantId,
                'tag_number'     => $row['tag'],
                'species_id'     => $sp->id,
                'breed_id'       => $breed?->id,
                'gender'         => $row['gender'],
                'status'         => $row['status'],
                'health_status'  => $row['health'],
                'birth_date'     => $row['dob'],
                'source_type'    => $row['source'],
                'purchase_price' => $row['price'] > 0 ? $row['price'] : null,
                'purchase_date'  => $row['source'] === 'purchased' ? now()->subYear()->toDateString() : null,
                'notes'          => 'Seeded animal',
            ]
        );
    }

    return LivestockAnimal::withoutGlobalScopes()->where('tenant_id', $tenantId)->get();
}

    // ─────────────────────────────────────────────
    // Feeding Logs
    // ─────────────────────────────────────────────
    private function seedFeedingLogs(string $tenantId, $animals, $feedTypes): void
    {
        $feedList = $feedTypes->values();
        if ($feedList->isEmpty()) return;

        foreach ($animals as $i => $animal) {
            $feed = $feedList[$i % $feedList->count()];
            $qty  = round(rand(3, 15) + rand(0, 9) / 10, 1);
            $cost = (float) ($feed->cost_per_unit ?? 0);

            foreach (range(0, 4) as $daysAgo) {
                AnimalFeedingLog::withoutGlobalScopes()->updateOrCreate(
                    [
                        'tenant_id'    => $tenantId,
                        'animal_id'    => $animal->id,
                        'feed_type_id' => $feed->id,
                        'feeding_date' => now()->subDays($daysAgo)->toDateString(),
                    ],
                    [
                        'tenant_id'    => $tenantId,
                        'animal_id'    => $animal->id,
                        'feed_type_id' => $feed->id,
                        'feeding_date' => now()->subDays($daysAgo)->toDateString(),
                        'quantity'     => $qty,
                        'unit_cost'    => $cost,
                        'total_cost'   => $cost > 0 ? round($qty * $cost, 2) : null,
                        'notes'        => 'Seeded feeding log',
                    ]
                );
            }
        }
    }

    // ─────────────────────────────────────────────
    // Health Records
    // ─────────────────────────────────────────────
    private function seedHealthRecords(string $tenantId, $animals): void
    {
        $types = [
            ['type' => 'checkup',   'diagnosis' => 'Routine periodic checkup',      'treatment' => 'Vitamins and hydration',   'cost' => 120.00],
            ['type' => 'treatment', 'diagnosis' => 'Mild respiratory infection',     'treatment' => 'Antibiotics for 5 days',   'cost' => 250.00],
            ['type' => 'surgery',   'diagnosis' => 'Minor wound on left hind leg',   'treatment' => 'Stitching and dressing',   'cost' => 500.00],
        ];

        foreach ($animals as $i => $animal) {
            $t = $types[$i % count($types)];
            AnimalHealthRecord::withoutGlobalScopes()->updateOrCreate(
                ['tenant_id' => $tenantId, 'animal_id' => $animal->id, 'record_type' => $t['type']],
                [
                    'tenant_id'        => $tenantId,
                    'animal_id'        => $animal->id,
                    'record_type'      => $t['type'],
                    'diagnosis'        => $t['diagnosis'],
                    'treatment'        => $t['treatment'],
                    'cost'             => $t['cost'],
                    'next_followup_date'=> now()->addMonth()->toDateString(),
                ]
            );
        }
    }

    // ─────────────────────────────────────────────
    // Weight Logs
    // ─────────────────────────────────────────────
    private function seedWeightLogs(string $tenantId, $animals): void
    {
        $baseWeights = [
            'CATTLE' => 450, 'GOAT' => 60, 'SHEEP' => 55, 'CAMEL' => 550, 'HORSE' => 500,
        ];

        foreach ($animals as $animal) {
            $base = $baseWeights[$animal->species?->code ?? 'CATTLE'] ?? 100;

            foreach (range(0, 2) as $monthsAgo) {
                AnimalWeightLog::withoutGlobalScopes()->updateOrCreate(
                    [
                        'tenant_id'   => $tenantId,
                        'animal_id'   => $animal->id,
                        'recorded_at' => now()->subMonths($monthsAgo)->startOfMonth()->format('Y-m-d H:i:s'),
                    ],
                    [
                        'tenant_id'   => $tenantId,
                        'animal_id'   => $animal->id,
                        'weight'      => $base + rand(-10, 10),
                        'recorded_at' => now()->subMonths($monthsAgo)->startOfMonth()->format('Y-m-d H:i:s'),
                        'notes'       => 'Seeded weight log',
                    ]
                );
            }
        }
    }

    // ─────────────────────────────────────────────
    // Vaccinations
    // ─────────────────────────────────────────────
    private function seedVaccinations(string $tenantId, $animals, $vaccines): void
    {
        if ($vaccines->isEmpty()) return;

        foreach ($animals as $i => $animal) {
            $vaccine = $vaccines[$i % $vaccines->count()];

            AnimalVaccination::withoutGlobalScopes()->updateOrCreate(
                ['tenant_id' => $tenantId, 'animal_id' => $animal->id, 'vaccine_id' => $vaccine->id, 'dose_number' => 1],
                [
                    'tenant_id'        => $tenantId,
                    'animal_id'        => $animal->id,
                    'vaccine_id'       => $vaccine->id,
                    'dose_number'      => 1,
                    'vaccination_date' => now()->subDays(30)->toDateString(),
                    'next_due_date'    => now()->addDays((int)($vaccine->default_interval_days ?? 180))->toDateString(),
                    'notes'            => 'Seeded vaccination',
                ]
            );
        }
    }

    // ─────────────────────────────────────────────
    // Status History
    // ─────────────────────────────────────────────
    private function seedStatusHistory(string $tenantId, $animals): void
    {
        foreach ($animals as $animal) {
            AnimalStatusHistory::withoutGlobalScopes()->updateOrCreate(
                [
                    'tenant_id'  => $tenantId,
                    'animal_id'  => $animal->id,
                    'changed_at' => now()->subMonths(2)->format('Y-m-d H:i:s'),
                ],
                [
                    'tenant_id'     => $tenantId,
                    'animal_id'     => $animal->id,
                    'old_status'    => 'active',
                    'new_status'    => $animal->status,
                    'change_reason' => 'Initial seeded lifecycle state',
                    'changed_at'    => now()->subMonths(2)->format('Y-m-d H:i:s'),
                ]
            );
        }
    }

    // ─────────────────────────────────────────────
    // Reproduction Cycles & Births
    // ─────────────────────────────────────────────
    private function seedReproductionAndBirths(string $tenantId, $animals): void
    {
        $females = $animals->where('gender', 'female')->values();
        $males   = $animals->where('gender', 'male')->values();

        if ($females->isEmpty() || $males->isEmpty()) return;

        foreach ($females as $i => $female) {
            $male = $males[$i % $males->count()];

            $cycle = ReproductionCycle::withoutGlobalScopes()->updateOrCreate(
                [
                    'tenant_id'        => $tenantId,
                    'female_animal_id' => $female->id,
                ],
                [
                    'tenant_id'              => $tenantId,
                    'female_animal_id'       => $female->id,
                    'male_animal_id'         => $male->id,
                    'status'                 => 'delivered',
                    'cycle_start_date'       => now()->subMonths(10)->toDateString(),
                    'insemination_date'      => now()->subMonths(9)->toDateString(),
                    'pregnancy_confirmed_at' => now()->subMonths(8)->toDateString(),
                    'expected_delivery_date' => now()->subMonths(1)->toDateString(),
                    'notes'                  => 'Seeded reproduction cycle',
                ]
            );

            AnimalBirth::withoutGlobalScopes()->updateOrCreate(
                [
                    'tenant_id'              => $tenantId,
                    'mother_id'              => $female->id,
                    'reproduction_cycle_id'  => $cycle->id,
                ],
                [
                    'tenant_id'              => $tenantId,
                    'mother_id'              => $female->id,
                    'reproduction_cycle_id'  => $cycle->id,
                    'birth_date'             => now()->subMonths(1)->toDateString(),
                    'complications'          => null,
                    'notes'                  => 'Seeded birth record',
                ]
            );
        }
    }
}
