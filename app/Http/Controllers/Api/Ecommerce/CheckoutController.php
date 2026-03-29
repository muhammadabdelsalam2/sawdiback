<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Ecommerce\CheckoutPlaceOrderRequest;
use App\Services\API\Ecommerce\Checkout\CheckoutService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        protected CheckoutService $checkoutService
    ) {
    }

    public function summary(Request $request): JsonResponse
    {
        $result = $this->checkoutService->summary($request->user());

        return ApiResponse::success($result['data'], $result['message'], $result['code']);
    }

    public function placeOrder(CheckoutPlaceOrderRequest $request): JsonResponse
    {
        $result = $this->checkoutService->placeOrder($request->user(), $request->validated());

        if (!$result['success']) {
            return ApiResponse::error($result['message'], $result['code'], $result['errors'] ?? []);
        }

        return ApiResponse::success($result['data'], $result['message'], $result['code']);
    }
}
