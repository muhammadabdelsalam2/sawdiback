<?php

namespace  App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportItem extends Model
{
    // This Module About Support ItemHelp , FAQ , Contact Info

    protected $fillable = [
        'title',
        'subtitle',
        'icon',
        'type',
        'value',
        'order',
        'is_active'
    ];

    // Scope
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
