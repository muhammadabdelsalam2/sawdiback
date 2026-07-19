<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contact_infos', function (Blueprint $table): void {
            $table->id();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->json('address')->nullable();
            $table->json('working_hours')->nullable();
            $table->json('description')->nullable();
            $table->string('whatsapp_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('x_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_infos');
    }
};
