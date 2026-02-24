<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();

            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();

            $table->string('type', 50)->default('annual');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason')->nullable();

            $table->string('status', 20)->default('pending'); // pending|approved|rejected
            $table->timestamp('actioned_at')->nullable();
            $table->unsignedBigInteger('actioned_by')->nullable(); // user id

            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
