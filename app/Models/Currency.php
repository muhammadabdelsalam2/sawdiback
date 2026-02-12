<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'symbol', 'rate', 'is_default'];
    public static function default()
    {
        return self::where('is_default', true)->first();
    }
}
