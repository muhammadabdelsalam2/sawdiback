<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    //

    protected $fillable = ['code', 'symbol', 'rate', 'is_default'];
    public static function default()
    {
        return self::where('is_default', true)->first();
    }
}
