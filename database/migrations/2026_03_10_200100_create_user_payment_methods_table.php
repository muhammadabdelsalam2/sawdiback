<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();

            $table->string('brand', 50);
            $table->string('last_four', 4);
            $table->unsignedTinyInteger('expiry_month');
            $table->unsignedSmallInteger('expiry_year');
            $table->string('holder_name', 255)->nullable();
            $table->string('gateway', 100)->nullable();
            $table->string('gateway_reference', 191)->nullable();
            $table->boolean('is_default')->default(false);

            $table->timestamps();

            $table->index(['user_id', 'is_default']);
            $table->index(['gateway', 'gateway_reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_payment_methods');
    }
};
