<?php

namespace App\Services\SalesDistribution;

use App\Models\SalesDistribution\SalesInvoice;
use App\Models\SalesDistribution\SalesPayment;
use App\Repositories\Contracts\SalesDistribution\SalesInvoiceRepositoryInterface;
use App\Repositories\Contracts\SalesDistribution\SalesPaymentRepositoryInterface;
use App\Services\SalesDistribution\Accounting\AccountingGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalesPaymentService
{
    public function __construct(
        private readonly SalesPaymentRepositoryInterface $payments,
        private readonly SalesInvoiceRepositoryInterface $invoices,
        private readonly AccountingGateway $accountingGateway
    ) {}

    public function create(SalesInvoice $invoice, array $data): SalesPayment
    {
        return DB::transaction(function () use ($invoice, $data) {
            $this->ensureInvoicePayable($invoice);

            $payment = $this->payments->create($invoice, $data);
            $this->syncInvoiceStatus($invoice);
            $this->accountingGateway->recordPayment($invoice->refresh(), $payment);

            return $payment;
        });
    }

    public function update(SalesPayment $payment, array $data): SalesPayment
    {
        return DB::transaction(function () use ($payment, $data) {
            $invoice = $payment->invoice()->firstOrFail();
            $this->ensureInvoicePayable($invoice);

            $updated = $this->payments->update($payment, $data);
            $this->syncInvoiceStatus($invoice);
            $this->accountingGateway->recordPayment($invoice->refresh(), $updated);

            return $updated;
        });
    }

    public function delete(SalesPayment $payment): bool
    {
        return DB::transaction(function () use ($payment) {
            $invoice = $payment->invoice()->firstOrFail();
            $deleted = $this->payments->delete($payment);
            $this->syncInvoiceStatus($invoice);

            return $deleted;
        });
    }

    private function ensureInvoicePayable(SalesInvoice $invoice): void
    {
        if ($invoice->status === 'void') {
            throw ValidationException::withMessages([
                'invoice' => __('sales_dist.messages.void_invoice_payment'),
            ]);
        }
    }

    private function syncInvoiceStatus(SalesInvoice $invoice): void
    {
        $paid = $this->invoices->refreshTotalPaid($invoice);
        $total = (float) $invoice->total;

        $status = 'unpaid';
        if ($paid > 0 && $paid < $total) {
            $status = 'partially_paid';
        } elseif ($paid >= $total && $total > 0) {
            $status = 'paid';
        }

        $this->invoices->update($invoice, ['status' => $status]);
    }
}
