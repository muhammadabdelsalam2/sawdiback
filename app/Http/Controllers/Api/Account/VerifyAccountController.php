<?php

namespace App\Http\Controllers\Api\Account;

use App\DTOs\Auth\SendOtpDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\ResendOtpRequest;
use App\Http\Requests\Api\Auth\VerifyOtpRequest;
use Illuminate\Http\Request;
use App\DTOs\Auth\VerifyOtpDTO;
use App\Repositories\OtpRepository;
use App\Support\ApiResponse;
use Illuminate\Validation\ValidationException;
use App\Services\API\Auth\AuthService;
use App\Services\API\Auth\OtpService;
use Illuminate\Http\JsonResponse;
use App\Repositories\UserRepository;
use App\Enums\OtpType;
class VerifyAccountController extends Controller
{
    //
    public function __construct(
        protected AuthService $authService,
        private OtpService $otpService,
        private OtpRepository $otpRepository,
        private UserRepository $userRepository
    ) {
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $result = $this->authService->verifyOtp(
            VerifyOtpDTO::fromRequest($request)
        );


        return ApiResponse::success(
            data: $result['data'],
            message: $result['message'],
            code: $result['code']
        );
    }
    public function resendOtp(ResendOtpRequest $request): JsonResponse
    {
        $identifier = $request->identifier;

        // Step:one  Detect identifier type (email or phone)
        $identifierType = $this->detectIdentifierType($identifier);

        // Step:2 Find user by identifier
        $user = $this->userRepository->findByIdentifierValue($identifier);

        // Step:3 Determine OTP type based on full logic
        $type = $this->determineOtpType(
            identifierType: $identifierType,
            user: $user
        );

        // Step:4 Create DTO
        $dto = new SendOtpDTO(
            identifier: $identifier,
            type: $type
        );

        // Step:5 Resend OTP
        $this->otpService->resend($dto, $user);

        // Step:6  Return response (without leaking OTP in production)
        $responseData = [
            'identifier' => $identifier,
            'type' => $type,
        ];

        // Show OTP only in local/testing environment
        if (app()->environment('local')) {
            $existingOtp = $this->otpRepository
                ->findValidOtpByIdentifierAndType($identifier, $type);

            $responseData['otp_code'] = $existingOtp?->code;
        }

        return response()->json([
            'status' => true,
            'message' => __('auth.otp_sent'),
            'data' => $responseData
        ]);
    }


    /**
     * Determine OTP type based on:
     * - Identifier type
     * - User existence
     * - Verification state
     * - Activation state
     */
    private function determineOtpType(
        string $identifierType,
        ?object $user
    ): string {

        /**
         * Case 1: User does not exist
         * Registration flow
         */
        if (!$user) {
            return OtpType::REGISTER;
        }

        /**
         * Case 2: Email identifier
         */
        if ($identifierType === 'email') {

            // Email not verified
            if (is_null($user->email_verified_at)) {
                return OtpType::VERIFY_EMAIL;
            }

            // Account inactive
            if (!$user->is_active) {
                return OtpType::VERIFY_EMAIL;
            }
        }

        /**
         * Case 3: Phone identifier
         */
        if ($identifierType === 'phone') {

            // Phone not verified
            if (is_null($user->phone_verified_at)) {
                return OtpType::VERIFY_PHONE;
            }

            // Account inactive
            if (!$user->is_active) {
                return OtpType::VERIFY_PHONE;
            }
        }

        /**
         * Case 4:
         * User exists and fully verified
         * Forgot password flow
         */
        return OtpType::FORGOT_PASSWORD;
    }
    private function detectIdentifierType(string $identifier): string
    {
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }

        return 'phone';
    }
}
