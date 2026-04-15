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
        Schema::create('rfqs', function (Blueprint $table) {

            /**
             * UUID Primary Key
             */
            $table->uuid('id')
                ->primary()
                ->comment('Primary UUID for RFQ');

            /**
             * RFQ Code (Human readable)
             * Example: RFQ-2026-0001
             */
            $table->string('code')
                ->unique()
                ->comment('Unique RFQ code');

            /**
             * Related Purchase Requisition
             */
            $table->foreignUuid('purchase_requisition_id')
                ->constrained('purchase_requisitions')
                ->cascadeOnDelete()
                ->comment('Source purchase requisition');

            /**
             * RFQ Status
             * open → sent → closed → awarded
             */
            $table->string('status')
                ->default('open')
                ->index()
                ->comment('RFQ lifecycle status');

            /**
             * Timestamps
             */
            $table->timestamps();

            /**
             * Soft delete (ERP audit safety)
             */
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfqs');
    }
};
