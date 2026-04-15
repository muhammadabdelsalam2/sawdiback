<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $avatar = $this?->avatar;

        if ($avatar && !filter_var($avatar, FILTER_VALIDATE_URL)) {
            $avatar = Storage::disk('public')->url($avatar);
        }

        return [
            'id' => $this?->id,
            'tenant_id' => $this?->tenant_id,
            'name' => $this?->name,
            'email' => $this?->email,
            'phone' => $this?->phone,
            'avatar' => $avatar,
            'preferred_language' => $this?->preferred_language,
            'is_completed' => (bool) $this?->is_completed,

            'email_verified_at' => $this?->email_verified_at,
            'phone_verified_at' => $this?->phone_verified_at,

            'facebook_id' => $this?->facebook_id,
            'google_id' => $this?->google_id,

            'created_at' => $this?->created_at,
            'updated_at' => $this?->updated_at,
        ];
    }
}
