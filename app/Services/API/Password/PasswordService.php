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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Repositories\OtpRepository;
use App\Http\Requests\Api\Password\VerifyForgetPasswordRequest;

class PasswordService
{


    protected UserRepository $userRepository;
    protected OtpService $otpService;

    public function __construct(
        UserRepository $userRepository,

        protected OtpRepository $otpRepository,
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

       $otp =  $this->otpService->send($dto, $user);
        return ServiceResult::success(
            message: __('auth.otp_sent'),
            data: ['otp' => $otp?->code],
            code: 200,
            nextEndpoint: route('api.password.change')
        );
    }


    public function resetPassword(string $passwordResetToken, string $password): array
    {
        $user = $this->userRepository->findByPasswordResetToken($passwordResetToken);

        // Check password reset token Expire 10 minutes

        if (!$user) {
            return ServiceResult::error(
                message: __('auth.invalid_password_reset_token'),
                nextEndpoint: route('api.password.forget'),
                errors: ['token' => __('auth.invalid_password_reset_token')],
                code: 403
            );
        }

        //  Check if OTP was verified
        $verifiedOtp = $this->otpRepository->findLastVerifiedOtp(
            $user->id,
            OtpType::FORGOT_PASSWORD
        );
        // check if User Has Password Reset Token or Not
        if (!$user->password_reset_token) {
            return ServiceResult::error(
                message: __('auth.otp_not_verified'),
                errors: ['identifier' => __('auth.otp_not_verified')],
                code: 403
            );
        }
        //  Check Expire Time of Password Reset Token
        if ($user->password_reset_at->addMinutes(10)->isPast()) {
            $this->userRepository->clearPasswordResetToken($user);
            return ServiceResult::error(
                message: __('auth.password_reset_token_expired'),
                nextEndpoint: route('api.password.forget'),
                errors: ['identifier' => __('auth.password_reset_token_expired')],
                code: 403
            );
        }
        // Update Password and Clear Password Reset Token
        $this->userRepository->update($user, [
            'password' => Hash::make($password),
        ]);


        $this->userRepository->clearPasswordResetToken($user);

        if (!$verifiedOtp) {
            return ServiceResult::error(
                message: __('auth.otp_not_verified'),
                errors: ['identifier' => __('auth.otp_not_verified')],
                code: 403
            );
        }

        // Update password
        $this->userRepository->update($user, [
            'password' => Hash::make($password),
        ]);

        return ServiceResult::success(
            data: null,
            message: __('auth.password_reset_success'),
            code: 200
        );
    }


    public function verifyOtp(string $code)
    {


        $otpRecord = $this->otpRepository->findOtp(
            code: $code,
            type: OtpType::FORGOT_PASSWORD
        );
        if (!$otpRecord || $otpRecord->code !== $code) {
            return ServiceResult::error(
                message: __('auth.invalid_otp'),
                nextEndpoint: 'auth/resend-otp',
                errors: ['code' => __('auth.invalid_otp')],
                code: 403
            );
        }
        $this->otpRepository?->markAsUsed($otpRecord);
        $this->userRepository->passwordResetToken($otpRecord->user);

        return ServiceResult::success(
            data: [
                'token' => $otpRecord->user->password_reset_token
            ],
            message: __('auth.otp_verified'),
            nextEndpoint: route('api.password.change'),
            code: 200
        );
    }
}
