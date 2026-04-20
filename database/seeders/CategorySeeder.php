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
                'code' => 'electronics',
                'translations' => [
                    'en' => 'Electronics',
                    'ar' => 'إلكترونيات',
                ],
                'children' => [
                    [
                        'code' => 'mobiles',
                        'translations' => [
                            'en' => 'Mobile Phones',
                            'ar' => 'موبايلات',
                        ],
                    ],
                    [
                        'code' => 'laptops',
                        'translations' => [
                            'en' => 'Laptops',
                            'ar' => 'لابتوبات',
                        ],
                    ],
                    [
                        'code' => 'accessories',
                        'translations' => [
                            'en' => 'Accessories',
                            'ar' => 'إكسسوارات',
                        ],
                    ],
                    [
                        'code' => 'cameras',
                        'translations' => [
                            'en' => 'Cameras',
                            'ar' => 'كاميرات',
                        ],
                    ],
                ],
            ],

            [
                'code' => 'fashion',
                'translations' => [
                    'en' => 'Fashion',
                    'ar' => 'موضة',
                ],
                'children' => [
                    [
                        'code' => 'men',
                        'translations' => [
                            'en' => 'Men',
                            'ar' => 'رجالي',
                        ],
                    ],
                    [
                        'code' => 'women',
                        'translations' => [
                            'en' => 'Women',
                            'ar' => 'حريمي',
                        ],
                    ],
                    [
                        'code' => 'kids',
                        'translations' => [
                            'en' => 'Kids',
                            'ar' => 'أطفال',
                        ],
                    ],
                    [
                        'code' => 'shoes',
                        'translations' => [
                            'en' => 'Shoes',
                            'ar' => 'أحذية',
                        ],
                    ],
                ],
            ],

            [
                'code' => 'home',
                'translations' => [
                    'en' => 'Home & Living',
                    'ar' => 'المنزل والمعيشة',
                ],
                'children' => [
                    [
                        'code' => 'furniture',
                        'translations' => [
                            'en' => 'Furniture',
                            'ar' => 'أثاث',
                        ],
                    ],
                    [
                        'code' => 'kitchen',
                        'translations' => [
                            'en' => 'Kitchen',
                            'ar' => 'مطبخ',
                        ],
                    ],
                    [
                        'code' => 'decor',
                        'translations' => [
                            'en' => 'Home Decor',
                            'ar' => 'ديكور',
                        ],
                    ],
                    [
                        'code' => 'lighting',
                        'translations' => [
                            'en' => 'Lighting',
                            'ar' => 'إضاءة',
                        ],
                    ],
                ],
            ],

            [
                'code' => 'beauty',
                'translations' => [
                    'en' => 'Beauty',
                    'ar' => 'الجمال',
                ],
                'children' => [
                    [
                        'code' => 'makeup',
                        'translations' => [
                            'en' => 'Makeup',
                            'ar' => 'مكياج',
                        ],
                    ],
                    [
                        'code' => 'skincare',
                        'translations' => [
                            'en' => 'Skincare',
                            'ar' => 'العناية بالبشرة',
                        ],
                    ],
                    [
                        'code' => 'haircare',
                        'translations' => [
                            'en' => 'Haircare',
                            'ar' => 'العناية بالشعر',
                        ],
                    ],
                    [
                        'code' => 'fragrances',
                        'translations' => [
                            'en' => 'Fragrances',
                            'ar' => 'عطور',
                        ],
                    ],
                ],
            ],

            [
                'code' => 'sports',
                'translations' => [
                    'en' => 'Sports',
                    'ar' => 'رياضة',
                ],
                'children' => [
                    [
                        'code' => 'fitness',
                        'translations' => [
                            'en' => 'Fitness',
                            'ar' => 'لياقة بدنية',
                        ],
                    ],
                    [
                        'code' => 'football',
                        'translations' => [
                            'en' => 'Football',
                            'ar' => 'كرة القدم',
                        ],
                    ],
                    [
                        'code' => 'cycling',
                        'translations' => [
                            'en' => 'Cycling',
                            'ar' => 'دراجات',
                        ],
                    ],
                ],
            ],

            [
                'code' => 'books',
                'translations' => [
                    'en' => 'Books',
                    'ar' => 'كتب',
                ],
                'children' => [
                    [
                        'code' => 'education',
                        'translations' => [
                            'en' => 'Education',
                            'ar' => 'تعليم',
                        ],
                    ],
                    [
                        'code' => 'novels',
                        'translations' => [
                            'en' => 'Novels',
                            'ar' => 'روايات',
                        ],
                    ],
                    [
                        'code' => 'kids-books',
                        'translations' => [
                            'en' => 'Kids Books',
                            'ar' => 'كتب أطفال',
                        ],
                    ],
                ],
            ],

            [
                'code' => 'automotive',
                'translations' => [
                    'en' => 'Automotive',
                    'ar' => 'السيارات',
                ],
                'children' => [
                    [
                        'code' => 'car-accessories',
                        'translations' => [
                            'en' => 'Car Accessories',
                            'ar' => 'إكسسوارات سيارات',
                        ],
                    ],
                    [
                        'code' => 'motorcycles',
                        'translations' => [
                            'en' => 'Motorcycles',
                            'ar' => 'دراجات نارية',
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
