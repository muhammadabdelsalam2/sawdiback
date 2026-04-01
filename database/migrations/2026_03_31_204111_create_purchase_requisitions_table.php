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
        Schema::create('purchase_requisitions', function (Blueprint $table) {
            $table->uuid('id')
                ->primary()
                ->comment('Primary UUID for purchase requisition');
            /**
             * Human readable code (PR-2026-0001)
             */
            $table->string('code')
                ->unique()
                ->comment('Unique purchase requisition code');

            /**
             * Department reference
             */
            $table->foreignId('department_id')
                ->constrained('departments')
                ->cascadeOnDelete()
                ->comment('Department that requested this purchase requisition');

            /**
             * Request creator user
             */
            $table->foreignId('requested_by')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('User who created the purchase requisition');

            /**
             * Workflow status
             */
            $table->string('status')
                ->default('pending')
                ->index()
                ->comment('Status: pending, approved, rejected, converted_to_po');

            /**
             * Internal notes
             */
            $table->text('notes')
                ->nullable()
                ->comment('Optional internal notes for procurement team');


            /**
             * Soft delete support
             */
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requisitions');
    }
};
