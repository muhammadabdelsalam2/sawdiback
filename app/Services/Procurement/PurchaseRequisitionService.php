<?php

namespace App\Services\Procurement;

use App\Models\PurchaseRequisition;
use App\Repositories\Contracts\Procurement\PurchaseRequisitionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseRequisitionService
{
    public function __construct(
        private readonly PurchaseRequisitionRepositoryInterface $repo
    ) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->repo->paginateWithRelations($filters);
    }

    public function create(array $data): PurchaseRequisition
    {
        return DB::transaction(function () use ($data) {
            [$header, $items] = $this->normalizePayload($data);

            $requisition = $this->repo->create([
                'code' => $header['code'] ?? $this->generateCode(),
                ...$header,
            ]);

            $this->repo->replaceItems($requisition, $items);

            return $requisition->load(['department', 'requester', 'items']);
        });
    }

    public function update(PurchaseRequisition $requisition, array $data): PurchaseRequisition
    {
        return DB::transaction(function () use ($requisition, $data) {
            [$header, $items] = $this->normalizePayload($data);

            $this->repo->update($requisition, $header);
            $this->repo->replaceItems($requisition, $items);

            return $requisition->load(['department', 'requester', 'items']);
        });
    }

    public function delete(PurchaseRequisition $requisition): bool
    {
        return $this->repo->delete($requisition);
    }

    private function normalizePayload(array $data): array
    {
        $items = collect($data['items'] ?? [])
            ->map(function (array $item) {
                return [
                    'product_id' => (int) $item['product_id'],
                    'quantity' => (float) $item['quantity'],
                    'estimated_price' => (float) ($item['estimated_price'] ?? 0),
                ];
            })
            ->values()
            ->all();

        unset($data['items']);

        return [$data, $items];
    }

    private function generateCode(): string
    {
        return 'PR-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }
}
