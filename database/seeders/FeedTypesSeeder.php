<?php

namespace Database\Seeders;

use App\Models\FeedType;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class FeedTypesSeeder extends Seeder
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
            ['tenant_id' => $tenantA, 'name' => 'Corn Silage', 'category' => 'roughage', 'unit' => 'kg', 'cost_per_unit' => 0.35, 'notes' => null],
            ['tenant_id' => $tenantA, 'name' => 'Protein Mix', 'category' => 'concentrate', 'unit' => 'kg', 'cost_per_unit' => 0.80, 'notes' => null],
            ['tenant_id' => $tenantA, 'name' => 'Mineral Block', 'category' => 'supplement', 'unit' => 'piece', 'cost_per_unit' => 2.50, 'notes' => null],
        ];

        if ($tenantB !== $tenantA) {
            $rows[] = ['tenant_id' => $tenantB, 'name' => 'Alfalfa Hay', 'category' => 'roughage', 'unit' => 'kg', 'cost_per_unit' => 0.42, 'notes' => null];
            $rows[] = ['tenant_id' => $tenantB, 'name' => 'Grain Blend', 'category' => 'concentrate', 'unit' => 'kg', 'cost_per_unit' => 0.90, 'notes' => null];
        }

        foreach ($rows as $row) {
            FeedType::withoutGlobalScopes()->updateOrCreate(
                [
                    'tenant_id' => $row['tenant_id'],
                    'name' => $row['name'],
                ],
                $row
            );
        }
    }
}
