<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    //
    protected $fillable = [
        'identifier',   // email or phone
        'code',
        'type',         // login, register, reset_password, etc.
        'expires_at',
        'used_at',      // optional: when OTP was used
        'user_id',      // optional: when OTP was used
        'is_used',      // optional: when OTP was used
    ];

    protected $dates = [
        'expires_at',
        'used_at',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    // Check if OTP is expired
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    // Mark OTP as used
    public function markUsed(): void
    {
        $this->used_at = now();
        $this->save();
    }

    // Generate random OTP
    public static function generateCode(int $length = 6): string
    {
        return (string) random_int(pow(10, $length - 1), pow(10, $length) - 1);
    }

    // Scope to find valid OTPs
    public function scopeValid($query, string $identifier, string $type)
    {
        return $query->where('identifier', $identifier)
            ->where('type', $type)
            ->whereNull('used_at')
            ->where('expires_at', '>', now());
    }

    public function user()
{
    return $this->belongsTo(User::class ,'user_id');
}


}
