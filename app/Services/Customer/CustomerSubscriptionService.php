<?php

namespace App\Services\Customer;

use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use App\Repositories\Contracts\CustomerSubscriptionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CustomerSubscriptionService
{
    public function __construct(
        private readonly CustomerSubscriptionRepositoryInterface $repo
    ) {}

    public function getCurrent(string $tenantId): ?Subscription
    {
        return $this->repo->getCurrentForTenant($tenantId);
    }

    public function subscribe(string $tenantId, int $customerId, int $planId): Subscription
    {
        return DB::transaction(function () use ($tenantId, $customerId, $planId) {
            $active = Subscription::query()
                ->where('tenant_id', $tenantId)
                ->active()
                ->first();

            if ($active) {
                throw ValidationException::withMessages([
                    'subscription' => 'Tenant already has an active subscription.',
                ]);
            }

            $subscription = $this->repo->createSubscription([
                'tenant_id' => $tenantId,
                'customer_id' => $customerId,
                'plan_id' => $planId,
                'status' => Subscription::STATUS_ACTIVE,
                'start_at' => now(),
                'renewal_at' => now()->addMonth(),
            ]);

            $this->logHistory($subscription->id, 'subscribe', null, Subscription::STATUS_ACTIVE, $customerId, [
                'plan_id' => $planId,
            ]);

            return $subscription;
        });
    }

    public function changePlan(string $tenantId, int $customerId, int $newPlanId): Subscription
    {
        return DB::transaction(function () use ($tenantId, $customerId, $newPlanId) {
            $subscription = Subscription::query()
                ->where('tenant_id', $tenantId)
                ->active()
                ->orderByDesc('id')
                ->first();

            if (!$subscription) {
                throw ValidationException::withMessages([
                    'subscription' => 'No active subscription found for this tenant.',
                ]);
            }

            $oldPlanId = $subscription->plan_id;

            $this->repo->updateSubscription($subscription, [
                'plan_id' => $newPlanId,
            ]);

            $this->logHistory($subscription->id, 'change_plan', $subscription->status, $subscription->status, $customerId, [
                'old_plan_id' => $oldPlanId,
                'new_plan_id' => $newPlanId,
            ]);

            return $subscription->fresh(['plan.currency']);
        });
    }

    public function cancel(string $tenantId, int $customerId): Subscription
    {
        return DB::transaction(function () use ($tenantId, $customerId) {
            $subscription = Subscription::query()
                ->where('tenant_id', $tenantId)
                ->active()
                ->orderByDesc('id')
                ->first();

            if (!$subscription) {
                throw ValidationException::withMessages([
                    'subscription' => 'No active subscription found for this tenant.',
                ]);
            }

            $from = $subscription->status;

            $this->repo->updateSubscription($subscription, [
                'status' => Subscription::STATUS_CANCELED,
                'canceled_at' => now(),
            ]);

            $this->logHistory($subscription->id, 'cancel', $from, Subscription::STATUS_CANCELED, $customerId);

            return $subscription->fresh();
        });
    }

    private function logHistory(
        int $subscriptionId,
        string $action,
        ?string $fromStatus,
        ?string $toStatus,
        ?int $changedBy,
        ?array $payload = null
    ): void {
        SubscriptionHistory::create([
            'subscription_id' => $subscriptionId,
            'action' => $action,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'changed_by' => $changedBy,
            'payload' => $payload,
        ]);
    }
}
