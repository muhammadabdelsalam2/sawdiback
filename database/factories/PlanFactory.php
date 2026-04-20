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

    use Illuminate\Support\Str;

    public function definition(): array
    {
        $name = $this->fake->unique()->words(2, true);
        $slug = Str::slug($name, '_') . '-' . $this->fake->unique()->numberBetween(100, 999);

        $featureKeys = [
            ['key' => 'max_users', 'type' => 'numeric'],
            ['key' => 'advanced_reports', 'type' => 'boolean'],
            ['key' => 'priority_support', 'type' => 'boolean'],
            ['key' => 'storage_limit', 'type' => 'numeric'],
            ['key' => 'custom_domain', 'type' => 'boolean'],
        ];

        $randomFeatures = collect($featureKeys)->random(rand(2, 4));

        $featuresJson = [];

        foreach ($randomFeatures as $feature) {
            $featuresJson[$feature['key']] = [
                'value' => $feature['type'] === 'boolean'
                    ? true
                    : $this->fake->numberBetween(1, 100),
                'enabled' => true
            ];
        }

        return [
            'name' => $name,
            'slug' => $slug,
            'price' => $this->fake->randomFloat(2, 10, 500),
            'currency_id' => Currency::factory(),
            'billing_cycle' => $this->fake->randomElement(['monthly', 'yearly']),
            'is_active' => true,
            'description' => $this->fake->sentence(),
            'sort_order' => $this->fake->numberBetween(0, 10),
            'features' => $featuresJson,
        ];
    }
}
