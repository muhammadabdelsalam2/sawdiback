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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'instagram_id')) {
                $table->string('instagram_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'social_avatar')) {
                $table->string('social_avatar')->nullable()->after('instagram_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'facebook_id', 'instagram_id', 'social_avatar']);
        });
    }
};
