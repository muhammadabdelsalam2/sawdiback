<?php

namespace App\Services\SalesDistribution\Accounting;

use App\Models\SalesDistribution\SalesInvoice;
use App\Models\SalesDistribution\SalesPayment;

interface AccountingGateway
{
    public function recordPayment(SalesInvoice $invoice, SalesPayment $payment): void;
}
