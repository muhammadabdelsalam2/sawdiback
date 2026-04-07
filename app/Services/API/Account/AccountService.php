<?php

namespace App\Services\API\Account;

use App\DTOs\Account\UpdateAccountDTO;
use App\DTOs\Auth\SendOtpDTO;
use App\Http\Resources\ProfileOverviewResource;
use App\Http\Resources\UserAddressResource;
use App\Http\Resources\UserNotificationSettingsResource;
use App\Http\Resources\UserPaymentMethodResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserNotificationSetting;
use App\Models\UserPaymentMethod;
use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Repositories\OtpRepository;
use App\Repositories\UserRepository;
use App\Services\API\Auth\OtpService;
use App\Services\API\Plus\PlusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class AccountService
{
    public function __construct(
        protected ClientRepositoryInterface $clientRepository,
        protected UserRepository $userRepository,
        protected OtpService $otpService,
        protected OtpRepository $otpRepository,
        protected PlusService $plusService
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
                'code' => 404,
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
            'code' => 200,
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

        $user->loadMissing(['addresses', 'paymentMethods', 'notificationSetting']);
        $user->refresh();

        $user->setAttribute('current_locale', $locale);
        $user->setAttribute('plus_summary', $this->plusService->profileSummary($user));

        return [
            'success' => true,
            'message' => 'Profile loaded successfully.',
            'data' => new ProfileOverviewResource($user),
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
        $oldAvatar = $user->avatar;
        $newAvatarPath = null;

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
            $newAvatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $newAvatarPath;
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

        try {
            $this->clientRepository->update($user, $data);
            $user->refresh();

            if (
                $newAvatarPath &&
                !empty($oldAvatar) &&
                !filter_var($oldAvatar, FILTER_VALIDATE_URL) &&
                Storage::disk('public')->exists($oldAvatar)
            ) {
                Storage::disk('public')->delete($oldAvatar);
            }

            return [
                'success' => true,
                'message' => 'Profile updated successfully.',
                'data' => new ProfileOverviewResource($user),
                'code' => 200,
            ];
        } catch (Throwable $e) {
            if (
                $newAvatarPath &&
                Storage::disk('public')->exists($newAvatarPath)
            ) {
                Storage::disk('public')->delete($newAvatarPath);
            }

            return [
                'success' => false,
                'message' => 'Unable to update profile right now.',
                'data' => null,
                'code' => 422,
            ];
        }
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

    public function getAddressBook(): array
    {
        $user = $this->authenticatedUser();

        if (!$user) {
            return $this->userNotFoundResponse();
        }

        return [
            'success' => true,
            'message' => 'Address book loaded successfully.',
            'data' => $this->addressBookPayload($user),
            'code' => 200,
        ];
    }

    public function storeAddress(array $data): array
    {
        $user = $this->authenticatedUser();

        if (!$user) {
            return $this->userNotFoundResponse();
        }

        try {
            DB::transaction(function () use ($user, $data) {
                $shouldBeDefault = !empty($data['is_default']) || !$user->addresses()->exists();

                $address = $user->addresses()->create([
    'label' => $data['label'] ?? '',
    'title' => $data['title'] ?? '',
    'type' => $data['type'] ?? '',
    'recipient_name' => $data['recipient_name'] ?? '',
    'phone' => $data['phone'] ?? '',
    'address_line_1' => $data['address_line_1'] ?? '',
    'address_line_2' => $data['address_line_2'] ?? '',
    'building' => $data['building'] ?? '',
    'floor' => $data['floor'] ?? '',
    'apartment' => $data['apartment'] ?? '',
    'landmark' => $data['landmark'] ?? '',
    'city' => $data['city'] ?? '',
    'country' => $data['country'] ?? '',
    'postal_code' => $data['postal_code'] ?? '',
    'notes' => $data['notes'] ?? '',
    'latitude' => $data['latitude'] ?? null,
    'longitude' => $data['longitude'] ?? null,
    'is_default' => $shouldBeDefault,
]);

                if ($shouldBeDefault) {
                    $this->normalizeUserAddressDefaults($user, $address->id);
                } else {
                    $this->normalizeUserAddressDefaults($user);
                }
            });

            return [
                'success' => true,
                'message' => 'Address added successfully.',
                'data' => $this->addressBookPayload($user->fresh()),
                'code' => 201,
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Unable to save address right now.',
                'data' => null,
                'code' => 422,
            ];
        }
    }

    public function updateAddress(UserAddress $address, array $data): array
    {
        $user = $this->authenticatedUser();

        if (!$user) {
            return $this->userNotFoundResponse();
        }

        if ((int) $address->user_id !== (int) $user->id) {
            return [
                'success' => false,
                'message' => 'Address not found.',
                'data' => null,
                'code' => 404,
            ];
        }

        try {
            DB::transaction(function () use ($user, $address, $data) {
                $address->fill([
                    'label' => $data['label'] ?? $address->label,
                    'type' => array_key_exists('type', $data) ? $data['type'] : $address->type,
                    'recipient_name' => array_key_exists('recipient_name', $data) ? $data['recipient_name'] : $address->recipient_name,
                    'phone' => array_key_exists('phone', $data) ? $data['phone'] : $address->phone,
                    'address_line_1' => $data['address_line_1'] ?? $address->address_line_1,
                    'address_line_2' => array_key_exists('address_line_2', $data) ? $data['address_line_2'] : $address->address_line_2,
                    'building' => array_key_exists('building', $data) ? $data['building'] : $address->building,
                    'floor' => array_key_exists('floor', $data) ? $data['floor'] : $address->floor,
                    'apartment' => array_key_exists('apartment', $data) ? $data['apartment'] : $address->apartment,
                    'landmark' => array_key_exists('landmark', $data) ? $data['landmark'] : $address->landmark,
                    'city' => array_key_exists('city', $data) ? $data['city'] : $address->city,
                    'country' => array_key_exists('country', $data) ? $data['country'] : $address->country,
                    'postal_code' => array_key_exists('postal_code', $data) ? $data['postal_code'] : $address->postal_code,
                    'notes' => array_key_exists('notes', $data) ? $data['notes'] : $address->notes,
                    'latitude' => array_key_exists('latitude', $data) ? $data['latitude'] : $address->latitude,
                    'longitude' => array_key_exists('longitude', $data) ? $data['longitude'] : $address->longitude,
                    'is_default' => array_key_exists('is_default', $data) ? (bool) $data['is_default'] : $address->is_default,
                ]);

                $address->save();

                if (!empty($data['is_default'])) {
                    $this->normalizeUserAddressDefaults($user, $address->id);
                } else {
                    $this->normalizeUserAddressDefaults($user);
                }
            });

            return [
                'success' => true,
                'message' => 'Address updated successfully.',
                'data' => $this->addressBookPayload($user->fresh()),
                'code' => 200,
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Unable to update address right now.',
                'data' => null,
                'code' => 422,
            ];
        }
    }

    public function deleteAddress(UserAddress $address): array
    {
        $user = $this->authenticatedUser();

        if (!$user) {
            return $this->userNotFoundResponse();
        }

        if ((int) $address->user_id !== (int) $user->id) {
            return [
                'success' => false,
                'message' => 'Address not found.',
                'data' => null,
                'code' => 404,
            ];
        }

        try {
            DB::transaction(function () use ($user, $address) {
                $wasDefault = (bool) $address->is_default;

                $address->delete();

                if ($wasDefault) {
                    $this->normalizeUserAddressDefaults($user);
                }
            });

            return [
                'success' => true,
                'message' => 'Address deleted successfully.',
                'data' => $this->addressBookPayload($user->fresh()),
                'code' => 200,
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Unable to delete address right now.',
                'data' => null,
                'code' => 422,
            ];
        }
    }

    public function getPaymentMethods(): array
    {
        $user = $this->authenticatedUser();

        if (!$user) {
            return $this->userNotFoundResponse();
        }

        return [
            'success' => true,
            'message' => 'Payment methods loaded successfully.',
            'data' => $this->paymentMethodsPayload($user),
            'code' => 200,
        ];
    }

    public function storePaymentMethod(array $data): array
    {
        $user = $this->authenticatedUser();

        if (!$user) {
            return $this->userNotFoundResponse();
        }

        try {
            DB::transaction(function () use ($user, $data) {
                $shouldBeDefault = !empty($data['is_default']) || !$user->paymentMethods()->exists();

                $paymentMethod = $user->paymentMethods()->create([
                    'brand' => $data['brand'],
                    'last_four' => $data['last_four'],
                    'expiry_month' => (int) $data['expiry_month'],
                    'expiry_year' => (int) $data['expiry_year'],
                    'holder_name' => $data['holder_name'] ?? null,
                    'gateway' => $data['gateway'] ?? null,
                    'gateway_reference' => $data['gateway_reference'] ?? null,
                    'is_default' => $shouldBeDefault,
                ]);

                if ($shouldBeDefault) {
                    $this->normalizeUserPaymentMethodDefaults($user, $paymentMethod->id);
                } else {
                    $this->normalizeUserPaymentMethodDefaults($user);
                }
            });

            return [
                'success' => true,
                'message' => 'Payment method added successfully.',
                'data' => $this->paymentMethodsPayload($user->fresh()),
                'code' => 201,
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Unable to save payment method right now.',
                'data' => null,
                'code' => 422,
            ];
        }
    }

    public function deletePaymentMethod(UserPaymentMethod $paymentMethod): array
    {
        $user = $this->authenticatedUser();

        if (!$user) {
            return $this->userNotFoundResponse();
        }

        if ((int) $paymentMethod->user_id !== (int) $user->id) {
            return [
                'success' => false,
                'message' => 'Payment method not found.',
                'data' => null,
                'code' => 404,
            ];
        }

        try {
            DB::transaction(function () use ($user, $paymentMethod) {
                $wasDefault = (bool) $paymentMethod->is_default;

                $paymentMethod->delete();

                if ($wasDefault) {
                    $this->normalizeUserPaymentMethodDefaults($user);
                }
            });

            return [
                'success' => true,
                'message' => 'Payment method deleted successfully.',
                'data' => $this->paymentMethodsPayload($user->fresh()),
                'code' => 200,
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Unable to delete payment method right now.',
                'data' => null,
                'code' => 422,
            ];
        }
    }

    public function getNotificationSettings(): array
    {
        $user = $this->authenticatedUser();

        if (!$user) {
            return $this->userNotFoundResponse();
        }

        $setting = UserNotificationSetting::query()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'order_updates' => false,
                'sms_updates' => false,
                'promotions_deals' => false,
                'new_products' => false,
            ]
        );

        return [
            'success' => true,
            'message' => 'Notification settings loaded successfully.',
            'data' => new UserNotificationSettingsResource($setting),
            'code' => 200,
        ];
    }

    public function updateNotificationSettings(array $data): array
    {
        $user = $this->authenticatedUser();

        if (!$user) {
            return $this->userNotFoundResponse();
        }

        try {
            $setting = UserNotificationSetting::query()->firstOrCreate(
                ['user_id' => $user->id],
                [
                    'order_updates' => false,
                    'sms_updates' => false,
                    'promotions_deals' => false,
                    'new_products' => false,
                ]
            );

            $setting->fill([
                'order_updates' => array_key_exists('order_updates', $data) ? (bool) $data['order_updates'] : $setting->order_updates,
                'sms_updates' => array_key_exists('sms_updates', $data) ? (bool) $data['sms_updates'] : $setting->sms_updates,
                'promotions_deals' => array_key_exists('promotions_deals', $data) ? (bool) $data['promotions_deals'] : $setting->promotions_deals,
                'new_products' => array_key_exists('new_products', $data) ? (bool) $data['new_products'] : $setting->new_products,
            ]);

            $setting->save();

            return [
                'success' => true,
                'message' => 'Notification settings updated successfully.',
                'data' => new UserNotificationSettingsResource($setting->fresh()),
                'code' => 200,
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Unable to update notification settings right now.',
                'data' => null,
                'code' => 422,
            ];
        }
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
                'data' => null,
                'code' => 422,
            ];
        }
    }

    private function authenticatedUser(): ?User
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user;
    }

    private function userNotFoundResponse(): array
    {
        return [
            'success' => false,
            'message' => __('auth.user_not_found'),
            'data' => null,
            'code' => 404,
        ];
    }

    private function addressBookPayload(User $user): array
    {
        $addresses = $user->addresses()
            ->orderByDesc('is_default')
            ->latest('id')
            ->get();

        return [
            'items' => UserAddressResource::collection($addresses),
            'default_address_id' => $addresses->firstWhere('is_default', true)?->id,
            'count' => $addresses->count(),
        ];
    }

    private function paymentMethodsPayload(User $user): array
    {
        $paymentMethods = $user->paymentMethods()
            ->orderByDesc('is_default')
            ->latest('id')
            ->get();

        return [
            'items' => UserPaymentMethodResource::collection($paymentMethods),
            'default_payment_method_id' => $paymentMethods->firstWhere('is_default', true)?->id,
            'count' => $paymentMethods->count(),
        ];
    }

    private function normalizeUserAddressDefaults(User $user, ?int $preferredId = null): void
    {
        $query = $user->addresses();

        if (!$query->exists()) {
            return;
        }

        if ($preferredId) {
            $query->where('id', '!=', $preferredId)->update(['is_default' => false]);
            $query->whereKey($preferredId)->update(['is_default' => true]);
            return;
        }

        $currentDefault = $query->where('is_default', true)->orderBy('id')->first();

        if ($currentDefault) {
            $query->where('id', '!=', $currentDefault->id)->update(['is_default' => false]);
            return;
        }

        $fallback = $query->orderBy('id')->first();

        if ($fallback) {
            $query->whereKey($fallback->id)->update(['is_default' => true]);
        }
    }

    private function normalizeUserPaymentMethodDefaults(User $user, ?int $preferredId = null): void
    {
        $query = $user->paymentMethods();

        if (!$query->exists()) {
            return;
        }

        if ($preferredId) {
            $query->where('id', '!=', $preferredId)->update(['is_default' => false]);
            $query->whereKey($preferredId)->update(['is_default' => true]);
            return;
        }

        $currentDefault = $query->where('is_default', true)->orderBy('id')->first();

        if ($currentDefault) {
            $query->where('id', '!=', $currentDefault->id)->update(['is_default' => false]);
            return;
        }

        $fallback = $query->orderBy('id')->first();

        if ($fallback) {
            $query->whereKey($fallback->id)->update(['is_default' => true]);
        }
    }
}
