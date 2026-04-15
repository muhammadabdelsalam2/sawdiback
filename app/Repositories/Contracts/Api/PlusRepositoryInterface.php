<?php

namespace App\Repositories\Contracts\Api;

use App\Models\PlusSubscription;
use App\Models\PlusSubscriptionSkip;
use App\Models\User;
use Illuminate\Support\Collection;

interface PlusRepositoryInterface
{
    public function findLatestForUser(User $user): ?PlusSubscription;

    public function findCurrentForUser(User $user): ?PlusSubscription;

    public function findActiveForUser(User $user): ?PlusSubscription;

    public function createForUser(User $user, array $data): PlusSubscription;

    public function update(PlusSubscription $subscription, array $data): PlusSubscription;

    public function syncCategories(PlusSubscription $subscription, array $categoryIds): PlusSubscription;

    public function loadFull(PlusSubscription $subscription): PlusSubscription;

    public function getUserAddresses(User $user): Collection;

    public function getUserPaymentMethods(User $user): Collection;

    public function getAvailableCategories(): Collection;

    public function createSkip(PlusSubscription $subscription, array $data): PlusSubscriptionSkip;

    public function getSkipDates(PlusSubscription $subscription): Collection;
}
