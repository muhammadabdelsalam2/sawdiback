<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();

            $table->string('label', 100)->nullable();
            $table->string('recipient_name', 255)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('address_line_1', 255);
            $table->string('address_line_2', 255)->nullable();
            $table->string('building', 100)->nullable();
            $table->string('floor', 100)->nullable();
            $table->string('apartment', 100)->nullable();
            $table->string('city', 150)->nullable();
            $table->string('country', 150)->nullable();
            $table->string('postal_code', 50)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_default')->default(false);

            $table->timestamps();

            $table->index(['user_id', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
