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
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->string('identifier'); // email or phone
            $table->string('code'); // numeric OTP code
            $table->string('type'); // login, register, forgot_password, reset_password, resend
            $table->boolean('is_used')->default(false); // mark as used
            $table->timestamp('expires_at'); // expiration time
            $table->timestamps();

            $table->index(['identifier', 'type']); // optimize lookups
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
