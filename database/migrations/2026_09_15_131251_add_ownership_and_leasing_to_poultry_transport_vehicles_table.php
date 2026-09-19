<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('poultry_transport_vehicles', function (Blueprint $table) {
            $table->enum('ownership_type', ['owned', 'leased'])->default('owned')->after('farm_id'); // مملوكة أو مستأجرة
            $table->string('lessor_name', 150)->nullable()->after('ownership_type');                 // اسم المؤجر / الشركة المؤجرة
            $table->string('lessor_phone', 50)->nullable()->after('lessor_name');                    // هاتف المؤجر
            $table->decimal('lease_cost', 12, 2)->default(0)->after('lessor_phone');                 // تكلفة الإيجار
            $table->enum('lease_period', ['daily', 'monthly', 'per_trip'])->nullable()->after('lease_cost'); // دورية سداد الإيجار
            $table->date('lease_start_date')->nullable()->after('lease_period');                     // بداية عقد التأجير
            $table->date('lease_end_date')->nullable()->after('lease_start_date');                     // نهاية عقد التأجير
        });
    }

    public function down(): void
    {
        Schema::table('poultry_transport_vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'ownership_type',
                'lessor_name',
                'lessor_phone',
                'lease_cost',
                'lease_period',
                'lease_start_date',
                'lease_end_date',
            ]);
        });
    }
};