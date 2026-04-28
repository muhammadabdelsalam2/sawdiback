<?php

namespace App\Services\Procurement;

use App\Models\Invoice;
use App\Repositories\Contracts\Procurement\InvoiceRepositoryInterface;
use App\Services\Finance\AccountLookupService;
use App\Services\Finance\JournalService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseInvoiceService
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $repo,
        private readonly JournalService $journal,
        private readonly AccountLookupService $accounts
    ) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->repo->paginateWithRelations($filters);
    }

    public function create(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            [$payload, $totals] = $this->normalizePayload($data);

            $invoice = $this->repo->create([
                'number' => $payload['number'] ?? $this->generateNumber(),
                'type' => 'purchase',
                ...$payload,
                ...$totals,
            ]);

            $this->syncJournal($invoice->refresh());

            return $invoice;
        });
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        return DB::transaction(function () use ($invoice, $data) {
            [$payload, $totals] = $this->normalizePayload($data);

            $updated = $this->repo->update($invoice, [
                ...$payload,
                ...$totals,
            ]);

            $this->syncJournal($updated->refresh());

            return $updated;
        });
    }

    public function delete(Invoice $invoice): bool
    {
        return DB::transaction(function () use ($invoice) {
            $deleted = $this->repo->delete($invoice);
            $this->journal->deleteBySource($this->tenantId($invoice), 'purchase_invoice', $invoice->id);

            return $deleted;
        });
    }

    private function normalizePayload(array $data): array
    {
        $subtotal = round((float) ($data['subtotal'] ?? 0), 2);
        $tax = round((float) ($data['tax'] ?? 0), 2);
        $discount = round((float) ($data['discount'] ?? 0), 2);
        $total = round($subtotal + $tax - $discount, 2);
        $data['id'] = $data['id'] ?? (string) Str::uuid();

        unset($data['subtotal'], $data['tax'], $data['discount'], $data['total']);

        return [$data, [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total,
        ]];
    }

    private function syncJournal(Invoice $invoice): void
    {
        if ($invoice->status !== 'posted') {
            $this->journal->deleteBySource($this->tenantId($invoice), 'purchase_invoice', $invoice->id);
            return;
        }

        $amount = round((float) $invoice->total, 2);
        if ($amount <= 0) {
            return;
        }

        $tenantId = $this->tenantId($invoice);
        $ap = $this->accounts->accountsPayable($tenantId);

        $debitAccount = $this->resolveDebitAccount($invoice, $tenantId);

        $this->journal->upsertBySource($tenantId, 'purchase_invoice', $invoice->id, [
            'entry_date' => $invoice->invoice_date?->format('Y-m-d') ?? now()->toDateString(),
            'description' => 'Purchase invoice ' . $invoice->number,
            'created_by' => auth()->id(),
        ], [
            ['account_id' => $debitAccount->id, 'debit' => $amount, 'credit' => 0, 'memo' => $debitAccount->name],
            ['account_id' => $ap->id, 'debit' => 0, 'credit' => $amount, 'memo' => 'Accounts Payable'],
        ]);
    }

    private function resolveDebitAccount(Invoice $invoice, string $tenantId)
    {
        if ($invoice->goods_receipt_id || $invoice->purchase_order_id) {
            return $this->accounts->inventory($tenantId);
        }

        return $this->accounts->expenses($tenantId);
    }

    private function tenantId(Invoice $invoice): string
    {
        return (string) ($invoice->tenant_id ?? auth()->user()?->tenant_id ?? '');
    }

    private function generateNumber(): string
    {
        return 'PINV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }
}
