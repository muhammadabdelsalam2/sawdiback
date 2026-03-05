<?php

namespace App\Http\Controllers\Api\Account;

use App\DTOs\Account\UpdateAccountDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Account\UpdateAccountRequest;
use App\Services\API\Account\AccountService;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    public function __construct(
        protected AccountService $accountService
    ) {}

    public function complete(UpdateAccountRequest $request): JsonResponse
    {
        $dto = UpdateAccountDTO::fromRequest($request);
    dd($dto);
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
        ]);
    }
}