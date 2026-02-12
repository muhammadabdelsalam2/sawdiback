<?php

namespace Database\Factories;

use App\Models\Feature;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Feature>
 */
class FeatureFactory extends Factory
{
    protected $model = Feature::class;

    public function definition(): array
    {
        $keyBase = Str::slug(fake()->unique()->words(2, true), '_');

        return [
            'key' => $keyBase,
            'name' => ucwords(str_replace('_', ' ', $keyBase)),
            'type' => fake()->randomElement(['boolean', 'number', 'string']),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
