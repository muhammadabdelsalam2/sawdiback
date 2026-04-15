<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plus_subscriptions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tenant_id')->nullable();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('status')->default('active');
            $table->decimal('monthly_price', 10, 2)->default(0);
            $table->string('currency', 10)->default('AED');
            $table->string('frequency', 30);
            $table->json('delivery_days')->nullable();

            $table->date('starts_at');
            $table->dateTime('next_delivery_at')->nullable();
            $table->dateTime('next_billing_at')->nullable();

            $table->foreignId('user_address_id')
                ->nullable()
                ->constrained('user_addresses')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('user_payment_method_id')
                ->nullable()
                ->constrained('user_payment_methods')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->boolean('auto_renew')->default(true);
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('starts_at');
            $table->index('next_delivery_at');
            $table->index('next_billing_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plus_subscriptions');
    }
};
