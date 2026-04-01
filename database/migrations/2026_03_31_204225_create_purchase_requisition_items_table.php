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
        Schema::create('purchase_requisition_items', function (Blueprint $table) {
            /**
             * UUID Primary Key (ERP consistent design)
             */
            $table->uuid('id')
                ->primary()
                ->comment('Primary UUID for requisition item');

            /**
             * Purchase Requisition Header
             */
            $table->foreignUuid('purchase_requisition_id')
                ->constrained('purchase_requisitions')
                ->cascadeOnDelete() 
                ->comment('Parent purchase requisition');

            /**
             * Product reference
             */
            $table->foreignId('product_id')
                ->constrained('inventory_products')
                ->cascadeOnDelete()
                ->comment('Requested product');

            /**
             * Requested quantity
             */
            $table->decimal('quantity', 12, 2)
                ->comment('Requested quantity');

            /**
             * Estimated price at request time
             */
            $table->decimal('estimated_price', 12, 2)
                ->default(0)
                ->comment('Estimated unit price');

            $table->timestamps();

            /**
             * Prevent duplicate product per requisition
             */
            $table->unique(
                ['purchase_requisition_id', 'product_id'],
                'pr_item_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requisition_items');
    }
};
