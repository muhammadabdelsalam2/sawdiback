<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',
            'roles.manage',
            'animals.view',
            'animals.manage',
            'production.view',
            'production.manage',
            'inventory.view',
            'inventory.manage',
            'sales.view',
            'sales.manage',
            'hr.view',
            'hr.manage',
            'settings.view',
            'settings.manage',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        $customerRole = Role::firstOrCreate([
            'name' => 'Customer',
            'guard_name' => 'web',
        ]);

        $superAdminRole = Role::firstOrCreate([
            'name' => 'SuperAdmin',
            'guard_name' => 'web',
        ]);

        $clientRole = Role::firstOrCreate([
            'name' => 'Client',
            'guard_name' => 'web',
        ]);

        // Customer gets everything for now to make sure the demo works perfectly
        $customerRole->syncPermissions($permissions);
        $superAdminRole->syncPermissions($permissions);
    }
}
