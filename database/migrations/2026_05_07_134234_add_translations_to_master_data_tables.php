<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'animal_species' => 'name',
            'animal_breeds' => 'name',
            'vaccines' => 'name',
            'feed_types' => 'name',
            'crops' => 'name',
            'departments' => 'name',
            'job_titles' => 'name',
        ];

        foreach ($tables as $table => $column) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) use ($column) {
                    $table->json('name_translations')->nullable()->after($column);
                });

                // Migrate data
                DB::table($table)->orderBy('id')->chunkById(100, function ($rows) use ($table, $column) {
                    foreach ($rows as $row) {
                        DB::table($table)->where('id', $row->id)->update([
                            'name_translations' => json_encode([
                                'ar' => $row->{$column},
                                'en' => $row->{$column},
                            ], JSON_UNESCAPED_UNICODE)
                        ]);
                    }
                });
            }
        }

        // Special handling for categories
        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->json('name_translations')->nullable()->after('code');
            });

            // Migrate data from category_translations
            if (Schema::hasTable('category_translations')) {
                $translations = DB::table('category_translations')->get();
                $data = [];
                foreach ($translations as $t) {
                    $data[$t->category_id][$t->locale] = $t->name;
                }

                foreach ($data as $categoryId => $locales) {
                    DB::table('categories')->where('id', $categoryId)->update([
                        'name_translations' => json_encode($locales, JSON_UNESCAPED_UNICODE)
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'animal_species',
            'animal_breeds',
            'vaccines',
            'feed_types',
            'crops',
            'departments',
            'job_titles',
            'categories',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('name_translations');
                });
            }
        }
    }
};
