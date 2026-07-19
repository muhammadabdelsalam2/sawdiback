<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class SuperAdminUsersIndexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Permission::create(['name' => 'users.manage', 'guard_name' => 'web']);

        $superAdmin = Role::create(['name' => 'SuperAdmin', 'guard_name' => 'web']);
        Role::create(['name' => 'Customer', 'guard_name' => 'web']);

        $superAdmin->givePermissionTo('users.manage');
    }

    public function test_superadmin_users_index_opens_with_bootstrap_pagination(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('SuperAdmin');

        User::factory()->count(12)->create();

        $response = $this->actingAs($admin)->get('/en-SA/superadmin/users');

        $response->assertOk();
        $response->assertSee('pagination', false);
        $response->assertSee('page-link', false);
    }
}
