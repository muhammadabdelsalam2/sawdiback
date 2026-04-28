<?php

namespace App\Services\Procurement;

use App\Models\Quotation;
use App\Models\Rfq;
use App\Repositories\Contracts\Procurement\QuotationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Str;

class QuotationService
{
    public function __construct(
        private readonly QuotationRepositoryInterface $repo
    ) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->repo->paginateWithRelations($filters);
    }

    public function create(array $data): Quotation
    {
        return DB::transaction(function () use ($data) {
            [$header, $items, $total] = $this->normalizePayload($data);
                $header['id'] = $header['id'] ?? (string) Str::uuid();
            $quotation = $this->repo->create([
                ...$header,
                'total' => $total,
            ]);

            $this->repo->replaceItems($quotation, $items);

            return $quotation->load(['rfq', 'supplier', 'items']);
        });
    }

    public function createFromRfq(Rfq $rfq, array $data): Quotation
    {
        return $this->create([
            'rfq_id' => $rfq->id,
            ...$data,
        ]);
    }

    public function update(Quotation $quotation, array $data): Quotation
    {
        return DB::transaction(function () use ($quotation, $data) {
            [$header, $items, $total] = $this->normalizePayload($data);

            $this->repo->update($quotation, [
                ...$header,
                'total' => $total,
            ]);

            $this->repo->replaceItems($quotation, $items);

            return $quotation->load(['rfq', 'supplier', 'items']);
        });
    }

    public function delete(Quotation $quotation): bool
    {
        return $this->repo->delete($quotation);
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
                    'total' => $total,
                ];
            })
            ->values()
            ->all();

        $total = round(collect($items)->sum('total'), 2);

        unset($data['items']);

        return [$data, $items, $total];
    }
}
