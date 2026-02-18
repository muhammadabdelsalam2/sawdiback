<?php

namespace App\Repositories\Contracts;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CustomerSubscriptionRepositoryInterface
{
    public function getLatestForTenant(string $tenantId): ?Subscription;

    public function getActiveForTenant(string $tenantId): ?Subscription;

    public function getPendingForTenant(string $tenantId): ?Subscription;

    public function listActivePlans(int $perPage = 15): LengthAwarePaginator;

    public function createSubscription(array $data): Subscription;

    public function updateSubscription(Subscription $subscription, array $data): Subscription;
}
