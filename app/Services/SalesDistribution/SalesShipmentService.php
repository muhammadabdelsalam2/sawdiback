<?php

namespace App\Services\SalesDistribution;

use App\Models\SalesDistribution\SalesShipment;
use App\Repositories\Contracts\SalesDistribution\SalesShipmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SalesShipmentService
{
    public function __construct(
        private readonly SalesShipmentRepositoryInterface $repo
    ) {}

    public function paginate(string $tenantId, array $filters): LengthAwarePaginator
    {
        return $this->repo->paginateWithRelations($tenantId, $filters);
    }

    public function create(string $tenantId, array $data): SalesShipment
    {
        return DB::transaction(function () use ($tenantId, $data) {
            $shipment = $this->repo->create([
                'tenant_id' => $tenantId,
                ...$data,
            ]);

            $this->repo->pushStatusHistory($shipment, null, (string) $shipment->status, $shipment->notes);

            return $shipment->load(['order.customer', 'statusHistory.changedBy']);
        });
    }

    public function update(SalesShipment $shipment, array $data): SalesShipment
    {
        return DB::transaction(function () use ($shipment, $data) {
            $fromStatus = (string) $shipment->status;
            $updated = $this->repo->update($shipment, $data);
            $toStatus = (string) $updated->status;

            if ($fromStatus !== $toStatus) {
                $this->repo->pushStatusHistory($updated, $fromStatus, $toStatus, $updated->notes);
            }

            return $updated->load(['order.customer', 'statusHistory.changedBy']);
        });
    }

    public function delete(SalesShipment $shipment): bool
    {
        return $this->repo->delete($shipment);
    }
}
