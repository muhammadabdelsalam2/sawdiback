<?php

namespace App\Services\API\Ecommerce\Review;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use App\Repositories\Contracts\Api\OrderRepositoryInterface;
use App\Repositories\Contracts\Api\ReviewRepositoryInterface;
use App\Support\ServiceResult;

class ReviewService
{
    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        protected ReviewRepositoryInterface $reviewRepository
    ) {
    }

    public function openReview(User $user, int $orderId): array
    {
        $order = $this->orderRepository->findForUser($user, $orderId);

        if (!$order) {
            return ServiceResult::error('Order not found.', code: 404);
        }

        $order->load(['items', 'review', 'address']);

        $canReview = $order->status === Order::STATUS_DELIVERED && !$order->review;

        return ServiceResult::success(
            data: [
                'order' => new OrderResource($order),
                'can_review' => $canReview,
                'already_reviewed' => (bool) $order->review,
                'reasons' => config('ecommerce.review.reasons', []),
                'rating_scale' => [
                    'min' => (int) config('ecommerce.review.min_rating', 1),
                    'max' => (int) config('ecommerce.review.max_rating', 5),
                ],
            ],
            message: 'Review screen data loaded successfully.',
            code: 200
        );
    }

    public function submitReview(User $user, int $orderId, array $payload): array
    {
        $order = $this->orderRepository->findForUser($user, $orderId);

        if (!$order) {
            return ServiceResult::error('Order not found.', code: 404);
        }

        $order->load('review');

        if ($order->status !== Order::STATUS_DELIVERED) {
            return ServiceResult::error('Only delivered orders can be reviewed.', code: 422);
        }

        if ($order->review) {
            return ServiceResult::error('This order was already reviewed.', code: 409);
        }

        $review = $this->reviewRepository->create($user, $order, $payload);

        return ServiceResult::success(
            data: [
                'review_id' => $review->id,
            ],
            message: 'Review submitted successfully.',
            code: 201
        );
    }
}
