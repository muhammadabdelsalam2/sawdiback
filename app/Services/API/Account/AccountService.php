<?php

namespace App\Services\API\Account;

use App\DTOs\Account\UpdateAccountDTO;
use App\DTOs\Auth\SendOtpDTO;
use App\Http\Resources\ProfileOverviewResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Repositories\OtpRepository;
use App\Repositories\UserRepository;
use App\Services\API\Auth\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class AccountService
{
    public function __construct(
        protected ClientRepositoryInterface $clientRepository,
        protected UserRepository $userRepository,
        protected OtpService $otpService,
        protected OtpRepository $otpRepository
    ) {
    }

    /**
     * Existing flow - kept intact
     */
    public function updateAccount(UpdateAccountDTO $dto): array
    {
        $user = $this->clientRepository->findById(Auth::id());

        if (!$user) {
            return [
                'success' => false,
                'message' => __('auth.user_not_found'),
                'data' => null,
                'code' => 404
            ];
        }

        $data = [
            'name' => $dto->name,
            'email' => $dto->email,
            'is_completed' => true,
        ];

        if ($user->email !== $dto->email) {
            $data['email_verified_at'] = null;
        }

        $this->clientRepository->update($user, $data);
        $user = $this->clientRepository->findById(Auth::id());

        return [
            'success' => true,
            'message' => __('account.updated_successfully'),
            'data' => new UserResource($user),
            'code' => 200
        ];
    }

    public function getProfileOverview(?string $locale = null): array
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return [
                'success' => false,
                'message' => __('auth.user_not_found'),
                'data' => null,
                'code' => 404,
            ];
        }

        $user->refresh();

        return [
            'success' => true,
            'message' => 'Profile loaded successfully.',
            'data' => new ProfileOverviewResource($user->setAttribute('current_locale', $locale)),
            'code' => 200,
        ];
    }

    /**
     * Supports:
     * - name
     * - avatar
     * - preferred_language
     * - appearance_mode
     */
    public function updateProfile(Request $request): array
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return [
                'success' => false,
                'message' => __('auth.user_not_found'),
                'data' => null,
                'code' => 404,
            ];
        }

        $data = [];

        if ($request->filled('name')) {
            $data['name'] = $request->string('name')->toString();
        }

        if ($request->filled('preferred_language')) {
            $data['preferred_language'] = $request->string('preferred_language')->toString();
        }

        if ($request->filled('appearance_mode')) {
            $data['appearance_mode'] = $request->string('appearance_mode')->toString();
        }

        if ($request->hasFile('avatar')) {
            $oldAvatar = $user->avatar;

            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');

            if (
                !empty($oldAvatar) &&
                !filter_var($oldAvatar, FILTER_VALIDATE_URL) &&
                Storage::disk('public')->exists($oldAvatar)
            ) {
                Storage::disk('public')->delete($oldAvatar);
            }
        }

        if (empty($data)) {
            return [
                'success' => false,
                'message' => 'No profile changes were provided.',
                'data' => null,
                'code' => 422,
            ];
        }

        $data['is_completed'] = true;

        $this->clientRepository->update($user, $data);
        $user->refresh();

        return [
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => new ProfileOverviewResource($user),
            'code' => 200,
        ];
    }

    /**
     * Shared request update for phone/email
     * type = phone|email
     * value = new phone or new email
     */
    public function requestContactUpdate(Request $request): array
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return [
                'success' => false,
                'message' => __('auth.user_not_found'),
                'data' => null,
                'code' => 404,
            ];
        }

        $type = $request->string('type')->toString();
        $value = trim($request->string('value')->toString());

        if ($type === 'phone') {
            if ($value === (string) $user->phone) {
                return [
                    'success' => false,
                    'message' => 'New phone number must be different from current phone number.',
                    'data' => null,
                    'code' => 422,
                ];
            }

            if ($this->userRepository->existsByPhone($value)) {
                return [
                    'success' => false,
                    'message' => 'This phone number is already in use.',
                    'data' => null,
                    'code' => 422,
                ];
            }

            $otpType = 'update_phone';
        } else {
            if ($value === (string) $user->email) {
                return [
                    'success' => false,
                    'message' => 'New email address must be different from current email address.',
                    'data' => null,
                    'code' => 422,
                ];
            }

            if ($this->userRepository->existsByEmail($value)) {
                return [
                    'success' => false,
                    'message' => 'This email address is already in use.',
                    'data' => null,
                    'code' => 422,
                ];
            }

            $otpType = 'update_email';
        }

        $dto = new SendOtpDTO(
            identifier: $value,
            type: $otpType
        );

        $otp = $this->otpService->send($dto, $user);

        $responseData = [
            'type' => $type,
            'identifier' => $value,
        ];

        if (app()->environment('local')) {
            $existingOtp = $this->otpRepository->findValidOtpByIdentifierAndType($value, $otpType);
            $responseData['otp_code'] = $existingOtp?->code ?? $otp->code ?? null;
        }

        return [
            'success' => true,
            'message' => 'Verification code sent successfully.',
            'data' => $responseData,
            'code' => 200,
        ];
    }

    /**
     * Shared verify update for phone/email
     * type = phone|email
     * identifier = new phone or new email
     * code = OTP
     */
    public function verifyContactUpdate(Request $request): array
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return [
                'success' => false,
                'message' => __('auth.user_not_found'),
                'data' => null,
                'code' => 404,
            ];
        }

        $type = $request->string('type')->toString();
        $identifier = trim($request->string('identifier')->toString());
        $code = trim($request->string('code')->toString());

        $expectedOtpType = $type === 'phone' ? 'update_phone' : 'update_email';

        try {
            $otp = $this->otpService->verify($identifier, $code);
        } catch (ValidationException $e) {
            return [
                'success' => false,
                'message' => 'OTP verification failed.',
                'data' => [
                    'errors' => $e->errors(),
                ],
                'code' => 422,
            ];
        }

        if ($otp->type !== $expectedOtpType) {
            return [
                'success' => false,
                'message' => 'Invalid verification type.',
                'data' => null,
                'code' => 422,
            ];
        }

        if ((int) $otp->user_id !== (int) $user->id) {
            return [
                'success' => false,
                'message' => 'This verification code does not belong to the authenticated user.',
                'data' => null,
                'code' => 403,
            ];
        }

        if ($type === 'phone') {
            if ($this->userRepository->existsByPhone($identifier)) {
                $existing = $this->userRepository->findByPhone($identifier);

                if (!$existing || (int) $existing->id !== (int) $user->id) {
                    return [
                        'success' => false,
                        'message' => 'This phone number is already in use.',
                        'data' => null,
                        'code' => 422,
                    ];
                }
            }

            $this->clientRepository->update($user, [
                'phone' => $identifier,
                'phone_verified_at' => now(),
                'is_completed' => true,
            ]);
        } else {
            if ($this->userRepository->existsByEmail($identifier)) {
                $existing = $this->userRepository->findByEmail($identifier);

                if (!$existing || (int) $existing->id !== (int) $user->id) {
                    return [
                        'success' => false,
                        'message' => 'This email address is already in use.',
                        'data' => null,
                        'code' => 422,
                    ];
                }
            }

            $this->clientRepository->update($user, [
                'email' => $identifier,
                'email_verified_at' => now(),
                'is_completed' => true,
            ]);
        }

        $user->refresh();

        return [
            'success' => true,
            'message' => ucfirst($type) . ' updated successfully.',
            'data' => new ProfileOverviewResource($user),
            'code' => 200,
        ];
    }

    public function logout(?User $user): array
    {
        if (!$user) {
            return [
                'success' => false,
                'message' => __('auth.user_not_found'),
                'data' => null,
                'code' => 404,
            ];
        }

        if ($user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return [
            'success' => true,
            'message' => 'Logged out successfully.',
            'data' => null,
            'code' => 200,
        ];
    }

    public function deleteAccount(?User $user): array
    {
        if (!$user) {
            return [
                'success' => false,
                'message' => __('auth.user_not_found'),
                'data' => null,
                'code' => 404,
            ];
        }

        try {
            if (
                !empty($user->avatar) &&
                !filter_var($user->avatar, FILTER_VALIDATE_URL) &&
                Storage::disk('public')->exists($user->avatar)
            ) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->tokens()->delete();
            $user->delete();

            return [
                'success' => true,
                'message' => 'Account deleted successfully.',
                'data' => null,
                'code' => 200,
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Unable to delete account right now.',
                'data' => [
                    'error' => $e->getMessage(),
                ],
                'code' => 422,
            ];
        }
    }
}
