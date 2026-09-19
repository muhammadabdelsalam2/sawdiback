<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('poultry_transport_vehicles');

        Schema::create('poultry_transport_vehicles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->foreignId('farm_id')->nullable()->constrained('farms')->nullOnDelete();

            $table->string('plate_number', 50);          // رقم اللوحة / السيارة
            $table->string('driver_name', 150)->nullable(); // اسم السائق
            $table->string('driver_phone', 50)->nullable(); // هاتف السائق
            $table->unsignedInteger('capacity_birds')->default(0); // السعة الاستيعابية (عدد الطيور)
            $table->unsignedInteger('capacity_crates')->default(0); // السعة (عدد الأقفاص)
            $table->enum('status', ['available', 'on_trip', 'maintenance', 'out_of_service'])->default('available');
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status'], 'ptv_tenant_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poultry_transport_vehicles');
    }
};