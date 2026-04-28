<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farmer extends Model
{
    use HasFactory;
    // Model properties and methods go here

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'opening_balance',
        'is_active',
        'account_id',
        'user_id',
        // Add other fields as necessary
    ];

    public function products()
    {
        // Define The Relation With Inventory Product Model
        return $this->hasMany(InventoryProduct::class);
    }

    // User Relation If Needed
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // // Scope for Tenant Filtering
    // protected static function booted()
    // {
    //     static::addGlobalScope(new \App\Scopes\TenantScope);
    // }

    // Default Image Accessor or Get Url of Farmer's Image
    public function getImageUrlAttribute()
    {


        // Return the URL of the farmer's image or a default image if not set
        return $this->image ? asset('storage/' . $this->image) : asset('images/default-farmer.png');
    }


    // Generate Unique id UUID for Farmer Automatically
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) \Illuminate\Support\Str::uuid();
        });

    }

}
