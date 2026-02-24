<?php

namespace App\Services;

use App\Models\User;
use App\Models\Subscription;

class PlanFeatureService
{
    /**
     * Build subscription + feature context for views / UI.
     */
    public function contextFor(?User $user): array
    {
        if (!$user) {
            return [
                'hasActiveSubscription' => false,
                'activeSubscription'    => null,
                'planFeatures'          => [],
                'featureFlags'          => [],
            ];
        }

        // Latest active subscription for this customer
        $activeSubscription = Subscription::query()
            ->with('plan') // ensure plan is loaded if relation exists
            ->where('customer_id', $user->id)  // ✅ correct column
            ->where('status', 'active')
            ->latest('id')
            ->first();

        // Plan priority: active subscription plan, else tenant plan
        $plan = $activeSubscription?->plan ?? $user->tenant?->plan;

        // Resolve plan features (array or JSON string)
        $planFeatures = $plan?->features ?? [];
        if (!is_array($planFeatures)) {
            $planFeatures = json_decode((string) $planFeatures, true) ?: [];
        }

        // Build boolean flags for quick checks in views
        $featureFlags = [];
        foreach ($planFeatures as $key => $data) {
            $featureFlags[$key] = is_array($data)
                ? (bool) ($data['enabled'] ?? false)
                : (bool) $data;
        }

        return [
            'hasActiveSubscription' => (bool) $activeSubscription,
            'activeSubscription'    => $activeSubscription,
            'planFeatures'          => $planFeatures,
            'featureFlags'          => $featureFlags,
        ];
    }
}
