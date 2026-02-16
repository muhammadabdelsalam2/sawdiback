<?php
namespace App\Observers;

use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

class TenantObserver
{
    public function created(Tenant $tenant)
    {
        // Automatically create default subscription or log
        Log::info("New tenant created: {$tenant->name}");
    }

    public function updated(Tenant $tenant)
    {
        Log::info("Tenant updated: {$tenant->id}");
    }
}
