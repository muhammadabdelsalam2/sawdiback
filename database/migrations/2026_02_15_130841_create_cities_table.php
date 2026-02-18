<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();

            // Multi-tenant readiness
            $table->unsignedBigInteger('tenant_id')->index();

            $table->foreignId('country_id')
                ->constrained('countries')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('name');
            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();
            $table->softDeletes();

            // Avoid duplicates per tenant + country
            $table->unique(['tenant_id', 'country_id', 'name']);
            $table->index(['tenant_id', 'country_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
