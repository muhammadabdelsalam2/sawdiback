<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('farms', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->string('name');
            $table->enum('type', ['owned', 'rented']);
            $table->string('location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['tenant_id', 'name']);
        });

        Schema::create('farm_pens', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('pen_number', 100);
            $table->enum('type', ['livestock', 'poultry', 'mixed']);
            $table->unsignedInteger('capacity')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['tenant_id', 'farm_id', 'pen_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('farm_pens');
        Schema::dropIfExists('farms');
    }
};
