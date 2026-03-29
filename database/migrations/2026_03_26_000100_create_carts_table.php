<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->nullable()->index();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('user_address_id')->nullable()->constrained('user_addresses')->nullOnDelete();
            $table->string('coupon_code', 50)->nullable();
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('shipping', 14, 2)->default(0);
            $table->decimal('vat', 14, 2)->default(0);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->boolean('weekly_delivery')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['user_id']);
            $table->index(['tenant_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
