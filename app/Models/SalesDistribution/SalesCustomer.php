<?php

namespace App\Models\SalesDistribution;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesCustomer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales_customers';

    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'phones',
        'address',
        'tax_number',
        'notes',
        'status',
    ];

    public function contracts(): HasMany
    {
        return $this->hasMany(SalesContract::class, 'customer_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(SalesOrder::class, 'customer_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(SalesInvoice::class, 'customer_id');
    }
}
