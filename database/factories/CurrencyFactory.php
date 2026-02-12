<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Currency>
 */
class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->lexify('??#')),
            'symbol' => fake()->randomElement(['$', 'SR', 'EGP']),
            'rate' => fake()->randomFloat(4, 0.1, 3),
            'is_default' => false,
        ];
    }
}
