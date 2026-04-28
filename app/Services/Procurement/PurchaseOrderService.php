<?php

namespace App\Services\Procurement;

use App\Models\PurchaseOrder;
use App\Models\Quotation;
use App\Repositories\Contracts\Procurement\PurchaseOrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseOrderService
{
    public function __construct(
        private readonly PurchaseOrderRepositoryInterface $repo
    ) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->repo->paginateWithRelations($filters);
    }

    public function create(array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($data) {
            [$header, $items, $totals] = $this->normalizePayload($data);

            $order = $this->repo->create([
                'po_number' => $header['po_number'] ?? $this->generateNumber(),
                ...$header,
                ...$totals,
            ]);

            $this->repo->replaceItems($order, $items);

            return $order->load(['supplier', 'items']);
        });
    }

    public function createFromQuotation(Quotation $quotation, array $data = []): PurchaseOrder
    {
        $items = $quotation->items()->get()->map(function ($item) {
            return [
                'id' => (string) Str::uuid(),
                'product_id' => $item->product_id,
                'quantity' => (float) $item->quantity,
                'unit_price' => (float) $item->unit_price,
            ];
        })->toArray();

        $order = $this->create([
            'id' => $data['id'] ?? (string) Str::uuid(),
            'supplier_id' => $quotation->supplier_id,
            'rfq_id' => $quotation->rfq_id,
            'quotation_id' => $quotation->id,
            'status' => $data['status'] ?? 'draft',
            'items' => $items,
        ]);

        $quotation->update(['status' => $data['quotation_status'] ?? 'selected']);
        $quotation->rfq?->update(['status' => $data['rfq_status'] ?? 'awarded']);

        return $order;
    }

    public function update(PurchaseOrder $order, array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($order, $data) {
            [$header, $items, $totals] = $this->normalizePayload($data);

            $this->repo->update($order, [
                ...$header,
                ...$totals,
            ]);

            $this->repo->replaceItems($order, $items);

            return $order->load(['supplier', 'items']);
        });
    }

    public function delete(PurchaseOrder $order): bool
    {
        return $this->repo->delete($order);
    }

    private function normalizePayload(array $data): array
    {
        $items = collect($data['items'] ?? [])
            ->map(function (array $item) {
                $qty = (float) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];
                $total = round($qty * $unitPrice, 2);

                return [
                    'id' => $item['id'] ?? (string) Str::uuid(),
                    'product_id' => (int) $item['product_id'],
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'received_quantity' => (float) ($item['received_quantity'] ?? 0),
                    'total' => $total,
                ];
            })
            ->values()
            ->all();

        $subtotal = round(collect($items)->sum('total'), 2);
        $vat = round((float) ($data['vat'] ?? 0), 2);
        $netTotal = round($subtotal + $vat, 2);

        unset($data['items']);

        return [$data, $items, ['total' => $subtotal, 'vat' => $vat, 'net_total' => $netTotal]];
    }

    private function generateNumber(): string
    {
        return 'PO-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }
}
