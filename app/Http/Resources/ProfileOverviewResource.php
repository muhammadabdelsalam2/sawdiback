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

        return [
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
                'address_book_count' => 0,
                'payment_methods_count' => 0,
            ],

            'settings' => [
                'notifications' => false,
                'appearance' => 'system',
                'language' => $language,
            ],

            'actions' => [
                'can_logout' => true,
                'can_delete_account' => true,
            ],
        ];
    }
}
