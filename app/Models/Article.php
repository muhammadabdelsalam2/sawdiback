<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'image',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
    ];

  public function getImageUrlAttribute(): ?string
{
    if (!$this->image) {
        return null;
    }

    if (filter_var($this->image, FILTER_VALIDATE_URL)) {
        return $this->image;
    }

    // الصور في public/assets مش في storage
    return asset(ltrim($this->image, '/'));
}
}
