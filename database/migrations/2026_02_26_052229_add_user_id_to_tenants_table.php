<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            //
                 Schema::table('tenants', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('slug'); // adjust after column as needed
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // optional foreign key
        });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            //
                 Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['user_id']); // drop foreign key first
            $table->dropColumn('user_id');    // then drop column
        });
        });
    }
};
