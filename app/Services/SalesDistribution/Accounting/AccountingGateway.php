<?php

namespace App\Services\SalesDistribution\Accounting;

use App\Models\SalesDistribution\SalesInvoice;
use App\Models\SalesDistribution\SalesPayment;

interface AccountingGateway
{
    public function recordInvoice(SalesInvoice $invoice): void;
    public function deleteInvoice(SalesInvoice $invoice): void;
    public function recordPayment(SalesInvoice $invoice, SalesPayment $payment): void;
    public function deletePayment(SalesPayment $payment): void;
}
