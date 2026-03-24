<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProfileOverviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $avatar = $this->avatar;

        if ($avatar && !filter_var($avatar, FILTER_VALIDATE_URL)) {
            $avatar = Storage::disk('public')->url($avatar);
        }

        $language = $this->preferred_language
            ?? $this->current_locale
            ?? $request->route('locale')
            ?? app()->getLocale();

        $addressBookCount = $this->relationLoaded('addresses')
            ? $this->addresses->count()
            : $this->addresses()->count();

        $paymentMethodsCount = $this->relationLoaded('paymentMethods')
            ? $this->paymentMethods->count()
            : $this->paymentMethods()->count();

        $notificationSetting = $this->relationLoaded('notificationSetting')
            ? $this->notificationSetting
            : $this->notificationSetting()->first();

        $hasNotifications = (bool) (
            $notificationSetting?->order_updates ||
            $notificationSetting?->sms_updates ||
            $notificationSetting?->promotions_deals ||
            $notificationSetting?->new_products
        );

        $response = [
            'user' => [
                'id' => $this->id,
                'tenant_id' => $this->tenant_id,
                'name' => $this->name,
                'avatar' => $avatar,
                'phone' => $this->phone,
                'email' => $this->email,
                'is_completed' => (bool) $this->is_completed,
            ],

            'personal_information' => [
                'phone' => $this->phone,
                'email' => $this->email,
                'linked_accounts' => [
                    'google' => !empty($this->google_id),
                    'facebook' => !empty($this->facebook_id),
                ],
            ],

            'my_shopping' => [
                'wallet_points' => 0,
                'address_book_count' => $addressBookCount,
                'payment_methods_count' => $paymentMethodsCount,
            ],

            'settings' => [
                'notifications' => $hasNotifications,
                'appearance' => $this->appearance_mode ?? 'system',
                'language' => $language,
            ],
        ];

        if (is_array($this->plus_summary ?? null)) {
            $response['plus'] = [
                'is_subscribed' => (bool) ($this->plus_summary['is_subscribed'] ?? false),
                'subscription_status' => $this->plus_summary['subscription_status'] ?? null,
                'ui_state' => $this->plus_summary['ui_state'] ?? 'landing',
            ];
        }

        $response['actions'] = [
            'can_logout' => true,
            'can_delete_account' => true,
        ];

        return $response;
    }
}
