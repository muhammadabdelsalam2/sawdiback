<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plus_subscription_categories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('plus_subscription_id')
                ->constrained('plus_subscriptions')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['plus_subscription_id', 'category_id'], 'plus_subscription_category_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plus_subscription_categories');
    }
};
