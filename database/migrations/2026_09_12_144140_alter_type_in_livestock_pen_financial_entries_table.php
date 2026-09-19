<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `livestock_pen_financial_entries` MODIFY `type` VARCHAR(50) NOT NULL");
    }

    public function down(): void
    {
        //
    }
};