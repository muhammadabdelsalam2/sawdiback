<?php

namespace App\Repositories\Contracts\SalesDistribution;

use App\Models\SalesDistribution\SalesShipment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SalesShipmentRepositoryInterface
{
    public function paginateWithRelations(string $tenantId, array $filters, int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): SalesShipment;
    public function update(SalesShipment $shipment, array $data): SalesShipment;
    public function delete(SalesShipment $shipment): bool;
    public function pushStatusHistory(SalesShipment $shipment, ?string $fromStatus, string $toStatus, ?string $notes = null): void;
}
