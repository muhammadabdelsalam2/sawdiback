<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Ecommerce\CartAddItemRequest;
use App\Http\Requests\Api\Ecommerce\CartApplyCouponRequest;
use App\Http\Requests\Api\Ecommerce\CartSetAddressRequest;
use App\Http\Requests\Api\Ecommerce\CartToggleWeeklyDeliveryRequest;
use App\Http\Requests\Api\Ecommerce\CartUpdateItemRequest;
use App\Models\CartItem;
use App\Models\InventoryProduct;
use App\Services\API\Ecommerce\Cart\CartService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $result = $this->cartService->getCart($request->user());

        return ApiResponse::success(
            data: $result['data'],
            message: $result['message'],
            code: $result['code']
        );
    }

    public function add(CartAddItemRequest $request): JsonResponse
    {
        $product = InventoryProduct::query()->findOrFail($request->integer('product_id'));
        $quantity = (float) ($request->input('quantity', 1));

        $result = $this->cartService->addItem($request->user(), $product, $quantity);

        if (!$result['success']) {
            return ApiResponse::error($result['message'], $result['code'], $result['errors'] ?? []);
        }

        return ApiResponse::success($result['data'], $result['message'], $result['code']);
    }

    public function increase(Request $request, string $locale, CartItem $item): JsonResponse
    {
        $result = $this->cartService->updateItemQuantity($request->user(), $item, (float) $item->quantity + 1);

        if (!$result['success']) {
            return ApiResponse::error($result['message'], $result['code'], $result['errors'] ?? []);
        }

        return ApiResponse::success($result['data'], $result['message'], $result['code']);
    }

    public function decrease(Request $request, string $locale, CartItem $item): JsonResponse
    {
        $newQty = max(0, (float) $item->quantity - 1);
        $result = $this->cartService->updateItemQuantity($request->user(), $item, $newQty);

        if (!$result['success']) {
            return ApiResponse::error($result['message'], $result['code'], $result['errors'] ?? []);
        }

        return ApiResponse::success($result['data'], $result['message'], $result['code']);
    }

    public function update(CartUpdateItemRequest $request, CartItem $item): JsonResponse
    {
        $quantity = (float) $request->input('quantity');
        $result = $this->cartService->updateItemQuantity($request->user(), $item, $quantity);

        if (!$result['success']) {
            return ApiResponse::error($result['message'], $result['code'], $result['errors'] ?? []);
        }

        return ApiResponse::success($result['data'], $result['message'], $result['code']);
    }

    public function remove(Request $request, string $locale, CartItem $item): JsonResponse
    {
        $result = $this->cartService->removeItem($request->user(), $item);

        if (!$result['success']) {
            return ApiResponse::error($result['message'], $result['code'], $result['errors'] ?? []);
        }

        return ApiResponse::success($result['data'], $result['message'], $result['code']);
    }

    public function clear(Request $request): JsonResponse
    {
        $result = $this->cartService->clearCart($request->user());

        return ApiResponse::success($result['data'], $result['message'], $result['code']);
    }

    public function applyCoupon(CartApplyCouponRequest $request): JsonResponse
    {
        $result = $this->cartService->applyCoupon($request->user(), $request->string('code')->toString());

        if (!$result['success']) {
            return ApiResponse::error($result['message'], $result['code'], $result['errors'] ?? []);
        }

        return ApiResponse::success($result['data'], $result['message'], $result['code']);
    }

    public function removeCoupon(Request $request): JsonResponse
    {
        $result = $this->cartService->removeCoupon($request->user());

        return ApiResponse::success($result['data'], $result['message'], $result['code']);
    }

    public function weeklyDelivery(CartToggleWeeklyDeliveryRequest $request): JsonResponse
    {
        $result = $this->cartService->toggleWeeklyDelivery($request->user(), (bool) $request->boolean('weekly_delivery'));

        return ApiResponse::success($result['data'], $result['message'], $result['code']);
    }

    public function setAddress(CartSetAddressRequest $request): JsonResponse
    {
        $result = $this->cartService->setAddress($request->user(), $request->integer('address_id'));

        if (!$result['success']) {
            return ApiResponse::error($result['message'], $result['code'], $result['errors'] ?? []);
        }

        return ApiResponse::success($result['data'], $result['message'], $result['code']);
    }
}
