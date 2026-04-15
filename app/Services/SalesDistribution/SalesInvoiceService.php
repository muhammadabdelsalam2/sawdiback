<?php

namespace App\Services\SalesDistribution;

use App\Models\SalesDistribution\SalesInvoice;
use App\Repositories\Contracts\SalesDistribution\SalesInvoiceRepositoryInterface;
use App\Services\SalesDistribution\Accounting\AccountingGateway;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SalesInvoiceService
{
    public function __construct(
        private readonly SalesInvoiceRepositoryInterface $repo,
        private readonly AccountingGateway $accountingGateway
    ) {}

    public function paginate(string $tenantId, array $filters): LengthAwarePaginator
    {
        return $this->repo->paginateWithRelations($tenantId, $filters);
    }

    public function create(string $tenantId, array $data): SalesInvoice
    {
        [$subtotal, $tax, $total] = $this->calcTotals($data);

        $invoice = $this->repo->create([
            'tenant_id' => $tenantId,
            ...$data,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ]);

        $this->accountingGateway->recordInvoice($invoice->refresh());

        return $invoice;
    }

    public function update(SalesInvoice $invoice, array $data): SalesInvoice
    {
        [$subtotal, $tax, $total] = $this->calcTotals($data);

        $updated = $this->repo->update($invoice, [
            ...$data,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ]);

        $this->accountingGateway->recordInvoice($updated->refresh());

        return $updated;
    }

    public function delete(SalesInvoice $invoice): bool
    {
        $deleted = $this->repo->delete($invoice);
        $this->accountingGateway->deleteInvoice($invoice);

        return $deleted;
    }

    private function calcTotals(array $data): array
    {
        $subtotal = round((float) ($data['subtotal'] ?? 0), 2);
        $tax = round((float) ($data['tax'] ?? 0), 2);
        $total = round($subtotal + $tax, 2);

        return [$subtotal, $tax, $total];
    }
}
