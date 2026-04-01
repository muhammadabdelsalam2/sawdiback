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
        Schema::create('quotation_items', function (Blueprint $table) {

            /**
             * UUID Primary Key
             */
            $table->uuid('id')
                ->primary()
                ->comment('Primary UUID for quotation item');

            /**
             * Parent quotation
             */
            $table->foreignUuid('quotation_id')
                ->constrained('quotations')
                ->cascadeOnDelete()
                ->comment('Related supplier quotation');

            /**
             * Product reference
             */
            $table->foreignId('product_id')
                ->constrained('inventory_products')
                ->cascadeOnDelete()
                ->comment('Quoted product');

            /**
             * Quantity offered by supplier
             */
            $table->decimal('quantity', 12, 2)
                ->comment('Quantity quoted by supplier');

            /**
             * Unit price from supplier
             */
            $table->decimal('unit_price', 12, 2)
                ->comment('Supplier unit price');

            /**
             * Calculated line total (quantity × unit_price)
             */
            $table->decimal('total', 12, 2)
                ->comment('Line total amount');

            /**
             * Timestamps
             */
            $table->timestamps();

            /**
             * Prevent duplicate product per quotation
             */
            $table->unique(
                ['quotation_id', 'product_id'],
                'quotation_product_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};
