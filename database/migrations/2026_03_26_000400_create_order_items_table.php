<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('inventory_product_id')->nullable()->constrained('inventory_products')->nullOnDelete();
            $table->string('product_name', 255);
            $table->string('product_code', 120)->nullable();
            $table->string('unit', 50)->nullable();
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('line_total', 14, 2)->default(0);
            $table->json('snapshot')->nullable();
            $table->timestamps();

            $table->index(['order_id']);
            $table->index(['inventory_product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
