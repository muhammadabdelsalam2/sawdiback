<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'rating',
        'title',
        'review',
        'reasons',
        'images',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'reasons' => 'array',
            'images' => 'array',
            'submitted_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
