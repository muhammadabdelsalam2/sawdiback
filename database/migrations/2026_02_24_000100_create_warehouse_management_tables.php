<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_products', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->string('code')->nullable();
            $table->string('name');
            $table->enum('category', ['feed', 'vet_medicine', 'equipment', 'animal_product']);
            $table->string('unit');
            $table->boolean('track_expiry')->default(false);
            $table->decimal('low_stock_threshold', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['tenant_id', 'code']);
            $table->index(['tenant_id', 'category'], 'inv_prod_tenant_category_idx');
            $table->index(['tenant_id', 'name'], 'inv_prod_tenant_name_idx');
        });

        Schema::create('inventory_batches', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('inventory_product_id')->constrained('inventory_products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('batch_number');
            $table->date('production_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('received_at')->nullable();
            $table->decimal('quantity_initial', 12, 2);
            $table->decimal('quantity_available', 12, 2);
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['tenant_id', 'inventory_product_id', 'batch_number'], 'inv_batches_tenant_product_batch_unique');
            $table->index(['tenant_id', 'expiry_date'], 'inv_batches_tenant_expiry_idx');
            $table->index(['tenant_id', 'inventory_product_id'], 'inv_batches_tenant_product_idx');
        });

        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('inventory_product_id')->constrained('inventory_products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('inventory_batch_id')->nullable()->constrained('inventory_batches')->cascadeOnUpdate()->nullOnDelete();
            $table->enum('movement_type', ['in', 'out', 'adjustment']);
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->decimal('total_cost', 12, 2)->nullable();
            $table->date('movement_date');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['tenant_id', 'movement_date'], 'inv_mov_tenant_date_idx');
            $table->index(['tenant_id', 'inventory_product_id', 'movement_type'], 'inv_mov_tenant_prod_type_idx');
            $table->index(['reference_type', 'reference_id'], 'inv_mov_ref_idx');
        });

        Schema::create('inventory_production_records', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('inventory_product_id')->constrained('inventory_products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('livestock_animal_id')->nullable()->constrained('livestock_animals')->cascadeOnUpdate()->nullOnDelete();
            $table->date('production_date');
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['tenant_id', 'production_date'], 'inv_prodrec_tenant_date_idx');
            $table->index(['tenant_id', 'inventory_product_id'], 'inv_prodrec_tenant_product_idx');
        });

        Schema::create('inventory_deliveries', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->string('delivery_number');
            $table->string('customer_name')->nullable();
            $table->dateTime('delivered_at');
            $table->enum('status', ['draft', 'shipped', 'delivered', 'cancelled'])->default('delivered');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['tenant_id', 'delivery_number']);
            $table->index(['tenant_id', 'delivered_at'], 'inv_deliveries_tenant_date_idx');
        });

        Schema::create('inventory_delivery_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('inventory_delivery_id')->constrained('inventory_deliveries')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('inventory_product_id')->constrained('inventory_products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('inventory_batch_id')->nullable()->constrained('inventory_batches')->cascadeOnUpdate()->nullOnDelete();
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_price', 12, 2)->nullable();
            $table->decimal('total_price', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->index(['tenant_id', 'inventory_product_id'], 'inv_del_items_tenant_product_idx');
            $table->index(['tenant_id', 'inventory_batch_id'], 'inv_del_items_tenant_batch_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_delivery_items');
        Schema::dropIfExists('inventory_deliveries');
        Schema::dropIfExists('inventory_production_records');
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('inventory_batches');
        Schema::dropIfExists('inventory_products');
    }
};
