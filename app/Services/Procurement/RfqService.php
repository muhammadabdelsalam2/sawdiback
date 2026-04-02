<?php

namespace App\Services\Procurement;

use App\Models\PurchaseRequisition;
use App\Models\Rfq;
use App\Repositories\Contracts\Procurement\RfqRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class RfqService
{
    public function __construct(
        private readonly RfqRepositoryInterface $repo
    ) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->repo->paginateWithRelations($filters);
    }

    public function create(array $data): Rfq
    {
        return $this->repo->create([
            'code' => $data['code'] ?? $this->generateCode(),
            ...$data,
        ]);
    }

    public function createFromRequisition(PurchaseRequisition $requisition, array $data = []): Rfq
    {
        return $this->repo->create([
            'code' => $data['code'] ?? $this->generateCode(),
            'purchase_requisition_id' => $requisition->id,
            'status' => $data['status'] ?? 'open',
        ]);
    }

    public function update(Rfq $rfq, array $data): Rfq
    {
        return $this->repo->update($rfq, $data);
    }

    public function delete(Rfq $rfq): bool
    {
        return $this->repo->delete($rfq);
    }

    private function generateCode(): string
    {
        return 'RFQ-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }
}
