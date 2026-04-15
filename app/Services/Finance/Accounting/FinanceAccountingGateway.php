<?php

namespace App\Services\Finance\Accounting;

use App\Models\SalesDistribution\SalesInvoice;
use App\Models\SalesDistribution\SalesPayment;
use App\Services\Finance\AccountLookupService;
use App\Services\Finance\JournalService;
use App\Services\SalesDistribution\Accounting\AccountingGateway;

class FinanceAccountingGateway implements AccountingGateway
{
    public function __construct(
        private readonly JournalService $journal,
        private readonly AccountLookupService $accounts
    ) {}

    public function recordInvoice(SalesInvoice $invoice): void
    {
        if ($invoice->status === 'void') {
            $this->deleteInvoice($invoice);
            return;
        }

        $tenantId = (string) $invoice->tenant_id;
        $amount = round((float) $invoice->total, 2);

        if ($amount <= 0) {
            return;
        }

        $ar = $this->accounts->accountsReceivable($tenantId);
        $revenue = $this->accounts->revenue($tenantId);

        $this->journal->upsertBySource($tenantId, 'sales_invoice', (int) $invoice->id, [
            'entry_date' => $invoice->invoice_date?->format('Y-m-d') ?? now()->toDateString(),
            'description' => 'Sales invoice ' . $invoice->invoice_no,
            'created_by' => auth()->id(),
        ], [
            ['account_id' => $ar->id, 'debit' => $amount, 'credit' => 0, 'memo' => 'Accounts Receivable'],
            ['account_id' => $revenue->id, 'debit' => 0, 'credit' => $amount, 'memo' => 'Revenue'],
        ]);
    }

    public function deleteInvoice(SalesInvoice $invoice): void
    {
        $tenantId = (string) $invoice->tenant_id;
        $this->journal->deleteBySource($tenantId, 'sales_invoice', (int) $invoice->id);
    }

    public function recordPayment(SalesInvoice $invoice, SalesPayment $payment): void
    {
        $tenantId = (string) $invoice->tenant_id;
        $amount = round((float) $payment->amount, 2);

        if ($amount <= 0) {
            return;
        }

        $paymentAccount = $this->accounts->paymentAccountByMethod($tenantId, $payment->method);
        $ar = $this->accounts->accountsReceivable($tenantId);

        $this->journal->upsertBySource($tenantId, 'sales_payment', (int) $payment->id, [
            'entry_date' => $payment->paid_at?->format('Y-m-d') ?? now()->toDateString(),
            'description' => 'Sales payment for invoice ' . $invoice->invoice_no,
            'created_by' => auth()->id(),
        ], [
            ['account_id' => $paymentAccount->id, 'debit' => $amount, 'credit' => 0, 'memo' => 'Payment'],
            ['account_id' => $ar->id, 'debit' => 0, 'credit' => $amount, 'memo' => 'Accounts Receivable'],
        ]);
    }

    public function deletePayment(SalesPayment $payment): void
    {
        $invoice = $payment->invoice()->first();
        $tenantId = $invoice?->tenant_id ? (string) $invoice->tenant_id : null;

        if ($tenantId) {
            $this->journal->deleteBySource($tenantId, 'sales_payment', (int) $payment->id);
        }
    }
}
