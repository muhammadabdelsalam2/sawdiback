<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();

            // Multi-tenant scope (avoid FK to prevent breaking if tenant PK not uuid)
            $table->uuid('tenant_id')->index();

            $table->string('name');
            $table->string('code', 50)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
