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
        Schema::create('goods_receipt_items', function (Blueprint $table) {
            /**
             * UUID Primary Key
             */
            $table->uuid('id')
                ->primary()
                ->comment('Primary UUID for goods receipt item');

            /**
             * Parent Goods Receipt
             */
            $table->foreignUuid('goods_receipt_id')
                ->constrained('goods_receipts')
                ->cascadeOnDelete()
                // ->index()
                ->comment('Related goods receipt document');

            /**
             * Product reference
             */
            $table->foreignId('product_id')
                ->constrained('inventory_products')
                ->cascadeOnDelete()
                ->comment('Received product');

            /**
             * Quantity received
             */
            $table->decimal('quantity', 12, 2)
                ->comment('Quantity received from supplier');

            /**
             * Timestamps
             */
            $table->timestamps();

            /**
             * Prevent duplicate product per GRN
             */
            $table->unique(
                ['goods_receipt_id', 'product_id'],
                'grn_product_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipt_items');
    }
};
