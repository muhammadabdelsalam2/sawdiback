<?php

namespace App\Services\Subscriptions;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function create(array $data, ?int $actorId = null): Subscription
    {
        return DB::transaction(function () use ($data, $actorId) {
            $plan = Plan::query()->findOrFail($data['plan_id']);
            $startAt = isset($data['start_at']) ? Carbon::parse($data['start_at']) : now();
            $endAt = $this->calculateEndAt($startAt, $plan->billing_cycle);

            $subscription = Subscription::query()->create([
                'customer_id' => $data['customer_id'],
                'plan_id' => $plan->id,
                'status' => Subscription::STATUS_ACTIVE,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'renewal_at' => $endAt,
                'metadata' => $data['metadata'] ?? null,
            ]);

            $this->log($subscription, 'created', null, Subscription::STATUS_ACTIVE, $actorId, [
                'plan_id' => $plan->id,
            ]);

            return $subscription->fresh(['plan.currency', 'customer']);
        });
    }

    public function upgradeOrDowngrade(Subscription $subscription, int $newPlanId, ?int $actorId = null): Subscription
    {
        return DB::transaction(function () use ($subscription, $newPlanId, $actorId) {
            $newPlan = Plan::query()->findOrFail($newPlanId);
            $oldPlanId = $subscription->plan_id;
            $oldStatus = $subscription->status;

            $subscription->update([
                'plan_id' => $newPlan->id,
                'status' => Subscription::STATUS_ACTIVE,
            ]);

            $this->log($subscription, 'plan_changed', $oldStatus, Subscription::STATUS_ACTIVE, $actorId, [
                'old_plan_id' => $oldPlanId,
                'new_plan_id' => $newPlan->id,
            ]);

            return $subscription->fresh(['plan.currency', 'customer']);
        });
    }

    public function renew(Subscription $subscription, ?string $fromDate = null, ?int $actorId = null): Subscription
    {
        return DB::transaction(function () use ($subscription, $fromDate, $actorId) {
            $baseDate = $fromDate ? Carbon::parse($fromDate) : ($subscription->end_at ?? now());
            $newEndAt = $this->calculateEndAt($baseDate, $subscription->plan->billing_cycle);
            $oldStatus = $subscription->status;

            $subscription->update([
                'status' => Subscription::STATUS_ACTIVE,
                'end_at' => $newEndAt,
                'renewal_at' => $newEndAt,
                'canceled_at' => null,
            ]);

            $this->log($subscription, 'renewed', $oldStatus, Subscription::STATUS_ACTIVE, $actorId, [
                'renewal_at' => $newEndAt->toDateTimeString(),
            ]);

            return $subscription->fresh(['plan.currency', 'customer']);
        });
    }

    public function cancel(Subscription $subscription, ?int $actorId = null): Subscription
    {
        return DB::transaction(function () use ($subscription, $actorId) {
            $oldStatus = $subscription->status;

            $subscription->update([
                'status' => Subscription::STATUS_CANCELED,
                'canceled_at' => now(),
            ]);

            $this->log($subscription, 'canceled', $oldStatus, Subscription::STATUS_CANCELED, $actorId);

            return $subscription->fresh(['plan.currency', 'customer']);
        });
    }

    public function expire(Subscription $subscription, ?int $actorId = null): Subscription
    {
        return DB::transaction(function () use ($subscription, $actorId) {
            $oldStatus = $subscription->status;

            $subscription->update([
                'status' => Subscription::STATUS_EXPIRED,
            ]);

            $this->log($subscription, 'expired', $oldStatus, Subscription::STATUS_EXPIRED, $actorId);

            return $subscription->fresh(['plan.currency', 'customer']);
        });
    }

    protected function calculateEndAt(Carbon $startAt, string $billingCycle): Carbon
    {
        return match ($billingCycle) {
            'monthly' => (clone $startAt)->addMonth(),
            'yearly' => (clone $startAt)->addYear(),
            'weekly' => (clone $startAt)->addWeek(),
            default => (clone $startAt)->addMonth(),
        };
    }

    protected function log(
        Subscription $subscription,
        string $action,
        ?string $fromStatus,
        ?string $toStatus,
        ?int $actorId,
        ?array $payload = null
    ): void {
        SubscriptionHistory::query()->create([
            'subscription_id' => $subscription->id,
            'action' => $action,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'changed_by' => $actorId,
            'payload' => $payload,
        ]);
    }
}
