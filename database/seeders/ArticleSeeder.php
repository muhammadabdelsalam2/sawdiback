<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = [
            [
                'title' => ['en' => 'Healthy Breakfast Ideas', 'ar' => 'أفكار فطور صحي'],
                'description' => ['en' => 'Start your day right with organic ingredients.', 'ar' => 'ابدأ يومك بمكونات عضوية.'],
                'category' => 'Recipes',
                'image' => 'assets/images/product1.jpg',
            ],
            [
                'title' => ['en' => 'Fresh Farm Vegetables', 'ar' => 'خضروات طازجة من المزرعة'],
                'description' => ['en' => 'Choose fresh vegetables packed with natural flavor.', 'ar' => 'اختر خضروات طازجة مليئة بالنكهة الطبيعية.'],
                'category' => 'Organic',
                'image' => 'assets/images/product2.jpg',
            ],
            [
                'title' => ['en' => 'Simple Lunch Recipes', 'ar' => 'وصفات غداء بسيطة'],
                'description' => ['en' => 'Quick lunch meals made with wholesome products.', 'ar' => 'وجبات غداء سريعة بمكونات مفيدة.'],
                'category' => 'Recipes',
                'image' => 'assets/images/product3.jpg',
            ],
            [
                'title' => ['en' => 'Seasonal Fruit Guide', 'ar' => 'دليل الفواكه الموسمية'],
                'description' => ['en' => 'Discover the best fruits for every season.', 'ar' => 'اكتشف أفضل الفواكه لكل موسم.'],
                'category' => 'Fruits',
                'image' => 'assets/images/product4.jpg',
            ],
            [
                'title' => ['en' => 'Nutritious Family Dinner', 'ar' => 'عشاء عائلي مغذي'],
                'description' => ['en' => 'Balanced dinner ideas for the whole family.', 'ar' => 'أفكار عشاء متوازن لكل العائلة.'],
                'category' => 'Meals',
                'image' => 'assets/images/product5.jpg',
            ],
        ];

        foreach ($articles as $article) {
            Article::updateOrCreate(
                ['image' => $article['image']],
                $article
            );
        }
    }
}
