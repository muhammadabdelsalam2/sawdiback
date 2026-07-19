<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Ecommerce\StoreLocationAddressRequest;
use App\Http\Requests\Api\Ecommerce\StoreManualAddressRequest;
use App\Http\Requests\Api\Ecommerce\UpdateAddressRequest;
use App\Models\UserAddress;
use App\Services\API\Account\AccountService;
use App\Services\API\Ecommerce\Cart\CartService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct(
        protected AccountService $accountService,
        protected CartService $cartService
    ) {
    }

    public function index(): JsonResponse
    {
        $result = $this->accountService->getAddressBook();

        return ApiResponse::success($result['data'], $result['message'], $result['code']);
    }

    public function storeManual(StoreManualAddressRequest $request): JsonResponse
    {
        $result = $this->accountService->storeAddress($request->validated());

        if (!$result['success']) {
            return ApiResponse::error($result['message'], $result['code'], $result['data'] ?? []);
        }

        return ApiResponse::success($result['data'], $result['message'], $result['code']);
    }

    public function storeLocation(StoreLocationAddressRequest $request): JsonResponse
    {
        $result = $this->accountService->storeAddress($request->validated());

        if (!$result['success']) {
            return ApiResponse::error($result['message'], $result['code'], $result['data'] ?? []);
        }

        return ApiResponse::success($result['data'], $result['message'], $result['code']);
    }

    public function show(Request $request, string $locale, UserAddress $address): JsonResponse
    {
        if ($address->user_id !== $request->user()->id) {
            return ApiResponse::error(__('ecommerce.address.not_found'), 404);
        }

        return ApiResponse::success(
            data: new \App\Http\Resources\UserAddressResource($address),
            message: __('ecommerce.address.loaded'),
            code: 200
        );
    }

    public function update(UpdateAddressRequest $request, string $locale,UserAddress $address): JsonResponse
    {
        $result = $this->accountService->updateAddress($address, $request->validated());

        if (!$result['success']) {
            return ApiResponse::error($result['message'], $result['code'], $result['data'] ?? []);
        }

        return ApiResponse::success($result['data'], $result['message'], $result['code']);
    }

    public function delete(string $locale, UserAddress $address): JsonResponse
    {
        $result = $this->accountService->deleteAddress($address);

        if (!$result['success']) {
            return ApiResponse::error($result['message'], $result['code'], $result['data'] ?? []);
        }

        return ApiResponse::success($result['data'], $result['message'], $result['code']);
    }

    public function setDefault(Request $request, string $locale,UserAddress $address): JsonResponse
    {
        $result = $this->accountService->updateAddress($address, ['is_default' => true]);

        if (!$result['success']) {
            return ApiResponse::error($result['message'], $result['code'], $result['data'] ?? []);
        }

        return ApiResponse::success($result['data'], __('ecommerce.address.default_updated'), 200);
    }

    public function selectForCheckout(Request $request, string $locale, UserAddress $address): JsonResponse
    {
        if ($address->user_id !== $request->user()->id) {
            return ApiResponse::error(__('ecommerce.address.not_found'), 404);
        }

        $result = $this->cartService->setAddress($request->user(), $address->id);

        if (!$result['success']) {
            return ApiResponse::error($result['message'], $result['code'], $result['errors'] ?? []);
        }

        return ApiResponse::success($result['data'], $result['message'], $result['code']);
    }
}
