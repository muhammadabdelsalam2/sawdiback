<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales_customers', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->string('name');
            $table->enum('type', ['trader', 'factory', 'shop'])->index();
            $table->string('phones', 255);
            $table->string('address', 500);
            $table->string('tax_number', 100)->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['tenant_id', 'type', 'status']);
        });

        Schema::create('sales_contracts', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('customer_id')->constrained('sales_customers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('contract_code', 100);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('payment_terms', 255);
            $table->decimal('credit_limit', 14, 2)->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active')->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['tenant_id', 'contract_code']);
            $table->index(['tenant_id', 'customer_id', 'status']);
            $table->index(['tenant_id', 'start_date', 'end_date']);
        });

        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->string('order_no', 100);
            $table->foreignId('customer_id')->constrained('sales_customers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('contract_id')->nullable()->constrained('sales_contracts')->cascadeOnUpdate()->nullOnDelete();
            $table->date('order_date');
            $table->enum('status', ['draft', 'confirmed', 'fulfilled', 'cancelled'])->default('draft')->index();
            $table->decimal('total', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['tenant_id', 'order_no']);
            $table->index(['tenant_id', 'customer_id', 'status', 'order_date']);
        });

        Schema::create('sales_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained('sales_orders')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedBigInteger('product_id')->index();
            $table->decimal('qty', 14, 3);
            $table->decimal('unit_price', 14, 2);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('line_total', 14, 2);
            $table->timestamps();

            $table->index(['sales_order_id', 'product_id']);
        });

        Schema::create('sales_shipments', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('sales_order_id')->constrained('sales_orders')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('shipment_no', 100);
            $table->string('shipping_company', 190);
            $table->string('tracking_no', 190)->nullable();
            $table->enum('status', ['pending', 'packed', 'shipped', 'delivered', 'returned'])->default('pending')->index();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['tenant_id', 'shipment_no']);
            $table->index(['tenant_id', 'status', 'shipped_at', 'delivered_at']);
        });

        Schema::create('sales_shipment_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('sales_shipments')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('from_status', 50)->nullable();
            $table->string('to_status', 50);
            $table->timestamp('changed_at');
            $table->foreignId('changed_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['shipment_id', 'changed_at']);
        });

        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->string('invoice_no', 100);
            $table->foreignId('sales_order_id')->constrained('sales_orders')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('sales_customers')->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('subtotal', 14, 2);
            $table->decimal('tax', 14, 2)->default(0);
            $table->decimal('total', 14, 2);
            $table->enum('status', ['unpaid', 'partially_paid', 'paid', 'void'])->default('unpaid')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['tenant_id', 'invoice_no']);
            $table->index(['tenant_id', 'status', 'invoice_date', 'due_date']);
        });

        Schema::create('sales_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('sales_invoices')->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('amount', 14, 2);
            $table->timestamp('paid_at');
            $table->enum('method', ['cash', 'bank', 'other']);
            $table->string('reference', 190)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['invoice_id', 'paid_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_payments');
        Schema::dropIfExists('sales_invoices');
        Schema::dropIfExists('sales_shipment_status_histories');
        Schema::dropIfExists('sales_shipments');
        Schema::dropIfExists('sales_order_items');
        Schema::dropIfExists('sales_orders');
        Schema::dropIfExists('sales_contracts');
        Schema::dropIfExists('sales_customers');
    }
};
