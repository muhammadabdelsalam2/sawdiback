<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE inventory_products SET asset_category = 'other' WHERE asset_category IS NULL OR asset_category NOT IN ('feed','seed','equipment','other')");
            DB::statement("ALTER TABLE inventory_products MODIFY asset_category ENUM('feed','seed','equipment','other') NOT NULL DEFAULT 'other'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE inventory_products MODIFY asset_category VARCHAR(255) NULL');
        }
    }
};
