<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('poultry_transport_vehicles', function (Blueprint $table) {
            $table->string('tenant_id', 64)->change();
        });

        // ونفس الشيء لجدول rentals إن وُجد
        if (Schema::hasTable('poultry_vehicle_rentals')) {
            Schema::table('poultry_vehicle_rentals', function (Blueprint $table) {
                $table->string('tenant_id', 64)->change();
            });
        }
    }

    public function down(): void
    {
        //
    }
};