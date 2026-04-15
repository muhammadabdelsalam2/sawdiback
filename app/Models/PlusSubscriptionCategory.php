<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlusSubscriptionCategory extends Model
{
    use HasFactory;

    protected $table = 'plus_subscription_categories';

    protected $fillable = [
        'plus_subscription_id',
        'category_id',
    ];

    public function plusSubscription(): BelongsTo
    {
        return $this->belongsTo(PlusSubscription::class, 'plus_subscription_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
