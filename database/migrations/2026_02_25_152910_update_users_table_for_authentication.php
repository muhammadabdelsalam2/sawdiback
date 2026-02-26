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
        //
        Schema::table('users', function (Blueprint $table) {

            // Make email nullable
            $table->string('email')->nullable()->change();

            // Make phone nullable
            $table->string('phone')->nullable()->change();

            // Verification columns
            $table->timestamp('email_verified_at')->nullable()->change();
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');

            // Social login columns
            $table->string('facebook_id')->nullable()->unique()->after('phone_verified_at');
            $table->string('google_id')->nullable()->unique()->after('facebook_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('users', function (Blueprint $table) {

            // Make email nullable
            $table->string('email')->nullable()->change();

            // Make phone nullable
            $table->string('phone')->nullable()->change();

            // Verification columns
            $table->timestamp('email_verified_at')->nullable()->change();
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');

            // Social login columns
            $table->string('facebook_id')->nullable()->unique()->after('phone_verified_at');
            $table->string('google_id')->nullable()->unique()->after('facebook_id');
            $table->boolean('is_active')->default(false)->after('password');

        });
    }
};