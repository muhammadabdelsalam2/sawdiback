<?php

namespace App\Repositories\Api\Ecommerce;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\User;
use App\Repositories\Contracts\Api\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class OrderRepository implements OrderRepositoryInterface
{
    public function createForUser(User $user, array $data): Order
    {
        $data['tenant_id'] = $this->resolveTenantId($user);
        $data['user_id'] = $user->id;

        return Order::query()->create($data);
    }

    public function addStatusHistory(Order $order, ?string $fromStatus, string $toStatus, ?int $changedBy = null, ?string $notes = null): OrderStatusHistory
    {
        return $order->statusHistories()->create([
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'changed_at' => now(),
            'changed_by' => $changedBy,
            'notes' => $notes,
        ]);
    }

    public function listActiveForUser(User $user, int $perPage = 10): LengthAwarePaginator
    {
        $tenantId = $this->resolveTenantId($user, false);

        return Order::query()
            ->where('user_id', $user->id)
            
            ->whereIn('status', Order::ACTIVE_STATUSES)
            ->latest('id')
            ->paginate($perPage);
    }

    public function listHistoryForUser(User $user, int $perPage = 10): LengthAwarePaginator
    {
        $tenantId = $this->resolveTenantId($user, false);

        return Order::query()
            ->where('user_id', $user->id)
            
            ->whereIn('status', Order::HISTORY_STATUSES)
            ->latest('id')
            ->paginate($perPage);
    }

    public function findForUser(User $user, int $orderId): ?Order
    {
        $tenantId = $this->resolveTenantId($user, false);

        return Order::query()
            ->whereKey($orderId)
            ->where('user_id', $user->id)
            
            ->first();
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
