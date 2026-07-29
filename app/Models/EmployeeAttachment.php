<?php

namespace App\Models;

use App\Models\Concerns\HasPublicFileUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAttachment extends Model
{
    use HasFactory;
    use HasPublicFileUrl;

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'type',
        'path',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getUrlAttribute(): string
    {
        return $this->publicFileUrl($this->path) ?? '';
    }
}
