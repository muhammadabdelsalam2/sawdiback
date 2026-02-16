<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('animal_species', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->string('code');
            $table->string('name');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['tenant_id', 'code']);
        });

        Schema::create('animal_breeds', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('species_id')->constrained('animal_species')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['tenant_id', 'species_id', 'name']);
        });

        Schema::create('livestock_animals', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->string('tag_number');
            $table->foreignId('species_id')->constrained('animal_species')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('breed_id')->nullable()->constrained('animal_breeds')->cascadeOnUpdate()->nullOnDelete();
            $table->enum('gender', ['male', 'female']);
            $table->date('birth_date')->nullable();
            $table->enum('source_type', ['born', 'purchased']);
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->enum('status', ['active', 'sold', 'dead', 'slaughtered']);
            $table->enum('health_status', ['healthy', 'under_treatment', 'quarantined']);
            $table->foreignId('mother_id')->nullable()->constrained('livestock_animals')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('father_id')->nullable()->constrained('livestock_animals')->cascadeOnUpdate()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['tenant_id', 'tag_number']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'health_status']);
        });

        Schema::create('animal_health_records', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('animal_id')->constrained('livestock_animals')->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('record_type', ['checkup', 'illness', 'injury']);
            $table->text('diagnosis');
            $table->text('treatment');
            $table->unsignedBigInteger('vet_employee_id')->nullable();
            $table->decimal('cost', 12, 2)->nullable();
            $table->date('next_followup_date')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['tenant_id', 'next_followup_date']);
        });

        Schema::create('vaccines', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->string('name');
            $table->integer('default_interval_days')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['tenant_id', 'name']);
        });

        Schema::create('animal_vaccinations', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('animal_id')->constrained('livestock_animals')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('vaccine_id')->constrained('vaccines')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('dose_number');
            $table->date('vaccination_date');
            $table->date('next_due_date')->nullable()->index();
            $table->unsignedBigInteger('administered_by_employee_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['tenant_id', 'next_due_date']);
        });

        Schema::create('reproduction_cycles', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('female_animal_id')->constrained('livestock_animals')->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('heat_date')->nullable();
            $table->date('insemination_date')->nullable();
            $table->enum('insemination_type', ['natural', 'artificial']);
            $table->foreignId('male_animal_id')->nullable()->constrained('livestock_animals')->cascadeOnUpdate()->nullOnDelete();
            $table->boolean('pregnancy_confirmed')->default(false);
            $table->date('pregnancy_check_date')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->enum('status', ['open', 'pregnant', 'failed', 'delivered']);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'expected_delivery_date']);
        });

        Schema::create('animal_births', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('mother_id')->constrained('livestock_animals')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('reproduction_cycle_id')->nullable()->constrained('reproduction_cycles')->cascadeOnUpdate()->nullOnDelete();
            $table->date('birth_date');
            $table->text('complications')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['tenant_id', 'birth_date']);
        });

        Schema::create('birth_offspring', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('birth_id')->constrained('animal_births')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('offspring_animal_id')->constrained('livestock_animals')->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('birth_weight', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['tenant_id', 'birth_id', 'offspring_animal_id']);
        });

        Schema::create('milk_production_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('animal_id')->constrained('livestock_animals')->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('production_date');
            $table->decimal('quantity_liters', 8, 2);
            $table->decimal('fat_percentage', 5, 2)->nullable();
            $table->string('quality_grade')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['tenant_id', 'production_date']);
            $table->unique(['tenant_id', 'animal_id', 'production_date']);
        });

        Schema::create('feed_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->string('name');
            $table->enum('category', ['concentrate', 'roughage', 'supplement']);
            $table->string('unit');
            $table->decimal('cost_per_unit', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['tenant_id', 'name']);
        });

        Schema::create('animal_feeding_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('animal_id')->constrained('livestock_animals')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('feed_type_id')->constrained('feed_types')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('feeding_date');
            $table->decimal('quantity', 8, 2);
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->decimal('total_cost', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['tenant_id', 'feeding_date']);
        });

        Schema::create('animal_weight_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('animal_id')->constrained('livestock_animals')->cascadeOnUpdate()->cascadeOnDelete();
            $table->dateTime('recorded_at');
            $table->decimal('weight', 8, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['tenant_id', 'animal_id', 'recorded_at']);
        });

        Schema::create('animal_status_history', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('animal_id')->constrained('livestock_animals')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('old_status');
            $table->string('new_status');
            $table->text('change_reason')->nullable();
            $table->dateTime('changed_at');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['tenant_id', 'changed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animal_status_history');
        Schema::dropIfExists('animal_weight_logs');
        Schema::dropIfExists('animal_feeding_logs');
        Schema::dropIfExists('feed_types');
        Schema::dropIfExists('milk_production_logs');
        Schema::dropIfExists('birth_offspring');
        Schema::dropIfExists('animal_births');
        Schema::dropIfExists('reproduction_cycles');
        Schema::dropIfExists('animal_vaccinations');
        Schema::dropIfExists('vaccines');
        Schema::dropIfExists('animal_health_records');
        Schema::dropIfExists('livestock_animals');
        Schema::dropIfExists('animal_breeds');
        Schema::dropIfExists('animal_species');
    }
};
