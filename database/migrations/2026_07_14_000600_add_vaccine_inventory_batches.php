<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vaccine_batches', function (Blueprint $table): void {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('vaccine_id')->constrained('vaccines')->cascadeOnDelete();
            $table->string('batch_number')->nullable();
            $table->decimal('quantity', 12, 2)->default(0);
            $table->date('expiry_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'vaccine_id', 'expiry_date']);
        });

        Schema::table('animal_vaccinations', function (Blueprint $table): void {
            $table->foreignId('pen_id')->nullable()->after('animal_id')->constrained('farm_pens')->nullOnDelete();
            $table->index(['tenant_id', 'pen_id']);
        });
    }

    public function down(): void
    {
        Schema::table('animal_vaccinations', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('pen_id');
        });

        Schema::dropIfExists('vaccine_batches');
    }
};
