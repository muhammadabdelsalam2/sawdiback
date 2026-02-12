<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Models\Feature;
use App\Models\Subscription;
class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    $plans = Plan::factory(5)->create();

    $features = Feature::factory(10)->create();

    foreach ($plans as $plan) {
        $plan->features()->attach(
            $features->random(3)->pluck('id')
        );
    }

    Subscription::factory(5)->create();
}
}
