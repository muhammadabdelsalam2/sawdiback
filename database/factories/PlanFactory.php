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

        // Define possible feature keys and types
        $featureKeys = [
            ['key' => 'max_users', 'type' => 'numeric'],
            ['key' => 'advanced_reports', 'type' => 'boolean'],
            ['key' => 'priority_support', 'type' => 'boolean'],
            ['key' => 'storage_limit', 'type' => 'numeric'],
            ['key' => 'custom_domain', 'type' => 'boolean'],
        ];

        // Pick 2–4 random features for this plan
        $randomFeatures = collect($featureKeys)->random(rand(2, 4));

        $featuresJson = [];
        foreach ($randomFeatures as $feature) {
            $featuresJson[$feature['key']] = [
                'value' => $feature['type'] === 'boolean' ? true : fake()->numberBetween(1, 100),
                'enabled' => true
            ];
        }

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(100, 999),
            'price' => fake()->randomFloat(2, 10, 500),
            'currency_id' => Currency::factory(),
            'billing_cycle' => fake()->randomElement(['monthly', 'yearly']),
            'is_active' => true,
            'description' => fake()->sentence(),
            'sort_order' => fake()->numberBetween(0, 10),
            'features' => $featuresJson, // assign features JSON
        ];
    }
}
