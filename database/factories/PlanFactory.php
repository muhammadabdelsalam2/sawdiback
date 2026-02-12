<?php

namespace Database\Factories;

use App\Models\Currency;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(100, 999),
            'price' => fake()->randomFloat(2, 10, 500),
            'currency_id' => Currency::factory(),
            'billing_cycle' => fake()->randomElement(['monthly', 'yearly']),
            'is_active' => true,
            'description' => fake()->sentence(),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
