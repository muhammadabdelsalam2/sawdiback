<?php

namespace App\Repositories\Contracts\SalesDistribution;

use App\Models\SalesDistribution\SalesInvoice;
use App\Models\SalesDistribution\SalesPayment;

interface SalesPaymentRepositoryInterface
{
    public function create(SalesInvoice $invoice, array $data): SalesPayment;
    public function update(SalesPayment $payment, array $data): SalesPayment;
    public function delete(SalesPayment $payment): bool;
}
