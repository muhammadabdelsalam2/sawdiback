<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('crops', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->string('name');
            $table->decimal('land_area', 12, 2);
            $table->date('planting_date');
            $table->decimal('yield_tons', 12, 2)->nullable();
            $table->decimal('available_for_feed_tons', 12, 2)->default(0);
            $table->decimal('sale_price_per_ton', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['tenant_id', 'name']);
            $table->index(['tenant_id', 'planting_date']);
        });

        Schema::create('crop_growth_stages', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('crop_id')->constrained('crops')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('stage_name');
            $table->date('recorded_on');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['tenant_id', 'recorded_on']);
        });

        Schema::create('crop_cost_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('crop_id')->constrained('crops')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('item');
            $table->decimal('amount', 12, 2);
            $table->date('cost_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['tenant_id', 'cost_date']);
        });

        Schema::create('feed_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('feed_type_id')->constrained('feed_types')->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('movement_type', ['in', 'out']);
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->decimal('total_cost', 12, 2)->nullable();
            $table->date('movement_date');
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['tenant_id', 'movement_date']);
            $table->index(['tenant_id', 'feed_type_id', 'movement_type']);
            $table->index(['source_type', 'source_id']);
        });

        Schema::create('feed_consumptions', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('feed_type_id')->constrained('feed_types')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('animal_id')->nullable()->constrained('livestock_animals')->cascadeOnUpdate()->nullOnDelete();
            $table->string('group_name')->nullable();
            $table->date('consumption_date');
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->decimal('total_cost', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['tenant_id', 'consumption_date']);
            $table->index(['tenant_id', 'animal_id']);
        });

        Schema::create('crop_feed_allocations', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('crop_id')->constrained('crops')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('feed_type_id')->constrained('feed_types')->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('quantity_tons', 12, 2);
            $table->date('allocation_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['tenant_id', 'allocation_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_feed_allocations');
        Schema::dropIfExists('feed_consumptions');
        Schema::dropIfExists('feed_stock_movements');
        Schema::dropIfExists('crop_cost_items');
        Schema::dropIfExists('crop_growth_stages');
        Schema::dropIfExists('crops');
    }
};
