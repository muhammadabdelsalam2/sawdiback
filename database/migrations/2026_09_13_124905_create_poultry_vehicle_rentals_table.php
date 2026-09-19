<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('poultry_vehicle_rentals');

        Schema::create('poultry_vehicle_rentals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->foreignId('vehicle_id')->constrained('poultry_transport_vehicles')->cascadeOnDelete();

            $table->enum('rental_type', ['internal', 'external'])->default('external'); // استخدام داخلي أو تأجير للغير
            $table->string('customer_name', 150)->nullable(); // اسم المستأجر (للخارجي)
            $table->string('customer_phone', 50)->nullable(); // هاتف المستأجر

            $table->dateTime('started_at'); // بداية الرحلة / التأجير
            $table->dateTime('ended_at')->nullable(); // نهاية الرحلة

            $table->string('origin')->nullable();      // مكان الانطلاق
            $table->string('destination')->nullable(); // جهة الوصول

            // البيانات المالية للرحلة
            $table->decimal('rental_fee', 12, 2)->default(0);      // قيمة الإيجار المحصلة
            $table->decimal('fuel_cost', 12, 2)->default(0);       // تكلفة الوقود
            $table->decimal('driver_commission', 12, 2)->default(0); // عمولة / أجر السائق
            $table->decimal('other_expenses', 12, 2)->default(0);  // رسوم طرق أو مصاريف أخرى

            $table->enum('payment_status', ['pending', 'paid', 'partially_paid'])->default('pending');
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'vehicle_id', 'started_at'], 'pvr_tenant_veh_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poultry_vehicle_rentals');
    }
};