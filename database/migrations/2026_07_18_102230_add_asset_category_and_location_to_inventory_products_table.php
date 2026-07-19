<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_products', function (Blueprint $table) {
            $table->string('asset_category')->nullable()->after('category');
            $table->string('farm_location')->nullable()->after('asset_category');
            $table->foreignId('farm_id')->nullable()->after('farm_location')->constrained('farms')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inventory_products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('farm_id');
            $table->dropColumn(['asset_category', 'farm_location']);
        });
    }
};
