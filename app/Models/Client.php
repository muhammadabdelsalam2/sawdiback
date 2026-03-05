<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
class Client extends Model
{
    //

    use HasFactory, HasApiTokens, Notifiable, HasRoles;
    protected $guard_name = 'api'; // default guard for this model
    protected $table = 'users'; // Use the existing users table


    protected $fillable = [
        'tenant_id',
        'name',
        'phone',
        'email',
        'password',
        'email_verified_at',
        'phone_verified_at',
        'password_reset_token',
        'password_reset_at',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'password_reset_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

}
