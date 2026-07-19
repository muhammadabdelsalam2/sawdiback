<?php

namespace App\Http\Controllers\Api\Plus;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Plus\SkipPlusSubscriptionRequest;
use App\Http\Requests\Api\Plus\StorePlusSubscriptionRequest;
use App\Http\Requests\Api\Plus\UpdatePlusSubscriptionSettingsRequest;
use App\Services\API\Plus\PlusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlusController extends Controller
{
    public function __construct(
        protected PlusService $plusService
    ) {
    }

    public function overview(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => __('plus.api.overview_loaded'),
            'data' => $this->plusService->overview($request->user()),
        ]);
    }

    public function setup(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => __('plus.api.setup_loaded'),
            'data' => $this->plusService->setup($request->user()),
        ]);
    }

    public function store(StorePlusSubscriptionRequest $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => __('plus.api.subscription_created'),
            'data' => $this->plusService->subscribe($request->user(), $request->validated()),
        ], 201);
    }

    public function manage(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => __('plus.api.management_loaded'),
            'data' => $this->plusService->manage($request->user()),
        ]);
    }

    public function manageSubscription(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => __('plus.api.settings_loaded'),
            'data' => $this->plusService->manageSubscription($request->user()),
        ]);
    }

    public function updateManageSubscription(UpdatePlusSubscriptionSettingsRequest $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => __('plus.api.settings_updated'),
            'data' => $this->plusService->updateManageSubscription($request->user(), $request->validated()),
        ]);
    }

    public function skip(SkipPlusSubscriptionRequest $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => __('plus.api.delivery_settings_updated'),
            'data' => $this->plusService->skip($request->user(), $request->validated()),
        ]);
    }
}
