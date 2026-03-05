<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        // اجلب ال Default User
        $defaultUser = User::where('email', 'customer@elsawady.com')->first();

        // اجلب tenant_id الخاص به
        $tenantId = $defaultUser->tenant_id;

        // بعد كده استخدم $tenantId في Seeder
        $category = Category::create([
            'tenant_id' => $tenantId,
            'code' => 'FEED',
            'sort_order' => 1,
            'is_active' => true,
            'notes' => 'Animal Feed',
        ]);

        // Add translations
        $category->translations()->create([
            'locale' => 'en',
            'name' => 'Feed',
            'slug' => 'feed',
            'description' => 'Animal Feed Category',
        ]);
        $category->translations()->create([
            'locale' => 'ar',
            'name' => 'أعلاف',
            'slug' => 'a3laf',
            'description' => 'قسم الأعلاف',
        ]);
    }
}
