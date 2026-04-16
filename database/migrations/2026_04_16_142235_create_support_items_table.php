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
        Schema::create('support_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('icon')->nullable();
            $table->string('type'); // link, route, action
            $table->string('value')->nullable(); // url or route name
            $table->integer('order')->default(0);
            $table->enum('module', ['FAQS','CONTACT_US','TERMS_POLICIES', 'HELP_CENTER','GENERAL'])->default('GENERAL'); // for categorization
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_items');
    }
};
