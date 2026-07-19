<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AuthPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::create(['name' => 'Customer', 'guard_name' => 'web']);
        Role::create(['name' => 'SuperAdmin', 'guard_name' => 'web']);
    }

    public function test_existing_user_can_request_password_reset_and_notification_is_sent(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'customer-reset@test.com',
        ]);
        $user->assignRole('Customer');

        $response = $this->post('/en-SA/forgot-password', [
            'email' => 'customer-reset@test.com',
        ]);

        $response->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPasswordNotification::class);
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'customer-reset@test.com',
        ]);
    }

    public function test_user_can_reset_password_and_login_with_new_password(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'customer-change@test.com',
            'password' => 'old-password',
        ]);
        $user->assignRole('Customer');

        $this->post('/en-SA/forgot-password', [
            'email' => 'customer-change@test.com',
        ]);

        $token = null;
        Notification::assertSentTo(
            $user,
            ResetPasswordNotification::class,
            function (ResetPasswordNotification $notification) use (&$token): bool {
                $token = $notification->token();

                return true;
            }
        );

        $response = $this->post('/en-SA/reset-password', [
            'token' => $token,
            'email' => 'customer-change@test.com',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertRedirect('/en-SA/login');

        $this->post('/en-SA/login', [
            'email' => 'customer-change@test.com',
            'password' => 'new-password123',
        ])->assertRedirect('/en-SA/dashboard');

        $this->assertAuthenticatedAs($user->fresh());
    }
}
