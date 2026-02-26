<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\VerifyOtpRequest;
use Illuminate\Http\Request;
use App\DTOs\Auth\VerifyOtpDTO;
use App\Support\ApiResponse;
use Illuminate\Validation\ValidationException;
use App\Services\API\Auth\AuthService;
use App\Services\API\Auth\OtpService;
use Illuminate\Http\JsonResponse; 
use App\Repositories\UserRepository;

class VerifyAccountController extends Controller
{
    //
        public function __construct(
        protected AuthService $authService,
        private OtpService $otpService,
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

        // Determine OTP type automatically
        $user = $this->userRepository->findByIdentifierValue($identifier);
        $type = $this->determineOtpType($user);

        // Create DTO for OTP
        $dto = new SendOtpDTO(
            identifier: $identifier,
            type: $type
        );

        // Send or resend OTP
        $this->otpService->resend($dto);

        return response()->json([
            'status' => true,
            'message' => __('auth.otp_sent'),
            'data' => [
                'identifier' => $identifier,
                'type' => $type
            ]
        ]);
    }

    /**
     * Automatically detect OTP type based on user state
     */
    private function determineOtpType(?object $user): string
    {
        if (!$user) {
            // New user: registration OTP
            return OtpType::REGISTER;
        }

        if (!$user->is_active || !$user->email_verified_at) {
            // Not verified yet: verification OTP
            return 'verify_email';
        }

        // Existing verified user: forgot password OTP
        return OtpType::FORGOT_PASSWORD;
    }
}
