<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE journal_entries MODIFY source_id VARCHAR(36) NULL');
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE journal_entries ALTER COLUMN source_id TYPE VARCHAR(36)');
            return;
        }

        if ($driver === 'sqlite') {
            // SQLite requires table rebuild for column type changes; skip to avoid migration failure.
            return;
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE journal_entries MODIFY source_id BIGINT UNSIGNED NULL');
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE journal_entries ALTER COLUMN source_id TYPE BIGINT');
            return;
        }

        if ($driver === 'sqlite') {
            return;
        }
    }
};
