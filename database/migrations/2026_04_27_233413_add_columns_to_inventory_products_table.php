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
        Schema::table('inventory_products', function (Blueprint $table) {
            // Link to farmers for traceability
            $table->uuid('farmer_id')->nullable()->after('id');
            $table->foreign('farmer_id')->references('id')->on('farmers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_products', function (Blueprint $table) {
            // Drop foreign key and column
            $table->dropForeign(['farmer_id']);
            $table->dropColumn('farmer_id');
        });
    }
};
