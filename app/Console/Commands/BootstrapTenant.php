<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class BootstrapTenant extends Command
{
    protected $signature = 'app:bootstrap-tenant {name=Default Tenant} {slug=default-tenant} {--user_id=1}';
    protected $description = 'Create a tenant and link it to an existing user (Customer).';

    public function handle(): int
    {
        $name = (string) $this->argument('name');
        $slug = (string) $this->argument('slug');
        $userId = (int) $this->option('user_id');

        $user = User::find($userId);
        if (!$user) {
            $this->error("User not found: {$userId}");
            return self::FAILURE;
        }

        $tenant = Tenant::query()->where('slug', $slug)->first();
        if (!$tenant) {
            $tenant = Tenant::create([
                'id' => (string) Str::uuid(),
                'name' => $name,
                'slug' => $slug,
                'status' => 'active',
            ]);
        }

        $user->tenant_id = $tenant->id;
        $user->save();

        // Ensure Customer role exists and assign
        $role = Role::findOrCreate('Customer');
        if (!$user->hasRole('Customer')) {
            $user->assignRole($role);
        }

        $this->info("Tenant created/loaded: {$tenant->id} ({$tenant->slug})");
        $this->info("User linked: {$user->id} -> tenant_id={$tenant->id} and role=Customer");

        return self::SUCCESS;
    }
}
