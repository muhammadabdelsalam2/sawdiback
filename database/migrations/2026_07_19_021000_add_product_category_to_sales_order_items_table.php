<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('sales_order_items', 'product_category')) {
            Schema::table('sales_order_items', function (Blueprint $table): void {
                $table->string('product_category')->default('other')->after('product_id');
            });
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE sales_order_items SET product_category = 'other' WHERE product_category IS NULL OR product_category NOT IN ('eggs','chicken','vegetables','other')");
            DB::statement("ALTER TABLE sales_order_items MODIFY product_category ENUM('eggs','chicken','vegetables','other') NOT NULL DEFAULT 'other'");
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sales_order_items', 'product_category')) {
            Schema::table('sales_order_items', function (Blueprint $table): void {
                $table->dropColumn('product_category');
            });
        }
    }
};
