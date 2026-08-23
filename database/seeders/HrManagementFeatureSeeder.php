<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class HrManagementFeatureSeeder extends Seeder
{
    public function run(): void
    {
        $feature = Feature::query()->updateOrCreate(
            ['key' => 'hr_management'],
            [
                'name' => 'HR Management',
                'type' => 'boolean',
                'description' => 'Enable HR module in dashboard navigation',
                'is_active' => true,
            ]
        );

        Plan::withInactive()->get()->each(function (Plan $plan) use ($feature): void {
            $features = $plan->features ?? [];

            if (!is_array($features)) {
                $features = json_decode((string) $features, true) ?: [];
            }

            $features['hr_management'] = [
                'enabled' => true,
                'value' => true,
            ];

            $plan->features = $features;
            $plan->save();

            $plan->featureDefinitions()->syncWithoutDetaching([
                $feature->id => [
                    'enabled' => true,
                    'value' => '1',
                ],
            ]);
        });
    }
}
