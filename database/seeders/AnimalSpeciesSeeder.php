<?php

namespace Database\Seeders;

use App\Models\AnimalSpecies;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class AnimalSpeciesSeeder extends Seeder
{
    public function run(): void
    {
        $tenantIds = Tenant::query()->pluck('id')->values();
        if ($tenantIds->isEmpty()) {
            return;
        }

        $tenantA = $tenantIds->get(0);
        $tenantB = $tenantIds->get(1, $tenantA);

        $rows = [
            ['tenant_id' => $tenantA, 'code' => 'CATTLE', 'name' => 'Cattle'],
            ['tenant_id' => $tenantA, 'code' => 'GOAT', 'name' => 'Goat'],
        ];

        if ($tenantB !== $tenantA) {
            $rows[] = ['tenant_id' => $tenantB, 'code' => 'CATTLE', 'name' => 'Cattle'];
            $rows[] = ['tenant_id' => $tenantB, 'code' => 'SHEEP', 'name' => 'Sheep'];
        }

        foreach ($rows as $row) {
            AnimalSpecies::withoutGlobalScopes()->updateOrCreate(
                [
                    'tenant_id' => $row['tenant_id'],
                    'code' => $row['code'],
                ],
                $row
            );
        }
    }
}
