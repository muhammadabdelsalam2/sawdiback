<?php

namespace App\Repositories;

use App\Models\Plan;
use App\Models\Subscription;
use App\Repositories\Contracts\CustomerSubscriptionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerSubscriptionRepository implements CustomerSubscriptionRepositoryInterface
{
    public function getCurrentForTenant(string $tenantId): ?Subscription
    {
        return Subscription::query()
            ->where('tenant_id', $tenantId)
            ->orderByDesc('id')
            ->first();
    }

    public function listActivePlans(int $perPage = 15): LengthAwarePaginator
    {
        // Plan model has global scope active; that's fine for Customer side
        return Plan::query()
            ->with('currency')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function createSubscription(array $data): Subscription
    {
        return Subscription::create($data);
    }

    public function updateSubscription(Subscription $subscription, array $data): Subscription
    {
        $subscription->update($data);
        return $subscription;
    }
}
