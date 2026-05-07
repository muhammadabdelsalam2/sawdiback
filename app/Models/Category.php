<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    //
    use SoftDeletes, Concerns\HasTranslations;

    protected $fillable = [
        'tenant_id',
        'parent_id',
        'code',
        'sort_order',
        'is_active',
        'notes',
        'name_translations',
    ];


    protected $casts = [
        'is_active' => 'boolean',
        'name_translations' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
<<<<<<< Updated upstream
    protected $appends = ['name'];

  

  
=======
    protected $appends = ['name', 'image_url'];
>>>>>>> Stashed changes

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

    // Relationship to translations (Keeping for compatibility)
    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }

<<<<<<< Updated upstream
      public function translation()
    {
        return $this->hasOne(CategoryTranslation::class)
            ->where('locale', app()->getLocale());
=======
    public function translation()
    {
        $locale = substr(app()->getLocale(), 0, 2); // 'en-SA' → 'en'
        return $this->hasOne(CategoryTranslation::class)
            ->where('locale', $locale);
>>>>>>> Stashed changes
    }
    public function getNameAttribute()
    {
        $locale = app()->getLocale(); // current locale, e.g., 'en', 'ar'

<<<<<<< Updated upstream
        // Get translation for the current locale
        $translation = $this->translation;

        // Fallback to default (e.g., first translation) if not found
        return $translation?->name ?? $this->translation?->name ?? null;
=======
    public function getNameAttribute(): ?string
    {
        return $this->getLocalized('name_translations', 'notes') ?? $this->code;
    }

public function getImageUrlAttribute(): string
{
    if ($this->image) {
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
>>>>>>> Stashed changes
    }
}
