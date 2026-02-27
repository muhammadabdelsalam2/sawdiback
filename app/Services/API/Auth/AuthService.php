<?php

namespace App\Services\API\Auth;

use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\DTOs\Auth\SendOtpDTO;
use App\DTOs\Auth\SocialLoginDTO;
use App\DTOs\Auth\VerifyOtpDTO;
use App\Enums\OtpType;
use App\Models\User;
use App\Repositories\Contracts\OtpRepositoryInterface;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Services\API\Auth\Contracts\OtpSenderInterface;
use App\Support\ServiceResult;
use GrahamCampbell\ResultType\Success;

class AuthService
{

    protected UserRepository $userRepository;
    protected OtpRepositoryInterface $otpRepository;
    protected OtpSenderInterface $otpSender;
    protected OtpService $otpService;

    public function __construct(
        UserRepository $userRepository,
        OtpRepositoryInterface $otpRepository,
        OtpSenderInterface $otpSender,
        OtpService $otpService
    ) {
        $this->userRepository = $userRepository;
        $this->otpRepository = $otpRepository;
        $this->otpSender = $otpSender;
        $this->otpService = $otpService;
    }

    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    public function login(LoginDTO $dto): array
    {
        // Find user by identifier (email or phone)
        $user = $this->userRepository->findByIdentifier($dto);
        if (!$user) {
            return ServiceResult::error(
                message: __('auth.user_not_found'),
                nextEndpoint: null,
                errors: ['identifier' => __('auth.user_not_found')],
                code: 404
            );
        }

        // Check if account is active
        // if (!$user->is_active) {
        //     return ServiceResult::error(
        //         message: __('auth.account_not_active'),
        //         nextEndpoint: 'auth/resend-otp',
        //         errors: ['account' => __('auth.account_not_active')],
        //         code: 403
        //     );
        // }

        //  Check password
        if (!Hash::check($dto->password, $user->password)) {
            return ServiceResult::error(
                message: __('auth.invalid_password'),
                nextEndpoint: null,
                errors: ['password' => __('auth.invalid_password')],
                code: 422
            );
        }

        // 🔎 Determine login type (email or phone)
        $identifier = $dto->identifier;

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {

            if (!$user->email_verified_at) {
                return ServiceResult::error(
                    message: __('auth.email_not_verified'),
                    nextEndpoint: 'auth/resend-otp',
                    errors: ['email' => __('auth.email_not_verified')],
                    code: 403
                );
            }

        } else {

            if (!$user->phone_verified_at) {
                return ServiceResult::error(
                    message: __('auth.phone_not_verified'),
                    nextEndpoint: 'auth/resend-otp',
                    errors: ['phone' => __('auth.phone_not_verified')],
                    code: 403
                );
            }
        }

        // ✅ Everything is valid → Create token
        $tokenResponse = $this->createTokenResponse($user);

        return ServiceResult::success(
            data: $tokenResponse ?? null,
            message: __('auth.login_success'),
            code: 200
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Register Client
    |--------------------------------------------------------------------------
    */
    public function register(RegisterDTO $dto): array
    {
        // Check if email or phone already exists
        if ($this->userRepository->existsByIdentifier($dto->email ?? $dto->phone)) {
            throw ValidationException::withMessages([
                'identifier' => __('auth.user_already_exists')
            ]);
        }

        // Create user but do NOT activate yet (optional)
        $user = $this->userRepository->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'password' => Hash::make($dto->password),
            // optionally add 'is_active' => false
        ]);

        // Assign role
        $user->assignRole('Client');
        resolve(\App\Repositories\Contracts\TenantRepositoryInterface::class)
            ->createTenantForUser($user);
        // Prepare DTO for OTP
        $otpDto = new SendOtpDTO(
            $dto->email ?? $dto->phone, // identifier
            OtpType::REGISTER           // type
        );

        // Send OTP via OtpService
        $otp = $this->otpService->send($otpDto,$user);


        return ServiceResult::success(
            data: $otp,
            message: __('auth.otp_sent'),
            nextEndpoint: route('api.account.verifyOtp'),
            code: 200
        );

    }

    /*
    |--------------------------------------------------------------------------
    | Create Super Admin
    |--------------------------------------------------------------------------
    */
    public function createSuperAdmin(RegisterDTO $dto): User
    {
        return DB::transaction(function () use ($dto) {

            $user = $this->userRepository->create([
                'name' => $dto->name,
                'email' => $dto->email,
                'phone' => $dto->phone,
                'password' => Hash::make($dto->password),
            ]);

            $user->assignRole('SuperAdmin');

            return $user;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Social Login
    |--------------------------------------------------------------------------
    */
    public function socialLogin(SocialLoginDTO $dto): array
    {
        return DB::transaction(function () use ($dto) {

            $user = $this->userRepository
                ->findByProvider(
                    $dto->provider,
                    $dto->providerId
                );

            if (!$user) {

                // check email exists
                if ($dto->email) {
                    $user = $this->userRepository
                        ->findByEmail($dto->email);
                }

                if (!$user) {

                    $user = $this->userRepository->create([
                        'name' => $dto->name,
                        'email' => $dto->email,
                        'provider' => $dto->provider,
                        'provider_id' => $dto->providerId,
                        'avatar' => $dto->avatar,
                    ]);

                    $user->assignRole('Client');

                } else {

                    $user->update([
                        'provider' => $dto->provider,
                        'provider_id' => $dto->providerId,
                        'avatar' => $dto->avatar,
                    ]);

                }
            }

            return $this->createTokenResponse($user);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Logout current device
    |--------------------------------------------------------------------------
    */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | Logout all devices
    |--------------------------------------------------------------------------
    */
    public function logoutAll(User $user): void
    {
        $user->tokens()->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | Refresh Token
    |--------------------------------------------------------------------------
    */
    public function refreshToken(User $user): array
    {
        $user->currentAccessToken()->delete();

        return $this->createTokenResponse($user);
    }

    /*
    |--------------------------------------------------------------------------
    | Create Token Response
    |--------------------------------------------------------------------------
    */
    protected function createTokenResponse(User $user): array
    {
        $token = $user->createToken('auth')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer'
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Role
    |--------------------------------------------------------------------------
    */
    public function ensureRole(User $user, string $role): void
    {
        if (!$user->hasRole($role)) {

            throw ValidationException::withMessages([
                'authorization' => __('auth.unauthorized')
            ]);
        }
    }

    public function verifyOtp(VerifyOtpDTO $dto): array
    {
        $otp = $this->otpService->verify($dto->identifier, $dto->code);

        $user = $this->userRepository->findByIdentifierValue($dto->identifier);

        if (!$user) {
            return ServiceResult::error(
                message: __('auth.user_not_found'),
                nextEndpoint: null,
                errors: ['identifier' => __('auth.user_not_found')],
                code: 404
            );
        }
        // Use type directly from OTP
        switch ($otp->type) {
            case 'register':
                $this->userRepository->update($user, [
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]);
                break;
            case 'verify_email':
                $this->userRepository->update($user, [
                    'email_verified_at' => now(),
                ]);
                break;

            case 'forgot_password':
                // Allow password reset
                break;

            case 'login':
                // Maybe generate token
                break;
        }

        return ServiceResult::success(
            data: $user,
            message: __('auth.account_verified'),
            nextEndpoint: route('api.account.verifyOtp'),
            code: 200
        );
    }





}
