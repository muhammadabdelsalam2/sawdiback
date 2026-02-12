<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Feature;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanFeatureAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_assign_features_to_plan(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@elsawady.com',
        ]);
        $currency = Currency::factory()->create();
        $plan = Plan::factory()->create([
            'currency_id' => $currency->id,
        ]);
        $featureOne = Feature::factory()->create([
            'key' => 'max_users',
            'type' => 'number',
        ]);
        $featureTwo = Feature::factory()->create([
            'key' => 'analytics',
            'type' => 'boolean',
        ]);

        $this->actingAs($admin);

        $this->put(route('superadmin.plans.features.update', ['locale' => 'en-SA', 'plan' => $plan->id]), [
            'features' => [
                $featureOne->id => [
                    'enabled' => 1,
                    'value' => '15',
                ],
                $featureTwo->id => [
                    'enabled' => 0,
                    'value' => '',
                ],
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('feature_plan', [
            'plan_id' => $plan->id,
            'feature_id' => $featureOne->id,
            'enabled' => 1,
            'value' => '15',
        ]);

        $this->assertDatabaseHas('feature_plan', [
            'plan_id' => $plan->id,
            'feature_id' => $featureTwo->id,
            'enabled' => 0,
        ]);
    }
}
