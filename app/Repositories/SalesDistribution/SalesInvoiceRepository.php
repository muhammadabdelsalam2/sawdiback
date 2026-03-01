<?php

namespace App\Repositories\SalesDistribution;

use App\Models\SalesDistribution\SalesInvoice;
use App\Repositories\Contracts\SalesDistribution\SalesInvoiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SalesInvoiceRepository implements SalesInvoiceRepositoryInterface
{
    public function paginateWithRelations(string $tenantId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return SalesInvoice::query()
            ->with(['customer', 'order'])
            ->where('tenant_id', $tenantId)
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['date_from'] ?? null, fn ($q, $v) => $q->whereDate('invoice_date', '>=', $v))
            ->when($filters['date_to'] ?? null, fn ($q, $v) => $q->whereDate('invoice_date', '<=', $v))
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): SalesInvoice
    {
        return SalesInvoice::query()->create($data);
    }

    public function update(SalesInvoice $invoice, array $data): SalesInvoice
    {
        $invoice->update($data);

        return $invoice->refresh();
    }

    public function delete(SalesInvoice $invoice): bool
    {
        return (bool) $invoice->delete();
    }

    public function refreshTotalPaid(SalesInvoice $invoice): float
    {
        return (float) $invoice->payments()->sum('amount');
    }
}
