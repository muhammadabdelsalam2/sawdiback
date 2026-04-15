<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->foreignUuid('rfq_id')
                ->nullable()
                ->after('supplier_id')
                ->constrained('rfqs')
                ->nullOnDelete();

            $table->foreignUuid('quotation_id')
                ->nullable()
                ->after('rfq_id')
                ->constrained('quotations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['quotation_id']);
            $table->dropColumn('quotation_id');

            $table->dropForeign(['rfq_id']);
            $table->dropColumn('rfq_id');
        });
    }
};
