<?php

namespace App\Services\API\Ecommerce\Order;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use App\Repositories\Contracts\Api\OrderRepositoryInterface;
use App\Support\ServiceResult;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderService
{
    public function __construct(
        protected OrderRepositoryInterface $orderRepository
    ) {
    }

    public function listActive(User $user, int $perPage = 10): array
    {
        $orders = $this->orderRepository->listActiveForUser($user, $perPage);

        return ServiceResult::success(
            data: [
                'items' => OrderResource::collection($orders),
                'pagination' => $this->paginationMeta($orders),
            ],
            message: 'Active orders loaded successfully.',
            code: 200
        );
    }

    public function listHistory(User $user, int $perPage = 10): array
    {
        $orders = $this->orderRepository->listHistoryForUser($user, $perPage);

        return ServiceResult::success(
            data: [
                'items' => OrderResource::collection($orders),
                'pagination' => $this->paginationMeta($orders),
            ],
            message: 'Order history loaded successfully.',
            code: 200
        );
    }

    public function getOrder(User $user, int $orderId): array
    {
        $order = $this->orderRepository->findForUser($user, $orderId);

        if (!$order) {
            return ServiceResult::error('Order not found.', code: 404);
        }

        $order->load(['items', 'address', 'statusHistories', 'review']);

        $canReview = $order->status === Order::STATUS_DELIVERED && !$order->review;
        $order->setAttribute('can_review', $canReview);
        $order->setAttribute('reviewed', (bool) $order->review);

        return ServiceResult::success(
            data: [
                'order' => new OrderResource($order),
            ],
            message: 'Order details loaded successfully.',
            code: 200
        );
    }

    public function tracking(User $user, int $orderId): array
    {
        $order = $this->orderRepository->findForUser($user, $orderId);

        if (!$order) {
            return ServiceResult::error('Order not found.', code: 404);
        }

        $order->load(['statusHistories', 'items']);

        $timeline = $order->statusHistories
            ->sortBy('changed_at')
            ->values()
            ->map(function ($history) {
                return [
                    'from' => $history->from_status,
                    'to' => $history->to_status,
                    'changed_at' => $history->changed_at?->toISOString(),
                    'notes' => $history->notes,
                ];
            });

        return ServiceResult::success(
            data: [
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'status' => $order->status,
                'estimated_delivery_at' => $order->estimated_delivery_at?->toISOString(),
                'timeline' => $timeline,
                'items_count' => $order->items->count(),
                'support' => config('ecommerce.support'),
            ],
            message: 'Order tracking loaded successfully.',
            code: 200
        );
    }

    protected function paginationMeta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }
}
