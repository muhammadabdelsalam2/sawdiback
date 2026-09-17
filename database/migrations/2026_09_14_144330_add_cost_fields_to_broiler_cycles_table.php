<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('poultry_broiler_cycles', function (Blueprint $table) {
            $table->decimal('chick_purchase_cost', 12, 2)->default(0)->after('chick_count');
            $table->decimal('electricity_cost', 12, 2)->default(0)->after('chick_purchase_cost');
            $table->decimal('water_cost', 12, 2)->default(0)->after('electricity_cost');
            $table->decimal('transport_cost', 12, 2)->default(0)->after('water_cost');
            $table->decimal('fuel_cost', 12, 2)->default(0)->after('transport_cost');
        });
    }

    public function down(): void
    {
        Schema::table('poultry_broiler_cycles', function (Blueprint $table) {
            $table->dropColumn([
                'chick_purchase_cost',
                'electricity_cost',
                'water_cost',
                'transport_cost',
                'fuel_cost',
            ]);
        });
    }
};