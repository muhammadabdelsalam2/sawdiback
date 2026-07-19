<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('poultry_broiler_cycles', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->unsignedBigInteger('pen_id')->nullable()->index();
            $table->string('cycle_number', 100);
            $table->unsignedInteger('chick_count');
            $table->date('started_at');
            $table->enum('status', ['active', 'finished'])->default('active')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['tenant_id', 'cycle_number']);
        });

        Schema::create('poultry_broiler_mortalities', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('broiler_cycle_id')->constrained('poultry_broiler_cycles')->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('mortality_date');
            $table->unsignedInteger('quantity');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['tenant_id', 'mortality_date']);
        });

        Schema::create('poultry_broiler_sales', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('broiler_cycle_id')->constrained('poultry_broiler_cycles')->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('sale_date');
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_amount', 12, 2);
            $table->string('customer_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['tenant_id', 'sale_date']);
        });

        Schema::create('poultry_broiler_costs', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('broiler_cycle_id')->constrained('poultry_broiler_cycles')->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('cost_type', ['chicks_purchase', 'feed', 'slaughter_packaging']);
            $table->decimal('amount', 12, 2);
            $table->date('cost_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['tenant_id', 'cost_date']);
        });

        Schema::create('poultry_layer_flocks', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->unsignedBigInteger('pen_id')->nullable()->index();
            $table->string('flock_number', 100);
            $table->unsignedInteger('chicken_count');
            $table->decimal('purchase_cost', 12, 2);
            $table->date('started_at');
            $table->enum('status', ['active', 'finished'])->default('active')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['tenant_id', 'flock_number']);
        });

        Schema::create('poultry_layer_egg_production_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('layer_flock_id')->constrained('poultry_layer_flocks')->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('production_date');
            $table->unsignedInteger('eggs_count');
            $table->decimal('sale_price', 12, 2)->default(0);
            $table->decimal('daily_feed_cost', 12, 2)->default(0);
            $table->unsignedInteger('damaged_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['tenant_id', 'layer_flock_id', 'production_date'], 'poultry_layer_daily_unique');
        });

        Schema::create('poultry_layer_mortalities', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('layer_flock_id')->constrained('poultry_layer_flocks')->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('mortality_date');
            $table->unsignedInteger('quantity');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['tenant_id', 'mortality_date']);
        });

        Schema::create('poultry_hatchery_machines', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->string('machine_number', 100);
            $table->unsignedInteger('capacity');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['tenant_id', 'machine_number']);
        });

        Schema::create('poultry_hatchery_batches', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('hatchery_machine_id')->constrained('poultry_hatchery_machines')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('batch_number', 100);
            $table->date('loaded_at');
            $table->date('expected_hatch_at');
            $table->date('actual_hatch_at')->nullable();
            $table->unsignedInteger('eggs_loaded');
            $table->unsignedInteger('chicks_produced')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['tenant_id', 'batch_number']);
        });

        Schema::create('poultry_chicken_breeds', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->unsignedBigInteger('pen_id')->nullable()->index();
            $table->string('code', 100);
            $table->enum('breed_type', ['local', 'improved', 'broiler', 'layer']);
            $table->decimal('purchase_amount', 12, 2);
            $table->unsignedInteger('female_count');
            $table->unsignedInteger('male_count');
            $table->date('started_at');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['tenant_id', 'code']);
        });

        Schema::create('poultry_chicken_breed_egg_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('chicken_breed_id')->constrained('poultry_chicken_breeds')->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('production_date');
            $table->unsignedInteger('eggs_count');
            $table->unsignedInteger('fertilized_count')->default(0);
            $table->unsignedInteger('unfertilized_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['tenant_id', 'chicken_breed_id', 'production_date'], 'poultry_breed_daily_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poultry_chicken_breed_egg_logs');
        Schema::dropIfExists('poultry_chicken_breeds');
        Schema::dropIfExists('poultry_hatchery_batches');
        Schema::dropIfExists('poultry_hatchery_machines');
        Schema::dropIfExists('poultry_layer_mortalities');
        Schema::dropIfExists('poultry_layer_egg_production_logs');
        Schema::dropIfExists('poultry_layer_flocks');
        Schema::dropIfExists('poultry_broiler_costs');
        Schema::dropIfExists('poultry_broiler_sales');
        Schema::dropIfExists('poultry_broiler_mortalities');
        Schema::dropIfExists('poultry_broiler_cycles');
    }
};
