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

    public function getLatest(string $tenantId): ?Subscription
    {
        return $this->repo->getLatestForTenant($tenantId);
    }

    public function getActive(string $tenantId): ?Subscription
    {
        return $this->repo->getActiveForTenant($tenantId);
    }

    public function getPending(string $tenantId): ?Subscription
    {
        return $this->repo->getPendingForTenant($tenantId);
    }

    /**
     * Customer flow: create REQUEST only (PENDING).
     * Activation must happen via SuperAdmin approval/payment flow later.
     */
    public function subscribe(string $tenantId, int $customerId, int $planId): Subscription
    {
        return DB::transaction(function () use ($tenantId, $customerId, $planId) {

            $active = $this->repo->getActiveForTenant($tenantId);
            if ($active) {
                throw ValidationException::withMessages([
                    'subscription' => 'Tenant already has an active subscription.',
                ]);
            }

            $pending = $this->repo->getPendingForTenant($tenantId);
            if ($pending) {
                throw ValidationException::withMessages([
                    'subscription' => 'There is already a pending subscription request.',
                ]);
            }

            // Create a pending request (no dates yet)
            $subscription = $this->repo->createSubscription([
                'tenant_id'   => $tenantId,
                'customer_id' => $customerId,
                'plan_id'     => $planId,
                'status'      => Subscription::STATUS_PENDING,
                'start_at'    => null,
                'end_at'      => null,
                'renewal_at'  => null,
                'canceled_at' => null,
                'metadata'    => [
                    'request_type' => 'new_subscription',
                    'requested_plan_id' => $planId,
                    'payment_mode' => 'manual_or_checkout',
                ],
            ]);

            $this->logHistory(
                $subscription->id,
                'requested_subscription',
                null,
                Subscription::STATUS_PENDING,
                $customerId,
                ['plan_id' => $planId]
            );

            return $subscription->fresh(['plan.currency']);
        });
    }

    /**
     * Customer change plan: REQUEST only (PENDING).
     * No direct update on active subscription.
     */
    public function changePlan(string $tenantId, int $customerId, int $newPlanId): Subscription
    {
        return DB::transaction(function () use ($tenantId, $customerId, $newPlanId) {

            $pending = $this->repo->getPendingForTenant($tenantId);
            if ($pending) {
                throw ValidationException::withMessages([
                    'subscription' => 'There is already a pending subscription request.',
                ]);
            }

            $active = $this->repo->getActiveForTenant($tenantId);
            if (!$active) {
                throw ValidationException::withMessages([
                    'subscription' => 'No active subscription found for this tenant.',
                ]);
            }

            if ((int) $active->plan_id === (int) $newPlanId) {
                throw ValidationException::withMessages([
                    'plan_id' => 'You are already subscribed to this plan.',
                ]);
            }

            // Create a pending request record (separate row) to be approved/processed later
            $request = $this->repo->createSubscription([
                'tenant_id'   => $tenantId,
                'customer_id' => $customerId,
                'plan_id'     => $newPlanId,
                'status'      => Subscription::STATUS_PENDING,
                'start_at'    => null,
                'end_at'      => null,
                'renewal_at'  => null,
                'canceled_at' => null,
                'metadata'    => [
                    'request_type' => 'change_plan',
                    'requested_plan_id' => $newPlanId,
                    'from_subscription_id' => $active->id,
                    'from_plan_id' => $active->plan_id,
                    'payment_mode' => 'manual_or_checkout',
                ],
            ]);

            $this->logHistory(
                $request->id,
                'requested_change_plan',
                null,
                Subscription::STATUS_PENDING,
                $customerId,
                [
                    'old_plan_id' => $active->plan_id,
                    'new_plan_id' => $newPlanId,
                ]
            );

            return $request->fresh(['plan.currency']);
        });
    }

    /**
     * Customer cancel is allowed only when ACTIVE.
     */
    public function cancel(string $tenantId, int $customerId): Subscription
    {
        return DB::transaction(function () use ($tenantId, $customerId) {

            $subscription = $this->repo->getActiveForTenant($tenantId);

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

            return $subscription->fresh(['plan.currency']);
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
