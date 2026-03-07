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
        // Check If tenant_id is null, you might want to handle that case
        $defaultUser = User::where('email', 'customer@elsawady.com')->first();

        if (!$defaultUser) {
            $this->command->error("Default user not found.");
            return;
        }

        $tenantId = $defaultUser->tenant_id;

        if (!$tenantId) {
            $this->command->error("User exists but tenant_id is null.");
            return;
        }

        $this->command->info("Seeding categories for tenant_id: $tenantId");

        $categories = [
            ['code' => 'FEED', 'en' => 'Feed', 'ar' => 'أعلاف'],
            ['code' => 'SEED', 'en' => 'Seeds', 'ar' => 'بذور'],
            ['code' => 'FERT', 'en' => 'Fertilizers', 'ar' => 'أسمدة'],
            ['code' => 'PEST', 'en' => 'Pesticides', 'ar' => 'مبيدات'],
            ['code' => 'IRRG', 'en' => 'Irrigation', 'ar' => 'الري'],
            ['code' => 'TOOLS', 'en' => 'Farm Tools', 'ar' => 'أدوات زراعية'],
            ['code' => 'MACH', 'en' => 'Machinery', 'ar' => 'معدات زراعية'],
            ['code' => 'TRACT', 'en' => 'Tractors', 'ar' => 'جرارات'],
            ['code' => 'ANML', 'en' => 'Livestock', 'ar' => 'مواشي'],
            ['code' => 'POUL', 'en' => 'Poultry', 'ar' => 'دواجن'],
            ['code' => 'VET', 'en' => 'Veterinary', 'ar' => 'أدوية بيطرية'],
            ['code' => 'DAIRY', 'en' => 'Dairy', 'ar' => 'منتجات ألبان'],
            ['code' => 'HAY', 'en' => 'Hay & Fodder', 'ar' => 'تبن وعلف'],
            ['code' => 'GREEN', 'en' => 'Greenhouses', 'ar' => 'صوب زراعية'],
            ['code' => 'SOIL', 'en' => 'Soil Care', 'ar' => 'العناية بالتربة'],
            ['code' => 'WATER', 'en' => 'Water Systems', 'ar' => 'أنظمة المياه'],
            ['code' => 'PLANT', 'en' => 'Plant Care', 'ar' => 'العناية بالنبات'],
            ['code' => 'HARV', 'en' => 'Harvest Tools', 'ar' => 'أدوات الحصاد'],
            ['code' => 'STOR', 'en' => 'Storage', 'ar' => 'التخزين'],
            ['code' => 'PACK', 'en' => 'Packaging', 'ar' => 'التعبئة والتغليف'],
            ['code' => 'ORGA', 'en' => 'Organic Products', 'ar' => 'منتجات عضوية'],
            ['code' => 'SEEDL', 'en' => 'Seedlings', 'ar' => 'شتلات'],
            ['code' => 'FRUIT', 'en' => 'Fruit Farming', 'ar' => 'زراعة الفاكهة'],
            ['code' => 'VEG', 'en' => 'Vegetable Farming', 'ar' => 'زراعة الخضروات'],
            ['code' => 'GRAIN', 'en' => 'Grains', 'ar' => 'الحبوب'],
            ['code' => 'HERB', 'en' => 'Herbs', 'ar' => 'أعشاب'],
            ['code' => 'FLOW', 'en' => 'Flowers', 'ar' => 'زهور'],
            ['code' => 'LAND', 'en' => 'Land Preparation', 'ar' => 'تجهيز الأراضي'],
            ['code' => 'FENCE', 'en' => 'Fencing', 'ar' => 'أسوار المزارع'],
            ['code' => 'ENER', 'en' => 'Farm Energy', 'ar' => 'طاقة المزارع'],
        ];

        foreach ($categories as $index => $cat) {

            $category = Category::create([
                'tenant_id' => $tenantId,
                'code' => $cat['code'],
                'sort_order' => $index + 1,
                'is_active' => true,
                'notes' => $cat['en'],
            ]);

            $category->translations()->create([
                'locale' => 'en',
                'name' => $cat['en'],
                'slug' => \Str::slug($cat['en']),
                'description' => $cat['en'] . ' Category',
            ]);

            $category->translations()->create([
                'locale' => 'ar',
                'name' => $cat['ar'],
                'slug' => \Str::slug($cat['ar']),
                'description' => 'قسم ' . $cat['ar'],
            ]);
        }
    }
}
