<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use App\Services\API\Ecommerce\Order\OrderService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {
    }

    public function active(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 10);
        $result = $this->orderService->listActive($request->user(), $perPage);

        return ApiResponse::success($result['data'], $result['message'], $result['code']);
    }

    public function history(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 10);
        $result = $this->orderService->listHistory($request->user(), $perPage);

        return ApiResponse::success($result['data'], $result['message'], $result['code']);
    }

    public function show(Request $request,  string $locale, int $order): JsonResponse
    {
        $result = $this->orderService->getOrder($request->user(), $order);

        if (!$result['success']) {
            return ApiResponse::error($result['message'], $result['code']);
        }

        return ApiResponse::success($result['data'], $result['message'], $result['code']);
    }

    public function tracking(Request $request, string $locale, int $order): JsonResponse
    {
        $result = $this->orderService->tracking($request->user(), $order);

        if (!$result['success']) {
            return ApiResponse::error($result['message'], $result['code']);
        }

        return ApiResponse::success($result['data'], $result['message'], $result['code']);
    }
}
