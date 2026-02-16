<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Subscription;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1️⃣ SuperAdmin
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@elsawady.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
            ]
        );
        $superAdmin->assignRole('SuperAdmin');

        // 2️⃣ Customer
        $customer = User::updateOrCreate(
            ['email' => 'customer@elsawady.com'],
            [
                'name' => 'Default Customer',
                'password' => Hash::make('password123'),
            ]
        );
        $customer->assignRole('Customer');

        $tenant = Tenant::where('slug', Str::slug('Customer Tenant'))->first();

        if (!$tenant) {
            $tenant = Tenant::create([
                'id' => Str::uuid(),
                'name' => 'Customer Tenant',
                'slug' => Str::slug('Customer Tenant'),
                'status' => 'active',
            ]);
        }

        // تأكد الآن من أن $tenant->id موجود
        $this->command->info("The Tenant Id Is => $tenant->id");

        // Assign tenant_id to customer
        $customer->tenant_id = $tenant->id;
        $customer->save();

        // 4️⃣ Create Plans with Features JSON
        $featureKeys = [
            ['key' => 'max_users', 'type' => 'numeric'],
            ['key' => 'advanced_reports', 'type' => 'boolean'],
            ['key' => 'priority_support', 'type' => 'boolean'],
            ['key' => 'storage_limit', 'type' => 'numeric'],
            ['key' => 'custom_domain', 'type' => 'boolean'],
        ];

        $plans = Plan::factory(5)->create()->each(function ($plan) use ($featureKeys) {
            $planFeatures = [];
            $randomFeatures = collect($featureKeys)->random(rand(2, 4));
            foreach ($randomFeatures as $feature) {
                $planFeatures[$feature['key']] = [
                    'value' => $feature['type'] === 'boolean' ? true : rand(5, 100),
                    'enabled' => true,
                ];
            }
            $plan->features = $planFeatures;
            $plan->save();
        });

        // 5️⃣ Create Subscription for the Tenant
        $plan = $plans->random();

        Subscription::create([
            'customer_id' => $customer->id,
            'plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'start_at' => now(),
            'end_at' => now()->addMonth(),
            'renewal_at' => now()->addMonth(),
            'canceled_at' => null,
            'metadata' => null,
        ]);

        $this->command->info("Seeder finished: SuperAdmin, Customer, Tenant, Plans, Subscription created.");
    }
}
