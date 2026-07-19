<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table): void {
            $table->string('worker_number')->nullable()->after('job_title_id');
            $table->string('profession')->nullable()->after('worker_number');
            $table->enum('employment_status', ['active', 'on_leave', 'contract_ended'])->default('active')->after('profession');
            $table->date('passport_expiry_date')->nullable()->after('hire_date');
            $table->date('iqama_expiry_date')->nullable()->after('passport_expiry_date');
            $table->enum('operational_department', ['poultry', 'crops', 'livestock'])->nullable()->after('job_title_id');
            $table->index(['tenant_id', 'employment_status']);
            $table->index(['passport_expiry_date', 'iqama_expiry_date']);
        });

        Schema::create('employee_attachments', function (Blueprint $table): void {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('type', ['passport', 'iqama', 'identity']);
            $table->string('path');
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'employee_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_attachments');

        Schema::table('employees', function (Blueprint $table): void {
            $table->dropIndex(['tenant_id', 'employment_status']);
            $table->dropIndex(['passport_expiry_date', 'iqama_expiry_date']);
            $table->dropColumn([
                'worker_number',
                'profession',
                'employment_status',
                'passport_expiry_date',
                'iqama_expiry_date',
                'operational_department',
            ]);
        });
    }
};
