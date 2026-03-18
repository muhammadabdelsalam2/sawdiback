<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\PlusSubscription;
use App\Models\PlusSubscriptionSkip;
use App\Models\User;
use App\Repositories\Contracts\Api\PlusRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class PlusRepository implements PlusRepositoryInterface
{
    public function findLatestForUser(User $user): ?PlusSubscription
    {
        return PlusSubscription::query()
            ->where('tenant_id', $this->resolveTenantId($user))
            ->where('user_id', $user->id)
            ->latest('id')
            ->first();
    }

    public function findCurrentForUser(User $user): ?PlusSubscription
    {
        $tenantId = $this->resolveTenantId($user, false);

        if (!$tenantId) {
            return null;
        }

        return PlusSubscription::query()
            ->where('tenant_id', $tenantId)
            ->where('user_id', $user->id)
            ->whereIn('status', [
                PlusSubscription::STATUS_ACTIVE,
                PlusSubscription::STATUS_PAUSED,
            ])
            ->latest('id')
            ->first();
    }

    public function findActiveForUser(User $user): ?PlusSubscription
    {
        $tenantId = $this->resolveTenantId($user, false);

        if (!$tenantId) {
            return null;
        }

        return PlusSubscription::query()
            ->where('tenant_id', $tenantId)
            ->where('user_id', $user->id)
            ->where('status', PlusSubscription::STATUS_ACTIVE)
            ->latest('id')
            ->first();
    }

    public function createForUser(User $user, array $data): PlusSubscription
    {
        $data['tenant_id'] = $this->resolveTenantId($user);
        $data['user_id'] = $user->id;

        return PlusSubscription::create($data);
    }

    public function update(PlusSubscription $subscription, array $data): PlusSubscription
    {
        $subscription->update($data);

        return $subscription->refresh();
    }

    public function syncCategories(PlusSubscription $subscription, array $categoryIds): PlusSubscription
    {
        $subscription->categories()->sync($categoryIds);

        return $this->loadFull($subscription);
    }

    public function loadFull(PlusSubscription $subscription): PlusSubscription
    {
        return $subscription->load([
            'address',
            'paymentMethod',
            'categories.translation',
            'categories.translations',
            'skips',
        ]);
    }

    public function getUserAddresses(User $user): Collection
    {
        return $user->addresses()
            ->orderByDesc('is_default')
            ->latest('id')
            ->get();
    }

    public function getUserPaymentMethods(User $user): Collection
    {
        return $user->paymentMethods()
            ->orderByDesc('is_default')
            ->latest('id')
            ->get();
    }

    public function getAvailableCategories(): Collection
    {
        return Category::query()
            ->with(['translation', 'translations'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();
    }

    public function createSkip(PlusSubscription $subscription, array $data): PlusSubscriptionSkip
    {
        return $subscription->skips()->create($data);
    }

    public function getSkipDates(PlusSubscription $subscription): Collection
    {
        return $subscription->skips()
            ->where('action', PlusSubscriptionSkip::ACTION_SKIP_ONCE)
            ->whereNotNull('scheduled_for')
            ->pluck('scheduled_for')
            ->map(fn ($date) => \Carbon\Carbon::parse($date)->toDateString());
    }

    protected function resolveTenantId(User $user, bool $failIfMissing = true): ?string
    {
        $tenantId = $user->tenant_id ?: User::query()->whereKey($user->id)->value('tenant_id');

        if (!$tenantId && $failIfMissing) {
            throw ValidationException::withMessages([
                'tenant_id' => ['The authenticated user is not linked to any tenant.'],
            ]);
        }

        return $tenantId;
    }
}
