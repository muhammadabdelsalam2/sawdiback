<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::firstOrCreate([
            'name' => 'full-access',
            'guard_name' => 'web',
        ]);

        $user = User::updateOrCreate(
            ['email' => 'client@gmail.com'],
            [
                'name' => 'Client User',
                'password' => 'password123',
            ]
        );

        $user->syncRoles([$role->name]);
    }
}
