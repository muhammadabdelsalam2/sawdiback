<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Account\ConvertWalletPointsRequest;
use App\Http\Requests\Api\Account\TopUpWalletRequest;
use App\Http\Resources\WalletOverviewResource;
use App\Http\Resources\WalletTransactionResource;
use App\Services\API\Account\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(
        protected WalletService $walletService
    ) {
    }

    public function show(Request $request): JsonResponse
    {
        $wallet = $this->walletService->getOverview($request->user());

        return response()->json([
            'status' => true,
            'message' => 'Wallet loaded successfully.',
            'data' => new WalletOverviewResource($wallet),
        ]);
    }

    public function topUp(TopUpWalletRequest $request): JsonResponse
    {
        $result = $this->walletService->topUp($request->user(), $request->validated());

        $walletData = (new WalletOverviewResource($result['wallet']))->resolve($request);

        return response()->json([
            'status' => true,
            'message' => 'Wallet topped up successfully.',
            'data' => array_merge($walletData, [
                'transaction' => (new WalletTransactionResource($result['transaction']))->resolve($request),
            ]),
        ]);
    }

    public function convertPoints(ConvertWalletPointsRequest $request): JsonResponse
    {
        $result = $this->walletService->convertPoints($request->user(), $request->validated());

        $walletData = (new WalletOverviewResource($result['wallet']))->resolve($request);

        return response()->json([
            'status' => true,
            'message' => 'Points converted successfully.',
            'data' => array_merge($walletData, [
                'transaction' => (new WalletTransactionResource($result['transaction']))->resolve($request),
            ]),
        ]);
    }
}
