<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CustomerAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::create(['name' => 'Customer', 'guard_name' => 'web']);
        Role::create(['name' => 'SuperAdmin', 'guard_name' => 'web']);
    }

    public function test_customer_can_open_profile_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Customer');

        $this->actingAs($user)
            ->get('/en-SA/account/profile')
            ->assertOk();
    }

    public function test_customer_can_update_profile_data(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old-customer@test.com',
            'phone' => '111',
            'preferred_language' => 'en',
        ]);
        $user->assignRole('Customer');

        $response = $this->actingAs($user)->put('/en-SA/account/profile', [
            'name' => 'Updated Customer',
            'email' => 'updated-customer@test.com',
            'phone' => '555123',
            'preferred_language' => 'ar',
        ]);

        $response->assertRedirect('/en-SA/account/profile');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Customer',
            'email' => 'updated-customer@test.com',
            'phone' => '555123',
            'preferred_language' => 'ar',
        ]);
    }

    public function test_customer_can_change_password_and_old_password_stops_working(): void
    {
        $user = User::factory()->create([
            'email' => 'password-customer@test.com',
            'password' => 'old-password123',
        ]);
        $user->assignRole('Customer');

        $response = $this->actingAs($user)->put('/en-SA/account/password', [
            'current_password' => 'old-password123',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertRedirect('/en-SA/account/password');

        Auth::logout();

        $this->post('/en-SA/login', [
            'email' => 'password-customer@test.com',
            'password' => 'old-password123',
        ])->assertSessionHasErrors('email');

        $this->post('/en-SA/login', [
            'email' => 'password-customer@test.com',
            'password' => 'new-password123',
        ])->assertRedirect('/en-SA/dashboard');
    }
}
