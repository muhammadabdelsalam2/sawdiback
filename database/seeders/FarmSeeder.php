<?php

namespace Database\Seeders;

use App\Models\Farm;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class FarmSeeder extends Seeder
{
    public function run(): void
    {
        $farms = [
            ['name' => 'سويحان', 'type' => 'owned', 'location' => 'سويحان'],
            ['name' => 'الهير', 'type' => 'owned', 'location' => 'الهير'],
            ['name' => 'إيجار', 'type' => 'rented', 'location' => null],
            ['name' => 'ملك', 'type' => 'owned', 'location' => null],
        ];

        Tenant::query()->each(function (Tenant $tenant) use ($farms): void {
            foreach ($farms as $farm) {
                Farm::withoutGlobalScopes()->firstOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'name' => $farm['name'],
                    ],
                    [
                        'type' => $farm['type'],
                        'location' => $farm['location'],
                        'is_active' => true,
                    ]
                );
            }
        });
    }
}
