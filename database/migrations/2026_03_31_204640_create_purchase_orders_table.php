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
        Schema::create('purchase_orders', function (Blueprint $table) {
            /**
             * UUID Primary Key
             */
            $table->uuid('id')
                ->primary()
                ->comment('Primary UUID for purchase order');

            /**
             * Human readable PO number
             * Example: PO-2026-0001
             */
            $table->string('po_number')
                ->unique()
                ->comment('Unique purchase order number');

            /**
             * Supplier reference
             */
            $table->foreignUuid('supplier_id')
                ->constrained('suppliers')
                ->cascadeOnDelete()
                ->comment('Supplier receiving this PO');

            /**
             * PO lifecycle status
             * draft → confirmed → partially_received → received → closed
             */
            $table->string('status')
                ->default('draft')
                ->index()
                ->comment('Purchase order status lifecycle');

            /**
             * Financial totals
             */
            $table->decimal('total', 12, 2)
                ->default(0)
                ->comment('Subtotal before VAT');

            $table->decimal('vat', 12, 2)
                ->default(0)
                ->comment('VAT amount');

            $table->decimal('net_total', 12, 2)
                ->default(0)
                ->comment('Final total including VAT');

            /**
             * Timestamps
             */
            $table->timestamps();

            /**
             * Soft delete (ERP audit requirement)
             */
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
