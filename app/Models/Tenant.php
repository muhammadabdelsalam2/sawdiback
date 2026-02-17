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
}
