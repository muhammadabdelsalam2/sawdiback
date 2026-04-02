<?php

namespace App\Services\Procurement;

use App\Models\GoodsReceipt;
use App\Models\InventoryBatch;
use App\Models\InventoryMovement;
use App\Models\PurchaseOrder;
use App\Repositories\Contracts\Procurement\GoodsReceiptRepositoryInterface;
use App\Services\Warehouse\ReceiveInventoryBatchService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GoodsReceiptService
{
    public function __construct(
        private readonly GoodsReceiptRepositoryInterface $repo,
        private readonly ReceiveInventoryBatchService $receiveBatchService
    ) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->repo->paginateWithRelations($filters);
    }

    public function create(array $data): GoodsReceipt
    {
        return DB::transaction(function () use ($data) {
            [$header, $items] = $this->normalizePayload($data);

            $receipt = $this->repo->create([
                'grn_number' => $header['grn_number'] ?? $this->generateNumber(),
                ...$header,
            ]);

            $this->repo->replaceItems($receipt, $items);

            $this->syncPurchaseOrderReceivedQuantities($receipt->purchaseOrder, $items);
            $this->syncInventoryForReceipt($receipt, $items, false);

            return $receipt->load(['purchaseOrder', 'receiver', 'items']);
        });
    }

    public function createFromPurchaseOrder(PurchaseOrder $order, array $data): GoodsReceipt
    {
        return $this->create([
            'purchase_order_id' => $order->id,
            'received_by' => $data['received_by'],
            'status' => $data['status'] ?? 'completed',
            'items' => $data['items'] ?? [],
        ]);
    }

    public function update(GoodsReceipt $receipt, array $data): GoodsReceipt
    {
        return DB::transaction(function () use ($receipt, $data) {
            [$header, $items] = $this->normalizePayload($data);

            $this->repo->update($receipt, $header);
            $this->repo->replaceItems($receipt, $items);

            $this->syncPurchaseOrderReceivedQuantities($receipt->purchaseOrder, $items, true);
            $this->syncInventoryForReceipt($receipt, $items, true);

            return $receipt->load(['purchaseOrder', 'receiver', 'items']);
        });
    }

    public function delete(GoodsReceipt $receipt): bool
    {
        return DB::transaction(function () use ($receipt) {
            $order = $receipt->purchaseOrder()->first();

            $this->syncInventoryForReceipt($receipt, [], true);

            $deleted = $this->repo->delete($receipt);

            if ($order) {
                $this->syncPurchaseOrderReceivedQuantities($order, [], true);
            }

            return $deleted;
        });
    }

    private function normalizePayload(array $data): array
    {
        $items = collect($data['items'] ?? [])
            ->map(function (array $item) {
                return [
                    'product_id' => (int) $item['product_id'],
                    'quantity' => (float) $item['quantity'],
                ];
            })
            ->values()
            ->all();

        unset($data['items']);

        return [$data, $items];
    }

    private function syncPurchaseOrderReceivedQuantities(?PurchaseOrder $order, array $items, bool $recalculate = false): void
    {
        if (!$order) {
            return;
        }

        if ($recalculate) {
            $order->items()->update(['received_quantity' => 0]);
            $allReceipts = $order->goodsReceipts()->with('items')->get();
            foreach ($allReceipts as $receipt) {
                foreach ($receipt->items as $item) {
                    $order->items()->where('product_id', $item->product_id)
                        ->increment('received_quantity', (float) $item->quantity);
                }
            }
        } else {
            foreach ($items as $item) {
                $order->items()->where('product_id', $item['product_id'])
                    ->increment('received_quantity', (float) $item['quantity']);
            }
        }

        $order->refresh();

        $totalOrdered = (float) $order->items()->sum('quantity');
        $totalReceived = (float) $order->items()->sum('received_quantity');

        $status = $order->status;
        if ($totalReceived <= 0) {
            $status = $status === 'draft' ? 'draft' : 'confirmed';
        } elseif ($totalReceived < $totalOrdered) {
            $status = 'partially_received';
        } else {
            $status = 'received';
        }

        if ($status !== $order->status) {
            $order->update(['status' => $status]);
        }
    }

    private function syncInventoryForReceipt(GoodsReceipt $receipt, array $items, bool $recalculate): void
    {
        if ($recalculate) {
            $batchIds = InventoryBatch::query()
                ->where('source_type', 'goods_receipt')
                ->where('source_id', $receipt->id)
                ->pluck('id')
                ->all();

            if (!empty($batchIds)) {
                InventoryMovement::query()->whereIn('inventory_batch_id', $batchIds)->delete();
                InventoryBatch::query()->whereIn('id', $batchIds)->delete();
            }
        }

        if (!count($items)) {
            return;
        }

        $order = $receipt->purchaseOrder()->with('items')->first();
        $orderItems = $order?->items?->keyBy('product_id') ?? collect();
        $tenantId = session('tenant_id') ?? auth()->user()?->tenant_id;

        foreach ($items as $item) {
            $orderItem = $orderItems->get($item['product_id']);
            $unitCost = $orderItem ? (float) $orderItem->unit_price : null;

            $this->receiveBatchService->execute([
                'tenant_id' => $tenantId,
                'inventory_product_id' => $item['product_id'],
                'batch_number' => $receipt->grn_number . '-' . $item['product_id'],
                'received_at' => $receipt->created_at?->format('Y-m-d') ?? now()->toDateString(),
                'quantity' => $item['quantity'],
                'unit_cost' => $unitCost,
                'source_type' => 'goods_receipt',
                'source_id' => $receipt->id,
                'reference_type' => 'goods_receipt',
                'reference_id' => $receipt->id,
                'notes' => 'GRN ' . $receipt->grn_number,
            ]);
        }
    }

    private function generateNumber(): string
    {
        return 'GRN-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }
}
