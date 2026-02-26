<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\VerifyOtpRequest;
use App\Http\Requests\Api\Auth\Password\ForgotPasswordRequest;

use App\Services\API\Auth\AuthService;

use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\Http\Requests\Api\Password\ResetPasswordRequest;
use App\Http\Requests\Api\Auth\ResendOtpRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Request;
use App\DTOs\Auth\VerifyOtpDTO;
use App\DTOs\Auth\SendOtpDTO;
use App\Services\API\Auth\OtpService;
use App\Repositories\UserRepository;
class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService,
        private OtpService $otpService,
        private UserRepository $userRepository
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Register
    |--------------------------------------------------------------------------
    */
    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = RegisterDTO::fromRequest($request);
        $result = $this->authService->register($dto);

        return response()->json([
            'status' => true,
            'message' => __('auth.register_success'),
            'data' => $result
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | Login (email or phone)
    |--------------------------------------------------------------------------
    */
    public function login(LoginRequest $request): JsonResponse
    {
        $dto = LoginDTO::fromRequest($request);
        $result = $this->authService->login($dto);
        if ($result['success'] == false) {
            return ApiResponse::error(
                message: $result['message'],
                errors: $result['errors'] ?? [],
                code: $result['code']
            );
        }

        return ApiResponse::success(
            data: $result['data'],
            message: $result['message'],
            code: $result['code']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'status' => true,
            'message' => __('auth.logout_success')
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Forgot Password
    |--------------------------------------------------------------------------
    */
    public function forgotPassword(
        ForgotPasswordRequest $request
    ): JsonResponse {

        $status = Password::sendResetLink(
            ['email' => $request->email]
        );

        if ($status !== Password::RESET_LINK_SENT) {

            throw ValidationException::withMessages([
                'email' => __($status)
            ]);

        }

        return response()->json([
            'status' => true,
            'message' => __('auth.reset_link_sent')
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Reset Password
    |--------------------------------------------------------------------------
    */
    public function resetPassword(
        ResetPasswordRequest $request
    ): JsonResponse {

        $status = Password::reset(
            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),
            function ($user, $password) {

                $user->update([
                    'password' => bcrypt($password)
                ]);

            }
        );

        if ($status !== Password::PASSWORD_RESET) {

            throw ValidationException::withMessages([
                'email' => __($status)
            ]);

        }

        return response()->json([
            'status' => true,
            'message' => __('auth.password_reset_success')
        ]);
    }

public function verifyOtp(VerifyOtpRequest $request): JsonResponse
{
    $result = $this->authService->verifyOtp(
        VerifyOtpDTO::fromRequest($request)
    );

    return ApiResponse::success(
        data: $result['data'],
        message: $result['message'],
        message: $result['nextEndpoint'],
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
