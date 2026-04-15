<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserNotificationSettingsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'order_updates' => (bool) $this->order_updates,
            'sms_updates' => (bool) $this->sms_updates,
            'promotions_deals' => (bool) $this->promotions_deals,
            'new_products' => (bool) $this->new_products,
            'has_enabled_notifications' => (bool) (
                $this->order_updates ||
                $this->sms_updates ||
                $this->promotions_deals ||
                $this->new_products
            ),
        ];
    }
}
