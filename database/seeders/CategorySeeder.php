<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\User;
use App\Models\CategoryTranslation;
use Illuminate\Support\Str;
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // $users = User::whereNotNull('tenant_id')->get();
        // // Check Role Should be tenant_id role client
        // $user = null;
        // foreach ($users as $u) {
        //     if ($u->hasRole('client')) {
        //         $user = $u;
        //         break;
        //     }
        // }

        $user = User::whereHas('roles', function ($query) {
            $query->where('name', 'Client');
        })->first();


        if (!$user) {
            $this->command->error('No tenant user found. Please create a tenant user with the "Client" role before running this seeder.');
            return;
        }

        $categories = [
            [
                'code' => 'feed',
                'translations' => [
                    'en' => 'Feed',
                    'ar' => 'أعلاف',
                ],
                'children' => [
                    [
                        'code' => 'corn-feed',
                        'translations' => [
                            'en' => 'Corn Feed',
                            'ar' => 'علف ذرة',
                        ],
                    ],
                    [
                        'code' => 'alfalfa',
                        'translations' => [
                            'en' => 'Alfalfa',
                            'ar' => 'برسيم',
                        ],
                    ],
                ],
            ],

            [
                'code' => 'vet_medicine',
                'translations' => [
                    'en' => 'Veterinary Medicine',
                    'ar' => 'أدوية بيطرية',
                ],
                'children' => [
                    [
                        'code' => 'antibiotics',
                        'translations' => [
                            'en' => 'Antibiotics',
                            'ar' => 'مضادات حيوية',
                        ],
                    ],
                    [
                        'code' => 'vitamins',
                        'translations' => [
                            'en' => 'Vitamins',
                            'ar' => 'فيتامينات',
                        ],
                    ],
                ],
            ],

            [
                'code' => 'equipment',
                'translations' => [
                    'en' => 'Equipment',
                    'ar' => 'معدات',
                ],
                'children' => [
                    [
                        'code' => 'milking',
                        'translations' => [
                            'en' => 'Milking Equipment',
                            'ar' => 'معدات الحلب',
                        ],
                    ],
                    [
                        'code' => 'pumps',
                        'translations' => [
                            'en' => 'Pumps',
                            'ar' => 'مضخات',
                        ],
                    ],
                ],
            ],

            [
                'code' => 'animal_product',
                'translations' => [
                    'en' => 'Animal Products',
                    'ar' => 'منتجات حيوانية',
                ],
                'children' => [
                    [
                        'code' => 'milk',
                        'translations' => [
                            'en' => 'Milk',
                            'ar' => 'ألبان',
                        ],
                    ],
                    [
                        'code' => 'meat',
                        'translations' => [
                            'en' => 'Meat',
                            'ar' => 'لحوم',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $this->createCategory($categoryData, null, $user);
        }
    }

    private function createCategory(array $data, $parentId = null, $user = null)
    {
        $this->command->info('tenant_id: ' . $user . ' - Creating category: ' . $data['code']);
        $category = Category::updateOrCreate([
            'tenant_id' => $user->tenant_id,
            'parent_id' => $parentId,
            'code' => $data['code'],
            'sort_order' => 0,
            'is_active' => 1,
        ]);

        // translations
        foreach ($data['translations'] as $locale => $name) {
            CategoryTranslation::updateOrCreate([
                'category_id' => $category->id,
                'locale' => $locale,
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $name . ' category',
            ]);
        }

        // children
        if (!empty($data['children'])) {
            foreach ($data['children'] as $child) {
                $this->createCategory($child, $category->id, $user);
            }
        }
        // Return Table command design to show the created categories
        $this->command->info('Created category: ' . $category->code);

    }
}
