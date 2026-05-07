<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tenant extends Model
{
    //
    use HasFactory;

    // ⛔ مهم جدًا
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'slug',
        'status',
        'user_id',
    ];

    protected $casts = [
        'id' => 'string',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // Relations
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function plan()
{
    return $this->belongsTo(Plan::class, 'plan_id');
}


    public function livestockAnimals()
    {
        return $this->hasMany(LivestockAnimal::class);
    }

    public function animalSpecies()
    {
        return $this->hasMany(AnimalSpecies::class);
    }

    public function animalBreeds()
    {
        return $this->hasMany(AnimalBreed::class);
    }

    public function inventoryProducts()
    {
        return $this->hasMany(InventoryProduct::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function inventoryBatches()
    {
        return $this->hasMany(InventoryBatch::class);
    }

    public function inventoryDeliveries()
    {
        return $this->hasMany(InventoryDelivery::class);
    }

    public function inventoryProductionRecords()
    {
        return $this->hasMany(InventoryProductionRecord::class);
    }

    public function crops()
    {
        return $this->hasMany(Crop::class);
    }

    public function feedTypes()
    {
        return $this->hasMany(FeedType::class);
    }

    public function salesOrders()
    {
        return $this->hasMany(\App\Models\SalesDistribution\SalesOrder::class);
    }
}
