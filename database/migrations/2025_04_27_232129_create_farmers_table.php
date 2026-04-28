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
        Schema::create('farmers', function (Blueprint $table) {
            // $table->id();
            // UUID for better security and to avoid enumeration
            $table->uuid('id')->primary();


            // Basic Info
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            // Business Info
            $table->string('farm_name')->nullable();
            $table->text('address')->nullable();

            // Optional: Link to user account if farmers can log in
            $table->unsignedBigInteger('user_id')->nullable();

            // ERP / Accounting
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->enum('balance_type', ['debit', 'credit'])->default('debit');

            // Link with accounting system
            $table->unsignedBigInteger('account_id')->nullable(); // chart of accounts

            // System
            $table->boolean('is_active')->default(true);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farmers');
    }
};
