<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\Password\ForgotPasswordRequest;

use App\Services\API\Auth\AuthService;

use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\Http\Requests\Api\Password\ResetPasswordRequest;
use App\Http\Requests\Api\Password\ForgotPasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

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

        return ApiResponse::success(
            $result['data'],
            $result['message'],
            $result['code']
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
}