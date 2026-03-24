<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inventory_products', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->nullable()
                ->after('category')
                ->constrained('categories')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->index(['tenant_id', 'category_id'], 'inv_prod_tenant_category_id_idx');
        });

        $categorySeeds = [
            'feed' => ['en' => 'Feed', 'ar' => 'أعلاف'],
            'vet_medicine' => ['en' => 'Vet Medicine', 'ar' => 'أدوية بيطرية'],
            'equipment' => ['en' => 'Equipment', 'ar' => 'معدات'],
            'animal_product' => ['en' => 'Animal Product', 'ar' => 'منتج حيواني'],
        ];

        $tenantIds = DB::table('inventory_products')->distinct()->pluck('tenant_id');

        foreach ($tenantIds as $tenantId) {
            foreach ($categorySeeds as $code => $labels) {
                $categoryId = DB::table('categories')
                    ->where('tenant_id', $tenantId)
                    ->where('code', $code)
                    ->value('id');

                if (!$categoryId) {
                    $categoryId = DB::table('categories')->insertGetId([
                        'tenant_id' => $tenantId,
                        'parent_id' => null,
                        'code' => $code,
                        'sort_order' => 0,
                        'is_active' => true,
                        'notes' => $labels['en'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                foreach ($labels as $locale => $name) {
                    $exists = DB::table('category_translations')
                        ->where('category_id', $categoryId)
                        ->where('locale', $locale)
                        ->exists();

                    if (!$exists) {
                        $slug = Str::slug($name);
                        if ($slug === '') {
                            $slug = Str::slug($code . '-' . $locale);
                        }

                        DB::table('category_translations')->insert([
                            'category_id' => $categoryId,
                            'locale' => $locale,
                            'name' => $name,
                            'slug' => $slug,
                            'description' => $locale === 'ar' ? ('قسم ' . $name) : ($name . ' Category'),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                DB::table('inventory_products')
                    ->where('tenant_id', $tenantId)
                    ->where('category', $code)
                    ->whereNull('category_id')
                    ->update(['category_id' => $categoryId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('inventory_products', function (Blueprint $table) {
            $table->dropIndex('inv_prod_tenant_category_id_idx');
            $table->dropConstrainedForeignId('category_id');
        });
    }
};
