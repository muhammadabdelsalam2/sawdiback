<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plus_subscription_skips', function (Blueprint $table) {
            $table->id();

            $table->foreignId('plus_subscription_id')
                ->constrained('plus_subscriptions')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('action', 20)->default('skip_once');
            $table->date('scheduled_for')->nullable();
            $table->date('resume_at')->nullable();
            $table->string('reason', 255)->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['plus_subscription_id', 'action']);
            $table->index(['plus_subscription_id', 'scheduled_for']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plus_subscription_skips');
    }
};
