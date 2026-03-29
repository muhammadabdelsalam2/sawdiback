<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('unit_tax', 14, 2)->default(0)->after('unit_price');
            $table->decimal('line_subtotal', 14, 2)->default(0)->after('unit_tax');
            $table->decimal('line_tax', 14, 2)->default(0)->after('line_subtotal');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['unit_tax', 'line_subtotal', 'line_tax']);
        });
    }
};
