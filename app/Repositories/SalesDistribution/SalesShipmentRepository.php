<?php

namespace App\Repositories\SalesDistribution;

use App\Models\SalesDistribution\SalesShipment;
use App\Repositories\Contracts\SalesDistribution\SalesShipmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SalesShipmentRepository implements SalesShipmentRepositoryInterface
{
    public function paginateWithRelations(string $tenantId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return SalesShipment::query()
            ->with(['order.customer'])
            ->where('tenant_id', $tenantId)
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['date_from'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($filters['date_to'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): SalesShipment
    {
        return SalesShipment::query()->create($data);
    }

    public function update(SalesShipment $shipment, array $data): SalesShipment
    {
        $shipment->update($data);

        return $shipment->refresh();
    }

    public function delete(SalesShipment $shipment): bool
    {
        return (bool) $shipment->delete();
    }

    public function pushStatusHistory(SalesShipment $shipment, ?string $fromStatus, string $toStatus, ?string $notes = null): void
    {
        $shipment->statusHistory()->create([
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'changed_at' => now(),
            'changed_by' => auth()->id(),
            'notes' => $notes,
        ]);
    }
}
