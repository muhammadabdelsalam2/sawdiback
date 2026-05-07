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
        'image',
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
    protected $appends = ['name', 'image_url'];





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
    $locale = substr(app()->getLocale(), 0, 2); // 'en-SA' → 'en'
    return $this->hasOne(CategoryTranslation::class)
        ->where('locale', $locale);
}
public function getNameAttribute(): ?string
{
    $locale = substr(app()->getLocale(), 0, 2); // 'en-SA' → 'en', 'ar-SA' → 'ar'

    if ($this->relationLoaded('translations')) {
        $translation = $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', 'en')
            ?? $this->translations->first();

        if ($translation) return $translation->name;
    }

    // Fallback: load from DB if not loaded
    $translation = $this->translations()
        ->where('locale', $locale)
        ->orWhere('locale', 'en')
        ->first();

    if ($translation) return $translation->name;

    return $this->notes ?? $this->code;
}

public function getImageUrlAttribute(): string
{
    if ($this->image) {
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }

    $name = urlencode($this->name ?? $this->code ?? 'Category');

    return "https://ui-avatars.com/api/?name={$name}&background=E5E7EB&color=374151&size=400";
}
}
