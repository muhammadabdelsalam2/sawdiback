<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RoleBasedAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Permission::create(['name' => 'dashboard.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'roles.manage', 'guard_name' => 'web']);

        $customer = Role::create(['name' => 'Customer', 'guard_name' => 'web']);
        $superAdmin = Role::create(['name' => 'SuperAdmin', 'guard_name' => 'web']);

        $customer->givePermissionTo('dashboard.view');
        $superAdmin->givePermissionTo(['dashboard.view', 'roles.manage']);
    }

    public function test_superadmin_login_redirects_to_superadmin_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);
        $user->assignRole('SuperAdmin');

        $response = $this->post('/en-SA/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/en-SA/superadmin/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_customer_login_redirects_to_customer_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'customer@test.com',
            'password' => 'password123',
        ]);
        $user->assignRole('Customer');

        $response = $this->post('/en-SA/login', [
            'email' => 'customer@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/en-SA/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_customer_cannot_access_superadmin_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Customer');

        $response = $this->actingAs($user)->get('/en-SA/superadmin/dashboard');

        $response->assertForbidden();
    }

    public function test_superadmin_can_access_access_management_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('SuperAdmin');

        $response = $this->actingAs($user)->get('/en-SA/superadmin/access-management');

        $response->assertOk();
    }

    public function test_guest_is_redirected_to_login_form_when_opening_dashboard(): void
    {
        $response = $this->get('/en-SA/dashboard');

        $response->assertRedirect('/en-SA/login');
    }

    public function test_superadmin_can_open_user_management_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole('SuperAdmin');

        $response = $this->actingAs($user)->get('/en-SA/superadmin/users');

        $response->assertOk();
    }

    public function test_customer_cannot_open_user_management_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Customer');

        $response = $this->actingAs($user)->get('/en-SA/superadmin/users');

        $response->assertForbidden();
    }

    public function test_superadmin_can_create_user_with_role_and_permission(): void
    {
        $user = User::factory()->create();
        $user->assignRole('SuperAdmin');

        $response = $this->actingAs($user)->post('/en-SA/superadmin/users', [
            'name' => 'New Manager',
            'email' => 'manager@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roles' => ['Customer'],
            'permissions' => ['dashboard.view'],
        ]);

        $response->assertRedirect('/en-SA/superadmin/users');
        $this->assertDatabaseHas('users', ['email' => 'manager@test.com']);
        $this->assertTrue(User::where('email', 'manager@test.com')->firstOrFail()->hasRole('Customer'));
    }

    public function test_superadmin_can_open_edit_user_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('SuperAdmin');

        $target = User::factory()->create();
        $target->assignRole('Customer');

        $response = $this->actingAs($admin)->get("/en-SA/superadmin/users/{$target->id}/edit");

        $response->assertOk();
    }

    public function test_superadmin_can_update_user_role_assignment(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('SuperAdmin');

        $target = User::factory()->create();
        $target->assignRole('Customer');

        $response = $this->actingAs($admin)->put("/en-SA/superadmin/users/{$target->id}", [
            'name' => $target->name,
            'email' => $target->email,
            'roles' => ['SuperAdmin'],
            'permissions' => ['roles.manage'],
        ]);

        $response->assertRedirect('/en-SA/superadmin/users');
        $target->refresh();
        $this->assertTrue($target->hasRole('SuperAdmin'));
        $this->assertTrue($target->hasPermissionTo('roles.manage'));
    }

    public function test_superadmin_can_delete_other_user(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('SuperAdmin');

        $target = User::factory()->create();
        $target->assignRole('Customer');

        $response = $this->actingAs($admin)->delete("/en-SA/superadmin/users/{$target->id}");

        $response->assertRedirect('/en-SA/superadmin/users');
        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_superadmin_can_create_role_from_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('SuperAdmin');

        $response = $this->actingAs($admin)->post('/en-SA/superadmin/access-management/roles', [
            'name' => 'Manager',
        ]);

        $response->assertRedirect('/en-SA/superadmin/access-management');
        $this->assertDatabaseHas('roles', ['name' => 'Manager', 'guard_name' => 'web']);
    }

    public function test_superadmin_can_create_permission_from_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('SuperAdmin');

        $response = $this->actingAs($admin)->post('/en-SA/superadmin/access-management/permissions', [
            'name' => 'reports.view',
        ]);

        $response->assertRedirect('/en-SA/superadmin/access-management');
        $this->assertDatabaseHas('permissions', ['name' => 'reports.view', 'guard_name' => 'web']);
    }

    public function test_superadmin_can_update_role_permissions_from_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('SuperAdmin');

        $manager = Role::create(['name' => 'Manager', 'guard_name' => 'web']);
        Permission::create(['name' => 'reports.view', 'guard_name' => 'web']);

        $response = $this->actingAs($admin)->put("/en-SA/superadmin/access-management/roles/{$manager->id}/permissions", [
            'permissions' => ['dashboard.view', 'reports.view'],
        ]);

        $response->assertRedirect('/en-SA/superadmin/access-management');
        $manager->refresh();
        $this->assertTrue($manager->hasPermissionTo('dashboard.view'));
        $this->assertTrue($manager->hasPermissionTo('reports.view'));
    }
}
