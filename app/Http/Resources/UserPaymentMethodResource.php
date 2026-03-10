<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPaymentMethodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $month = str_pad((string) $this->expiry_month, 2, '0', STR_PAD_LEFT);
        $yearTwoDigits = str_pad((string) ($this->expiry_year % 100), 2, '0', STR_PAD_LEFT);

        return [
            'id' => $this->id,
            'brand' => $this->brand,
            'last_four' => $this->last_four,
            'masked_card' => '**** **** **** ' . $this->last_four,
            'expiry_month' => (int) $this->expiry_month,
            'expiry_year' => (int) $this->expiry_year,
            'expiry_display' => $month . '/' . $yearTwoDigits,
            'holder_name' => $this->holder_name,
            'gateway' => $this->gateway,
            'gateway_reference' => $this->gateway_reference,
            'is_default' => (bool) $this->is_default,
            'created_at' => optional($this->created_at)?->toISOString(),
            'updated_at' => optional($this->updated_at)?->toISOString(),
        ];
    }
}
