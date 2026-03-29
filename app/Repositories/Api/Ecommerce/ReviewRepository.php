<?php

namespace App\Repositories\Api\Ecommerce;

use App\Models\Order;
use App\Models\OrderReview;
use App\Models\User;
use App\Repositories\Contracts\Api\ReviewRepositoryInterface;

class ReviewRepository implements ReviewRepositoryInterface
{
    public function findByOrder(User $user, Order $order): ?OrderReview
    {
        return OrderReview::query()
            ->where('order_id', $order->id)
            ->where('user_id', $user->id)
            ->first();
    }

    public function create(User $user, Order $order, array $payload): OrderReview
    {
        return OrderReview::query()->create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'rating' => $payload['rating'],
            'title' => $payload['title'] ?? null,
            'review' => $payload['review'] ?? null,
            'reasons' => $payload['reasons'] ?? null,
            'images' => $payload['images'] ?? null,
            'submitted_at' => now(),
        ]);
    }
}
