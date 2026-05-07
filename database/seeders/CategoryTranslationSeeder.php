<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoryTranslationSeeder extends Seeder
{
    private array $translations = [
        'AGRICULTURE' => ['en' => 'Agriculture',    'ar' => 'زراعة'],
        'LIVESTOCK'   => ['en' => 'Livestock',       'ar' => 'ثروة حيوانية'],
        'IRRIGATION'  => ['en' => 'Irrigation',      'ar' => 'ري'],
        'FERTILIZERS' => ['en' => 'Fertilizers',     'ar' => 'أسمدة'],
        'SEEDS'       => ['en' => 'Seeds',            'ar' => 'بذور'],
        'WHEAT'       => ['en' => 'Wheat',            'ar' => 'قمح'],
        'CORN'        => ['en' => 'Corn',             'ar' => 'ذرة'],
        'RICE'        => ['en' => 'Rice',             'ar' => 'أرز'],
        'VEGETABLES'  => ['en' => 'Vegetables',       'ar' => 'خضروات'],
        'FRUITS'      => ['en' => 'Fruits',           'ar' => 'فواكه'],
        'CATTLE'      => ['en' => 'Cattle',           'ar' => 'أبقار'],
        'POULTRY'     => ['en' => 'Poultry',          'ar' => 'دواجن'],
        'SHEEP'       => ['en' => 'Sheep',            'ar' => 'أغنام'],
        'GOATS'       => ['en' => 'Goats',            'ar' => 'ماعز'],
        'DAIRY'       => ['en' => 'Dairy Products',   'ar' => 'منتجات ألبان'],
    ];

    public function run(): void
    {
        $categories = Category::all();

        foreach ($categories as $category) {
            $trans = $this->translations[$category->code] ?? null;

            foreach (['en', 'ar'] as $locale) {
                $name = $trans[$locale]
                    ?? ($locale === 'en'
                        ? ucwords(strtolower(str_replace('_', ' ', $category->code)))
                        : $category->notes ?? $category->code);

                CategoryTranslation::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'locale'      => $locale,
                    ],
                    [
                        'name'        => $name,
                        'slug'        => Str::slug($name . '-' . $category->id),
                        'description' => $name,
                    ]
                );
            }
        }
    }
}
