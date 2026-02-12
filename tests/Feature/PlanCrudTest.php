<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PlanCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_create_update_and_delete_plan(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@elsawady.com',
        ]);
        $currency = Currency::factory()->create([
            'code' => 'SAR',
            'symbol' => 'SR',
            'rate' => 1,
            'is_default' => true,
        ]);

        $this->actingAs($admin);

        $this->post(route('superadmin.plans.store', ['locale' => 'en-SA']), [
            'name' => 'Starter',
            'slug' => 'starter',
            'price' => 49.99,
            'currency_id' => $currency->id,
            'billing_cycle' => 'monthly',
            'is_active' => 1,
            'description' => 'Starter plan',
            'sort_order' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('plans', [
            'slug' => 'starter',
            'name' => 'Starter',
        ]);

        $planId = (int) DB::table('plans')->where('slug', 'starter')->value('id');

        $this->put(route('superadmin.plans.update', ['locale' => 'en-SA', 'plan' => $planId]), [
            'name' => 'Starter Updated',
            'slug' => 'starter-updated',
            'price' => 59.99,
            'currency_id' => $currency->id,
            'billing_cycle' => 'yearly',
            'is_active' => 1,
            'description' => 'Updated',
            'sort_order' => 2,
        ])->assertRedirect();

        $this->assertDatabaseHas('plans', [
            'id' => $planId,
            'slug' => 'starter-updated',
            'name' => 'Starter Updated',
        ]);

        $this->delete(route('superadmin.plans.destroy', ['locale' => 'en-SA', 'plan' => $planId]))
            ->assertRedirect();

        $this->assertSoftDeleted('plans', [
            'id' => $planId,
        ]);
    }
}
