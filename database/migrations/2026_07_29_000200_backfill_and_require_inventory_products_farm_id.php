<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $requiredFarms = [
        ['name' => 'سويحان', 'type' => 'owned', 'location' => 'سويحان'],
        ['name' => 'الهير', 'type' => 'owned', 'location' => 'الهير'],
        ['name' => 'إيجار', 'type' => 'rented', 'location' => null],
        ['name' => 'ملك', 'type' => 'owned', 'location' => null],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('inventory_products') || ! Schema::hasColumn('inventory_products', 'farm_id')) {
            return;
        }

        $this->ensureFarmsForProductTenants();
        $this->backfillNullFarmIds();
        $this->makeFarmIdNotNullable();
    }

    public function down(): void
    {
        if (! Schema::hasTable('inventory_products') || ! Schema::hasColumn('inventory_products', 'farm_id')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE inventory_products DROP FOREIGN KEY inventory_products_farm_id_foreign');
            DB::statement('ALTER TABLE inventory_products MODIFY farm_id BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE inventory_products ADD CONSTRAINT inventory_products_farm_id_foreign FOREIGN KEY (farm_id) REFERENCES farms(id) ON UPDATE CASCADE ON DELETE SET NULL');
        }
    }

    private function ensureFarmsForProductTenants(): void
    {
        $tenantIds = DB::table('inventory_products')
            ->whereNull('farm_id')
            ->whereNotNull('tenant_id')
            ->distinct()
            ->pluck('tenant_id');

        foreach ($tenantIds as $tenantId) {
            foreach ($this->requiredFarms as $farm) {
                $exists = DB::table('farms')
                    ->where('tenant_id', $tenantId)
                    ->where('name', $farm['name'])
                    ->exists();

                if (! $exists) {
                    DB::table('farms')->insert([
                        'tenant_id' => $tenantId,
                        'name' => $farm['name'],
                        'type' => $farm['type'],
                        'location' => $farm['location'],
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    private function backfillNullFarmIds(): void
    {
        DB::table('inventory_products')
            ->whereNull('farm_id')
            ->whereNotNull('tenant_id')
            ->orderBy('tenant_id')
            ->select('tenant_id')
            ->distinct()
            ->chunk(100, function ($rows): void {
                foreach ($rows as $row) {
                    $farmId = DB::table('farms')
                        ->where('tenant_id', $row->tenant_id)
                        ->orderByRaw("CASE WHEN name = 'ملك' THEN 0 ELSE 1 END")
                        ->orderBy('id')
                        ->value('id');

                    if ($farmId) {
                        DB::table('inventory_products')
                            ->where('tenant_id', $row->tenant_id)
                            ->whereNull('farm_id')
                            ->update([
                                'farm_id' => $farmId,
                                'updated_at' => now(),
                            ]);
                    }
                }
            });
    }

    private function makeFarmIdNotNullable(): void
    {
        if (DB::table('inventory_products')->whereNull('farm_id')->exists()) {
            throw new RuntimeException('Cannot make inventory_products.farm_id NOT NULL while null values remain.');
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE inventory_products DROP FOREIGN KEY inventory_products_farm_id_foreign');
            DB::statement('ALTER TABLE inventory_products MODIFY farm_id BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE inventory_products ADD CONSTRAINT inventory_products_farm_id_foreign FOREIGN KEY (farm_id) REFERENCES farms(id) ON UPDATE CASCADE');
        }
    }
};
