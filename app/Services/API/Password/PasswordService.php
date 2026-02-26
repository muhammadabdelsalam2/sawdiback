<?php

namespace App\Services\API\Password;

use App\Http\Requests\Api\Password\ResetPasswordRequest;
use App\Http\Requests\Api\Password\ForgotPasswordRequest;
use App\DTOs\Auth\SendOtpDTO;
use App\Repositories\UserRepository;
use App\Services\API\Auth\OtpService;
use App\Enums\OtpType;
use App\Support\ServiceResult;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Route;

class PasswordService
{


    protected UserRepository $userRepository;
    protected OtpService $otpService;

    public function __construct(
        UserRepository $userRepository,
        OtpService $otpService
    ) {
        $this->userRepository = $userRepository;
        $this->otpService = $otpService;
    }

public function forgotPassword(string $identifier): array
{
    $user = $this->userRepository->findByIdentifierValue($identifier);

    if (!$user) {
   return ServiceResult::error(
    message: __('auth.user_not_found'),
    nextEndpoint: route('api.auth.register'),
    errors: ['identifier' => __('auth.user_not_found')],
    code: 404
);
    }

    $dto = new SendOtpDTO(
        identifier: $identifier,
        type: OtpType::FORGOT_PASSWORD
    );

    $this->otpService->send($dto);

return ServiceResult::success(
    message: __('auth.otp_sent'),
    data: null,
    code: 200,
    nextEndpoint: route('api.password.change')
);
}


}
