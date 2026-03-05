<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this?->id,
            'tenant_id' => $this?->tenant_id,
            'name' => $this?->name,
            'email' => $this?->email,
            'phone' => $this?->phone,
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
