<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('crops', function (Blueprint $table): void {
            $table->string('greenhouse_type')->nullable()->after('farm_id');
            $table->string('greenhouse_location')->nullable()->after('greenhouse_type');
            $table->enum('irrigation_type', ['towers', 'seedlings', 'ground'])->nullable()->after('greenhouse_location');
            $table->date('expected_harvest_date')->nullable()->after('planting_date');
            $table->decimal('water_cost', 12, 2)->default(0)->after('sale_price_per_ton');
            $table->decimal('labor_cost', 12, 2)->default(0)->after('water_cost');
            $table->decimal('wasted_tons', 12, 2)->default(0)->after('yield_tons');
        });

        Schema::create('crop_material_usages', function (Blueprint $table): void {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('crop_id')->constrained('crops')->cascadeOnDelete();
            $table->enum('material_type', ['fertilizer', 'vitamins', 'pesticide', 'other']);
            $table->string('name');
            $table->decimal('quantity', 12, 2)->nullable();
            $table->string('unit', 50)->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->date('used_on');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'crop_id', 'material_type']);
            $table->index(['used_on']);
        });

        Schema::create('crop_seedling_stocks', function (Blueprint $table): void {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->enum('item_type', ['seed', 'seedling']);
            $table->string('name');
            $table->decimal('quantity', 12, 2)->default(0);
            $table->string('unit', 50)->default('unit');
            $table->decimal('low_stock_threshold', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'item_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_seedling_stocks');
        Schema::dropIfExists('crop_material_usages');

        Schema::table('crops', function (Blueprint $table): void {
            $table->dropColumn([
                'greenhouse_type',
                'greenhouse_location',
                'irrigation_type',
                'expected_harvest_date',
                'water_cost',
                'labor_cost',
                'wasted_tons',
            ]);
        });
    }
};
