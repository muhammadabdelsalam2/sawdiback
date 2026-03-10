<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'brand',
        'last_four',
        'expiry_month',
        'expiry_year',
        'holder_name',
        'gateway',
        'gateway_reference',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'expiry_month' => 'integer',
            'expiry_year' => 'integer',
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
