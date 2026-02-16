<?php
namespace App\Services;

use App\Repositories\TenantRepository;

class TenantService
{
    protected $tenantRepo;

    public function __construct(TenantRepository $tenantRepo)
    {
        $this->tenantRepo = $tenantRepo;
    }

    public function createTenant(array $data)
    {
        // Business logic before creation
        $data['status'] = $data['status'] ?? 'active';
        return $this->tenantRepo->create($data);
    }

    public function changePlan($tenantId, $planId)
    {
        $tenant = $this->tenantRepo->findById($tenantId);
        if (!$tenant)
            throw new \Exception("Tenant not found");
        $tenant->plan_id = $planId;
        $tenant->save();
        return $tenant;
    }
}
