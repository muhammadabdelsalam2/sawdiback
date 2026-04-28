<?php

namespace Database\Factories;

use App\Models\Farmer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Farmer>
 */
class FarmerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // This is a placeholder factory for the Farmer model. You can customize the fields and data generation logic as needed.

        // Start by generating some sample data for the farmer

        return [
            // Example fields - adjust these based on your actual Farmer model
            'id' => $this->faker->uuid(),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            // opening_balance => Decimal field to represent the farmer's opening balance
            'opening_balance' => $this->faker->randomFloat(2, 0, 10000),
            'account_id' => null, // Set to null or use a factory for related Account model if needed
            // user_id => Betwwne Null or a factory for related User model if you have user association
            'user_id' => null,

            'created_at' => now(),
            'updated_at' => now(),

        ];
    }
}
