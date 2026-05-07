<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'parent_id',
        'code',
        'sort_order',
        'is_active',
        'notes',
    ];


    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    protected $appends = ['name'];

  

  

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function inventoryProducts()
    {
        return $this->hasMany(InventoryProduct::class);
    }

    public function addAttibute($locale)
    {
        return 'test';
    }

    /*
    |--------------------------------------------------------------------------
    | Auto Tenant Assign
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::creating(function ($model) {
            if (app()->has('currentTenant')) {
                $model->tenant_id = app('currentTenant')->id;
            }
        });
    }
    // Relationship to translations
    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }

      public function translation()
    {
        return $this->hasOne(CategoryTranslation::class)
            ->where('locale', app()->getLocale());
    }
    public function getNameAttribute()
    {
        $locale = app()->getLocale(); // current locale, e.g., 'en', 'ar'

        // Get translation for the current locale
        $translation = $this->translation;

        // Fallback to default (e.g., first translation) if not found
        return $translation?->name ?? $this->translation?->name ?? null;
    }
}
