<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_updates',
        'sms_updates',
        'promotions_deals',
        'new_products',
    ];

    protected function casts(): array
    {
        return [
            'order_updates' => 'boolean',
            'sms_updates' => 'boolean',
            'promotions_deals' => 'boolean',
            'new_products' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
