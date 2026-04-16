<?php

namespace Database\Seeders;

use App\Models\SupportItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        SupportItem::insert([
            [
                'title' => 'Help Center',
                'subtitle' => 'FAQs & Support',
                'icon' => 'headphones',
                'type' => 'route',
                'value' => 'support.value', // route name
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'FAQs',
                'subtitle' => 'Legal information',
                'icon' => 'question',
                'type' => 'route',
                'value' => 'support.value',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Contact Us',
                'subtitle' => 'Data & Privacy',
                'icon' => 'chat',
                'type' => 'route',
                'value' => 'support.value',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Terms & Policies',
                'subtitle' => 'Legal & Privacy Terms',
                'icon' => 'file',
                'type' => 'route',
                'value' => 'support.value',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Logout',
                'subtitle' => 'Sign out from account',
                'icon' => 'logout',
                'type' => 'action',
                'value' => 'support.value', // frontend action
                'order' => 5,
                'is_active' => true,
            ],
        ]);
    }
}
