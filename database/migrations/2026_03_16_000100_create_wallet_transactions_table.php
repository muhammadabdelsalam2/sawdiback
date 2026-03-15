<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('wallets')->cascadeOnUpdate()->cascadeOnDelete();

            $table->string('type', 50);   // top_up | points_conversion
            $table->string('status', 50)->default('completed');

            $table->string('title', 255);
            $table->text('description')->nullable();

            $table->decimal('amount', 12, 2)->default(0);
            $table->bigInteger('points')->default(0);

            $table->string('reference_type', 100)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['wallet_id', 'type']);
            $table->index(['wallet_id', 'status']);
            $table->index(['wallet_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
