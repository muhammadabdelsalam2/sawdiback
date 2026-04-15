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
        Schema::create('goods_receipts', function (Blueprint $table) {
            /**
             * UUID Primary Key
             */
            $table->uuid('id')
                ->primary()
                ->comment('Primary UUID for goods receipt');

            /**
             * GRN Number (Human readable)
             * Example: GRN-2026-0001
             */
            $table->string('grn_number')
                ->unique()
                ->comment('Unique goods receipt number');

            /**
             * Related Purchase Order
             */
            $table->foreignUuid('purchase_order_id')
                ->constrained('purchase_orders')
                ->cascadeOnDelete()
                // ->index()
                ->comment('Related purchase order');

            /**
             * User who received the goods
             */
            $table->foreignId('received_by')
                ->constrained('users')
                ->comment('Warehouse user who received goods');

            /**
             * GRN status
             * partial → completed
             */
            $table->string('status')
                ->default('completed')
                ->index()
                ->comment('Receipt status: partial or completed');

            /**
             * Timestamps
             */
            $table->timestamps();

            /**
             * Soft delete (audit + traceability)
             */
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipts');
    }
};
