<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();

            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();

            $table->date('day')->index();
            $table->timestamp('check_in_at')->nullable();
            $table->timestamp('check_out_at')->nullable();

            $table->timestamps();

            $table->unique(['employee_id', 'day']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
