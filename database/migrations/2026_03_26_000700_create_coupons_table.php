<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->nullable()->index();
            $table->string('code', 50)->unique();
            $table->string('type', 20)->default('fixed'); // fixed|percent
            $table->decimal('value', 14, 2)->default(0);
            $table->decimal('min_subtotal', 14, 2)->nullable();
            $table->decimal('max_discount', 14, 2)->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
