<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class LivestockManagementSeeder extends Seeder
{
    public function run(): void
    {
        if (Tenant::query()->count() === 0) {
            Tenant::query()->create([
                'id' => (string) Str::uuid(),
                'name' => 'Livestock Tenant A',
                'slug' => 'livestock-tenant-a',
                'status' => 'active',
            ]);

            Tenant::query()->create([
                'id' => (string) Str::uuid(),
                'name' => 'Livestock Tenant B',
                'slug' => 'livestock-tenant-b',
                'status' => 'active',
            ]);
        }

        $this->call([
            AnimalSpeciesSeeder::class,
            AnimalBreedsSeeder::class,
            LivestockAnimalsSeeder::class,
            AnimalHealthRecordsSeeder::class,
            VaccinesSeeder::class,
            AnimalVaccinationsSeeder::class,
            ReproductionCyclesSeeder::class,
            AnimalBirthsSeeder::class,
            BirthOffspringSeeder::class,
            MilkProductionLogsSeeder::class,
            FeedTypesSeeder::class,
            AnimalFeedingLogsSeeder::class,
            AnimalWeightLogsSeeder::class,
            AnimalStatusHistorySeeder::class,
        ]);
    }
}
