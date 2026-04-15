<?php

namespace Database\Seeders;

use App\Models\LivestockAnimal;
use App\Models\MilkProductionLog;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DashboardDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Demo Customer exists
        $user = User::where('email', 'customer@elsawady.com')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Demo Customer',
                'email' => 'customer@elsawady.com',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]);
            $user->assignRole('Customer');
        }

        // 2. Ensure Tenant exists
        $tenant = Tenant::first();
        if (!$tenant) {
            $tenant = Tenant::create([
                'id' => (string) Str::uuid(),
                'name' => 'Al-Sawady Farm',
                'slug' => 'al-sawady-farm',
                'status' => 'active',
            ]);
        }
        $user->tenant_id = $tenant->id;
        $user->save();

        // 3. Ensure Subscription is Valid
        $subscription = Subscription::where('customer_id', $user->id)->first();
        if (!$subscription) {
            $plan = Plan::first() ?? Plan::factory()->create();
            Subscription::create([
                'customer_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => Subscription::STATUS_ACTIVE,
                'start_at' => now(),
                'end_at' => now()->addYear(),
            ]);
        } else {
            $subscription->update(['status' => Subscription::STATUS_ACTIVE, 'end_at' => now()->addYear()]);
        }

        // 4. Seed some Production Data for the last 7 days for this tenant
        // We need some animals first
        $animals = LivestockAnimal::where('tenant_id', $tenant->id)->get();
        if ($animals->isEmpty()) {
            $this->call(LivestockAnimalsSeeder::class);
            $animals = LivestockAnimal::where('tenant_id', $tenant->id)->get();
        }

        foreach ($animals as $animal) {
            if ($animal->gender === 'female') {
                for ($i = 0; $i < 10; $i++) {
                    MilkProductionLog::updateOrCreate(
                        [
                            'animal_id' => $animal->id,
                            'production_date' => now()->subDays($i)->toDateString(),
                        ],
                        [
                            'quantity_liters' => rand(15, 30),
                            'fat_percentage' => rand(3, 5),
                            'notes' => 'Seeded for demo',
                            'tenant_id' => $tenant->id,
                        ]
                    );
                }
            }
        }

        $this->command->info('Dashboard Demo Data Seeded successfully!');
    }
}
