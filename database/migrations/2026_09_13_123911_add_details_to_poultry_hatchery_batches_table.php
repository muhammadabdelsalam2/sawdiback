<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('poultry_hatchery_batches', function (Blueprint $table) {
            $table->decimal('purchase_amount', 12, 2)->default(0)->after('batch_number');
            $table->string('breed_type', 100)->nullable()->after('purchase_amount');
            $table->string('source_type', 20)->default('farm')->after('breed_type'); // farm أو external
            $table->string('seller_phone', 50)->nullable()->after('source_type');
            $table->string('seller_location')->nullable()->after('seller_phone');
            $table->foreignId('pen_id')->nullable()->after('seller_location')->constrained('farm_pens')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('poultry_hatchery_batches', function (Blueprint $table) {
            $table->dropForeign(['pen_id']);
            $table->dropColumn([
                'purchase_amount',
                'breed_type',
                'source_type',
                'seller_phone',
                'seller_location',
                'pen_id',
            ]);
        });
    }
};