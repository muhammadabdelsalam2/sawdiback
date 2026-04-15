<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlusSubscriptionSkip extends Model
{
    use HasFactory;

    public const ACTION_SKIP_ONCE = 'skip_once';
    public const ACTION_PAUSE = 'pause';

    protected $fillable = [
        'plus_subscription_id',
        'action',
        'scheduled_for',
        'resume_at',
        'reason',
        'metadata',
    ];

    protected $casts = [
        'scheduled_for' => 'date',
        'resume_at' => 'date',
        'metadata' => 'array',
    ];

    public function plusSubscription(): BelongsTo
    {
        return $this->belongsTo(PlusSubscription::class, 'plus_subscription_id');
    }
}
