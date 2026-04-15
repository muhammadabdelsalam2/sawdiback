<?php

namespace App\Repositories\Contracts\Api;

use App\Models\Order;
use App\Models\OrderReview;
use App\Models\User;

interface ReviewRepositoryInterface
{
    public function findByOrder(User $user, Order $order): ?OrderReview;

    public function create(User $user, Order $order, array $payload): OrderReview;
}
