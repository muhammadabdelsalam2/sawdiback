<?php

namespace App\Http\Controllers\Api\Account;

use App\DTOs\Account\UpdateAccountDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Account\ProfileUpdateRequest;
use App\Http\Requests\Api\Account\RequestContactUpdateRequest;
use App\Http\Requests\Api\Account\StoreUserAddressRequest;
use App\Http\Requests\Api\Account\StoreUserPaymentMethodRequest;
use App\Http\Requests\Api\Account\UpdateAccountRequest;
use App\Http\Requests\Api\Account\UpdateUserAddressRequest;
use App\Http\Requests\Api\Account\UpdateUserNotificationSettingsRequest;
use App\Http\Requests\Api\Account\VerifyContactUpdateRequest;
use App\Models\UserAddress;
use App\Models\UserPaymentMethod;
use App\Services\API\Account\AccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct(
        protected AccountService $accountService
    ) {
    }

    /**
     * Existing flow - kept intact
     */
    public function complete(UpdateAccountRequest $request): JsonResponse
    {
        $dto = UpdateAccountDTO::fromRequest($request);
        $result = $this->accountService->updateAccount($dto);

        if (!$result['success']) {
            return response()->json([
                'status' => false,
                'message' => $result['message']
            ], $result['code']);
        }

        return response()->json([
            'status' => true,
            'message' => $result['message'],
            'data' => $result['data']
        ], $result['code']);
    }

    public function overview(Request $request): JsonResponse
    {
        $result = $this->accountService->getProfileOverview($request->route('locale'));

        return response()->json([
            'status' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['code']);
    }

    public function updateProfile(ProfileUpdateRequest $request): JsonResponse
    {
        $result = $this->accountService->updateProfile($request);

        return response()->json([
            'status' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['code']);
    }

    public function requestContactUpdate(RequestContactUpdateRequest $request): JsonResponse
    {
        $result = $this->accountService->requestContactUpdate($request);

        return response()->json([
            'status' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['code']);
    }

    public function verifyContactUpdate(VerifyContactUpdateRequest $request): JsonResponse
    {
        $result = $this->accountService->verifyContactUpdate($request);

        return response()->json([
            'status' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['code']);
    }

    public function addressBook(): JsonResponse
    {
        $result = $this->accountService->getAddressBook();

        return response()->json([
            'status' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['code']);
    }

    public function storeAddress(StoreUserAddressRequest $request): JsonResponse
    {
        $result = $this->accountService->storeAddress($request->validated());

        return response()->json([
            'status' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['code']);
    }

    public function updateAddress(UpdateUserAddressRequest $request, string $locale, string $address): JsonResponse
    {
        $addressModel = UserAddress::query()->findOrFail($address);

        $result = $this->accountService->updateAddress($addressModel, $request->validated());

        return response()->json([
            'status' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['code']);
    }

    public function deleteAddress(string $locale, string $address): JsonResponse
    {
        $addressModel = UserAddress::query()->findOrFail($address);

        $result = $this->accountService->deleteAddress($addressModel);

        return response()->json([
            'status' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['code']);
    }

    public function paymentMethods(): JsonResponse
    {
        $result = $this->accountService->getPaymentMethods();

        return response()->json([
            'status' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['code']);
    }

    public function storePaymentMethod(StoreUserPaymentMethodRequest $request): JsonResponse
    {
        $result = $this->accountService->storePaymentMethod($request->validated());

        return response()->json([
            'status' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['code']);
    }

    public function deletePaymentMethod(string $locale, string $paymentMethod): JsonResponse
    {
        $paymentMethodModel = UserPaymentMethod::query()->findOrFail($paymentMethod);

        $result = $this->accountService->deletePaymentMethod($paymentMethodModel);

        return response()->json([
            'status' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['code']);
    }

    public function notificationSettings(): JsonResponse
    {
        $result = $this->accountService->getNotificationSettings();

        return response()->json([
            'status' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['code']);
    }

    public function updateNotificationSettings(UpdateUserNotificationSettingsRequest $request): JsonResponse
    {
        $result = $this->accountService->updateNotificationSettings($request->validated());

        return response()->json([
            'status' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['code']);
    }

    public function logout(Request $request): JsonResponse
    {
        $result = $this->accountService->logout($request->user());

        return response()->json([
            'status' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['code']);
    }

    public function destroy(Request $request): JsonResponse
    {
        $result = $this->accountService->deleteAccount($request->user());

        return response()->json([
            'status' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'],
        ], $result['code']);
    }
}
