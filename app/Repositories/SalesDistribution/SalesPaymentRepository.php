<?php

namespace App\Repositories\SalesDistribution;

use App\Models\SalesDistribution\SalesInvoice;
use App\Models\SalesDistribution\SalesPayment;
use App\Repositories\Contracts\SalesDistribution\SalesPaymentRepositoryInterface;

class SalesPaymentRepository implements SalesPaymentRepositoryInterface
{
    public function create(SalesInvoice $invoice, array $data): SalesPayment
    {
        return $invoice->payments()->create($data);
    }

    public function update(SalesPayment $payment, array $data): SalesPayment
    {
        $payment->update($data);

        return $payment->refresh();
    }

    public function delete(SalesPayment $payment): bool
    {
        return (bool) $payment->delete();
    }
}
