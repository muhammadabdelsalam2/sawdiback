<?php

namespace App\Services\SalesDistribution\Accounting;

use App\Models\SalesDistribution\SalesInvoice;
use App\Models\SalesDistribution\SalesPayment;

class NullAccountingGateway implements AccountingGateway
{
    public function recordPayment(SalesInvoice $invoice, SalesPayment $payment): void
    {
        // No-op until accounting integration is available.
    }
}
