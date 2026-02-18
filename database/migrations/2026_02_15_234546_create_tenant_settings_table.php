<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tenant_settings', function (Blueprint $table) {
            $table->id();

            // Multi-tenant readiness
            $table->unsignedBigInteger('tenant_id')->unique();

            // Theme/UI settings (MVP)
            $table->boolean('rtl_enabled')->default(false);
            $table->string('app_name')->nullable();
            $table->string('primary_color', 20)->nullable(); // e.g. #16a34a

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_settings');
    }
};
