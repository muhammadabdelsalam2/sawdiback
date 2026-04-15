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
        Schema::create('quotations', function (Blueprint $table) {
            /**
             * UUID Primary Key
             */
            $table->uuid('id')
                ->primary()
                ->comment('Primary UUID for quotation');

            /**
             * RFQ reference
             */
            $table->foreignUuid('rfq_id')
                ->constrained('rfqs')
                ->cascadeOnDelete()
                ->comment('Related RFQ');

            /**
             * Supplier reference
             */
            $table->foreignUuid('supplier_id')
                ->constrained('suppliers')
                ->cascadeOnDelete()
                ->index()
                ->comment('Supplier submitting quotation');

            /**
             * Total quotation value
             */
            $table->decimal('total', 12, 2)
                ->default(0)
                ->comment('Total quotation amount');

            /**
             * Quotation status
             * submitted → selected → rejected
             */
            $table->string('status')
                ->default('submitted')
                ->index()
                ->comment('Quotation lifecycle status');

            /**
             * Timestamps
             */
            $table->timestamps();

            /**
             * Soft delete (ERP audit safety)
             */
            $table->softDeletes();

            /**
             * Prevent duplicate supplier per RFQ
             */
            $table->unique(['rfq_id', 'supplier_id'], 'rfq_supplier_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
