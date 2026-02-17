<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Vaccine;
use Illuminate\Database\Seeder;

class VaccinesSeeder extends Seeder
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
            ['tenant_id' => $tenantA, 'name' => 'FMD', 'default_interval_days' => 180, 'notes' => 'Foot and mouth disease vaccine'],
            ['tenant_id' => $tenantA, 'name' => 'Brucellosis', 'default_interval_days' => 365, 'notes' => 'Annual dose'],
        ];

        if ($tenantB !== $tenantA) {
            $rows[] = ['tenant_id' => $tenantB, 'name' => 'FMD', 'default_interval_days' => 180, 'notes' => 'Foot and mouth disease vaccine'];
            $rows[] = ['tenant_id' => $tenantB, 'name' => 'Sheep Pox', 'default_interval_days' => 365, 'notes' => 'Recommended for sheep'];
        }

        foreach ($rows as $row) {
            Vaccine::withoutGlobalScopes()->updateOrCreate(
                [
                    'tenant_id' => $row['tenant_id'],
                    'name' => $row['name'],
                ],
                $row
            );
        }
    }
}
