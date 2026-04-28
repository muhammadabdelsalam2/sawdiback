<?php

namespace Database\Seeders;

use App\Models\Farmer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FarmerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // This is a placeholder seeder for farmers. You can add logic here to create default farmers or related data as needed.    

        // Start by creating some sample farmers
        Farmer::factory()->count(10)->create(); // Uncomment this line if you have a factory set up for the Farmer model
    }
}
