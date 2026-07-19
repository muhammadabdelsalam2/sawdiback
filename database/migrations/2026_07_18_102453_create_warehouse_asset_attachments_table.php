<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_asset_attachments', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('warehouse_asset_id')->constrained('warehouse_assets')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('path');
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_asset_attachments');
    }
};
