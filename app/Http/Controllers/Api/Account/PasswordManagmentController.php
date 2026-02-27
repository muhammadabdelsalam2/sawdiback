<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Password\ForgotPasswordRequest;
use App\Http\Requests\Api\Password\ResetPasswordRequest;
use App\Services\API\Password\PasswordService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use App\Enums\OtpType;
use App\Http\Requests\Api\Password\VerifyForgetPasswordRequest;
use App\Repositories\OtpRepository;
use App\Support\ServiceResult;

class PasswordManagmentController extends Controller
{

    //
    public function __construct(
        private PasswordService $passwordService,
        private UserRepository $userRepository,
        protected OtpRepository $otpRepository,

    ) {
    }


    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $result = $this->passwordService->forgotPassword(
            $request->identifier
        );

        if (!$result['success']) {
            return ApiResponse::error(
                $result['message'],
                $result['code'],
                $result['errors'],
                $result['nextEndpoint'],
            );
        }
        return ApiResponse::success(
            $result['data'],
            $result['message'],
            $result['code'],
            $result['nextEndpoint'],
        );
    }



    public function verifyOtp(VerifyForgetPasswordRequest $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string']
        ]);
        $result = $this->passwordService->verifyOtp(
            $request->code
        );
        if (!$result['success']) {
            return ApiResponse::error(
                $result['message'],
                $result['code'],
                $result['errors'] ?? [],
            );
        }

        return ApiResponse::success(
            $result['data'],
            $result['message'],
            $result['code'],
            $result['nextEndpoint']
        );
    }


    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $result = $this->passwordService->resetPassword(
            $request->token,
            $request->new_password
        );

        if (!$result['success']) {
            return ApiResponse::error(
                $result['message'],
                $result['code'],
                $result['errors'] ?? [],
                $result['nextEndpoint'] ?? null,
            );
        }

        return ApiResponse::success(
            $result['data'],
            $result['message'],
            $result['code']
        );
    }



}
