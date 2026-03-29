<?php

namespace App\Repositories\Contracts\Api;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function createForUser(User $user, array $data): Order;

    public function addStatusHistory(Order $order, ?string $fromStatus, string $toStatus, ?int $changedBy = null, ?string $notes = null): OrderStatusHistory;

    public function listActiveForUser(User $user, int $perPage = 10): LengthAwarePaginator;

    public function listHistoryForUser(User $user, int $perPage = 10): LengthAwarePaginator;

    public function findForUser(User $user, int $orderId): ?Order;
}
