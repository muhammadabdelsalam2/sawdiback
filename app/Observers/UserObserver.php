<?php

namespace App\Observers;

use App\Models\User;
use App\Repositories\Contracts\TenantRepositoryInterface;

class UserObserver
{
        protected $tenantRepository;

    public function __construct(TenantRepositoryInterface $tenantRepository)
    {
        $this->tenantRepository = $tenantRepository;
    }

    /**
     * Handle the User "created" event.
     */
       public function created(User $user)
    {
        // Check if the role is customer
        // We Have Some Issue cause Spati Roles Siigned after observer not befor !!!
        if ($user->hasRole('Customer')) {
            $this->tenantRepository->createTenantForUser($user);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
