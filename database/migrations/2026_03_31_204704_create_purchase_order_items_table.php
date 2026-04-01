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
        Schema::create('purchase_order_items', function (Blueprint $table) {
            /**
             * UUID Primary Key
             */
            $table->uuid('id')
                ->primary()
                ->comment('Primary UUID for purchase order item');

            /**
             * Parent Purchase Order
             */
            $table->foreignUuid('purchase_order_id')
                ->constrained('purchase_orders')
                ->cascadeOnDelete()
                ->comment('Related purchase order');

            /**
             * Product reference
             */
            $table->foreignId('product_id')
                ->constrained('inventory_products')
                ->cascadeOnDelete()
                ->comment('Ordered product');

            /**
             * Ordered quantity
             */
            $table->decimal('quantity', 12, 2)
                ->comment('Total ordered quantity');

            /**
             * Unit price agreed in PO
             */
            $table->decimal('unit_price', 12, 2)
                ->comment('Agreed purchase order unit price');

            /**
             * Quantity received from supplier
             * Important for partial delivery tracking
             */
            $table->decimal('received_quantity', 12, 2)
                ->default(0)
                ->comment('Quantity received from supplier');

            /**
             * Line total (quantity × unit_price)
             */
            $table->decimal('total', 12, 2)
                ->comment('Total amount for this line item');

            /**
             * Timestamps
             */
            $table->timestamps();

            /**
             * Prevent duplicate product per PO
             */
            $table->unique(
                ['purchase_order_id', 'product_id'],
                'po_product_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
