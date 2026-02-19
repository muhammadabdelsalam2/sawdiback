<?php

namespace App\Services;

use App\Models\User;
use App\Models\Plan;

class PlanService
{
    /**
     * Return user's active plan (customize if you have different relations).
     */
    public function getUserPlan(?User $user): ?Plan
    {
        if (!$user) {
            return null;
        }

        // If you have a direct plan relation:
        if (method_exists($user, 'plan') && $user->plan) {
            return $user->plan;
        }

        // If you have a subscription relation:
        if (method_exists($user, 'subscription') && $user->subscription) {
            return $user->subscription->plan ?? null;
        }

        return null;
    }

    /**
     * Resolve plan features as array.
     * This exists because your User::hasPlanFeature() expects it.
     */
    public function resolvedFeatures(?User $user): array
    {
        $plan = $this->getUserPlan($user);

        if (!$plan) {
            return [];
        }

        $features = $plan->features ?? [];

        // Handle legacy string JSON
        if (!is_array($features)) {
            $features = json_decode((string) $features, true) ?: [];
        }

        return $features;
    }

    /**
     * Check if a feature is enabled.
     * Convention: feature["enabled"] is the source of truth.
     */
    public function hasFeature(?User $user, string $featureKey): bool
    {
        $features = $this->resolvedFeatures($user);

        $feature = $features[$featureKey] ?? null;

        if (is_array($feature)) {
            return (bool) ($feature['enabled'] ?? false);
        }

        return (bool) $feature;
    }
}
