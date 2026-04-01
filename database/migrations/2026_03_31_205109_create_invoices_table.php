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
        Schema::create('invoices', function (Blueprint $table) {
            /**
             * UUID Primary Key
             */
            $table->uuid('id')
                ->primary()
                ->comment('Primary UUID for invoice');

            /**
             * Human-readable invoice number
             */
            $table->string('number')
                ->unique()
                ->comment('Invoice reference number (system generated)');

            /**
             * Invoice type classification
             */
            $table->enum('type', [
                'purchase',
                'sale',
                'return_purchase',
                'return_sale'
            ])->comment('Defines invoice business direction');

            /**
             * Parties (nullable depending on type)
             */
            $table->foreignUuid('supplier_id')
                ->nullable()
                ->constrained('suppliers')
                ->nullOnDelete()
                ->comment('Supplier for purchase invoices');

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('sales_customers')
                ->nullOnDelete()
                ->comment('Customer for sales invoices');

            /**
             * Department ownership
             */
            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->cascadeOnDelete()
                ->comment('Owning department');

            /**
             * Financial breakdown
             */
            $table->decimal('subtotal', 12, 2)
                ->default(0)
                ->comment('Total before tax and discount');

            $table->decimal('tax', 12, 2)
                ->default(0)
                ->comment('Tax amount applied');

            $table->decimal('discount', 12, 2)
                ->default(0)
                ->comment('Discount applied');

            $table->decimal('total', 12, 2)
                ->default(0)
                ->comment('Final payable amount');

            /**
             * Invoice lifecycle status
             */
            $table->enum('status', [
                'draft',
                'posted',
                'paid',
                'cancelled'
            ])->default('draft')
                ->index()
                ->comment('Invoice workflow status');

            /**
             * Business date
             */
            $table->date('invoice_date')
                ->index()
                ->comment('Invoice issuance date');

            /**
             * Notes
             */
            $table->text('notes')
                ->nullable()
                ->comment('Internal or external remarks');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
