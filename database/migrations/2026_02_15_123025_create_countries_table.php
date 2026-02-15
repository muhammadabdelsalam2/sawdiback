<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();

            // Multi-tenant readiness
            $table->unsignedBigInteger('tenant_id')->index();

            $table->string('name');
            $table->string('iso2', 2)->nullable();
            $table->string('iso3', 3)->nullable();
            $table->string('phone_code', 10)->nullable();

            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();
            $table->softDeletes();

            // MVP guardrails (per tenant uniqueness)
            $table->unique(['tenant_id', 'name']);
            $table->unique(['tenant_id', 'iso2']);
            $table->unique(['tenant_id', 'iso3']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
