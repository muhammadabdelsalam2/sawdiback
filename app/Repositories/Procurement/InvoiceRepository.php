<?php

namespace App\Repositories\Procurement;

use App\Models\Invoice;
use App\Repositories\Contracts\Procurement\InvoiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function paginateWithRelations(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return Invoice::query()
            ->with(['supplier', 'department'])
            ->where('type', 'purchase')
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['supplier_id'] ?? null, fn ($q, $v) => $q->where('supplier_id', $v))
            ->when($filters['date_from'] ?? null, fn ($q, $v) => $q->whereDate('invoice_date', '>=', $v))
            ->when($filters['date_to'] ?? null, fn ($q, $v) => $q->whereDate('invoice_date', '<=', $v))
            ->orderByDesc('invoice_date')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): Invoice
    {
        return Invoice::query()->create($data);
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        $invoice->update($data);

        return $invoice->refresh();
    }

    public function delete(Invoice $invoice): bool
    {
        return (bool) $invoice->delete();
    }
}
