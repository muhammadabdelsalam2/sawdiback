<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SubscriptionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_create_and_transition_subscription_statuses(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@elsawady.com',
        ]);
        $customer = User::factory()->create();
        $currency = Currency::factory()->create();
        $planA = Plan::factory()->create([
            'currency_id' => $currency->id,
            'billing_cycle' => 'monthly',
        ]);
        $planB = Plan::factory()->create([
            'currency_id' => $currency->id,
            'billing_cycle' => 'yearly',
        ]);

        $this->actingAs($admin);

        $this->post(route('superadmin.subscriptions.store', ['locale' => 'en-SA']), [
            'customer_id' => $customer->id,
            'plan_id' => $planA->id,
        ])->assertRedirect();

        $subscriptionId = (int) DB::table('subscriptions')->value('id');

        $this->post(route('superadmin.subscriptions.change-plan', ['locale' => 'en-SA', 'subscription' => $subscriptionId]), [
            'plan_id' => $planB->id,
        ])->assertRedirect();

        $this->post(route('superadmin.subscriptions.renew', ['locale' => 'en-SA', 'subscription' => $subscriptionId]), [])
            ->assertRedirect();

        $this->post(route('superadmin.subscriptions.cancel', ['locale' => 'en-SA', 'subscription' => $subscriptionId]), [])
            ->assertRedirect();

        $this->post(route('superadmin.subscriptions.expire', ['locale' => 'en-SA', 'subscription' => $subscriptionId]), [])
            ->assertRedirect();

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscriptionId,
            'plan_id' => $planB->id,
            'status' => Subscription::STATUS_EXPIRED,
        ]);

        $historyCount = DB::table('subscription_histories')->where('subscription_id', $subscriptionId)->count();
        $this->assertGreaterThanOrEqual(5, $historyCount);
    }
}
