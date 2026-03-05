<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@elsawady.com'],
            [
                'password' => 'password123',
            ]
        );

        $customer = User::updateOrCreate(
            ['email' => 'customer@elsawady.com'],
            [
                'password' => 'password123',
            ]
        );

        $superAdmin->syncRoles(['SuperAdmin']);
        $customer->syncRoles(['Customer']);
    }
}
