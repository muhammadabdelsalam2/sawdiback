<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        $startAt = now();

        return [
            'customer_id' => User::factory(),
            'plan_id' => Plan::factory(),
            'status' => Subscription::STATUS_ACTIVE,
            'start_at' => $startAt,
            'end_at' => (clone $startAt)->addMonth(),
            'renewal_at' => (clone $startAt)->addMonth(),
            'canceled_at' => null,
            'metadata' => null,
        ];
    }
}
