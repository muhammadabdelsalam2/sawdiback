<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            if (!Schema::hasColumn('user_addresses', 'type')) {
                $table->string('type', 50)->nullable()->after('label');
            }
            if (!Schema::hasColumn('user_addresses', 'landmark')) {
                $table->string('landmark', 255)->nullable()->after('apartment');
            }
            if (!Schema::hasColumn('user_addresses', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('notes');
            }
            if (!Schema::hasColumn('user_addresses', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            if (Schema::hasColumn('user_addresses', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('user_addresses', 'landmark')) {
                $table->dropColumn('landmark');
            }
            if (Schema::hasColumn('user_addresses', 'latitude')) {
                $table->dropColumn('latitude');
            }
            if (Schema::hasColumn('user_addresses', 'longitude')) {
                $table->dropColumn('longitude');
            }
        });
    }
};
