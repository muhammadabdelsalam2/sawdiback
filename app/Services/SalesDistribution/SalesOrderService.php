<?php

namespace App\Services\SalesDistribution;

use App\Models\SalesDistribution\SalesOrder;
use App\Repositories\Contracts\SalesDistribution\SalesOrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SalesOrderService
{
    public function __construct(
        private readonly SalesOrderRepositoryInterface $repo
    ) {}

    public function paginate(string $tenantId, array $filters): LengthAwarePaginator
    {
        return $this->repo->paginateWithRelations($tenantId, $filters);
    }

    public function listForSelection(string $tenantId): Collection
    {
        return $this->repo->listForSelection($tenantId);
    }

    public function create(string $tenantId, array $data): SalesOrder
    {
        return DB::transaction(function () use ($tenantId, $data) {
            [$orderData, $items] = $this->normalizePayload($data);

            $order = $this->repo->create([
                'tenant_id' => $tenantId,
                ...$orderData,
            ]);

            $this->repo->replaceItems($order, $items);

            return $order->load(['customer', 'contract', 'items']);
        });
    }

    public function update(SalesOrder $order, array $data): SalesOrder
    {
        return DB::transaction(function () use ($order, $data) {
            [$orderData, $items] = $this->normalizePayload($data);

            $this->repo->update($order, $orderData);
            $this->repo->replaceItems($order, $items);

            return $order->load(['customer', 'contract', 'items']);
        });
    }

    public function delete(SalesOrder $order): bool
    {
        return $this->repo->delete($order);
    }

    private function normalizePayload(array $data): array
    {
        $items = collect($data['items'] ?? [])
            ->map(function (array $item) {
                $qty = (float) $item['qty'];
                $unitPrice = (float) $item['unit_price'];
                $discount = (float) ($item['discount'] ?? 0);
                $lineTotal = round(($qty * $unitPrice) - $discount, 2);

                return [
                    'product_id' => (int) $item['product_id'],
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'discount' => $discount,
                    'line_total' => max($lineTotal, 0),
                ];
            })
            ->values()
            ->all();

        $total = round(collect($items)->sum('line_total'), 2);

        unset($data['items']);
        $data['total'] = $total;

        return [$data, $items];
    }
}
