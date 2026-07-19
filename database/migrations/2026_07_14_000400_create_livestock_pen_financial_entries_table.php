<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('livestock_pen_financial_entries', function (Blueprint $table): void {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->foreignId('pen_id')->constrained('farm_pens')->cascadeOnDelete();
            $table->enum('type', ['sale', 'slaughter_packaging']);
            $table->decimal('amount', 12, 2);
            $table->date('entry_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'pen_id', 'type']);
            $table->index(['entry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_pen_financial_entries');
    }
};
