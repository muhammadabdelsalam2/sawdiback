<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->string('expense_no', 100);
            $table->date('expense_date');
            $table->decimal('amount', 14, 2);
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('expense_account_id')->constrained('accounts')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('payment_account_id')->constrained('accounts')->cascadeOnUpdate()->restrictOnDelete();
            $table->enum('payment_method', ['cash', 'bank', 'other'])->default('cash');
            $table->string('vendor_name', 190)->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('posted')->index();
            $table->string('source_type', 100)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unique(['tenant_id', 'expense_no']);
            $table->index(['tenant_id', 'expense_date', 'status']);
            $table->index(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
