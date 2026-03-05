<?php

namespace App\Services\SalesDistribution;

use App\Models\SalesDistribution\SalesContract;
use App\Models\SalesDistribution\SalesCustomer;
use App\Models\SalesDistribution\SalesInvoice;
use App\Models\SalesDistribution\SalesOrder;
use App\Models\SalesDistribution\SalesShipment;

class SalesDistributionDashboardService
{
    public function summary(string $tenantId): array
    {
        return [
            'customers_count' => SalesCustomer::query()->where('tenant_id', $tenantId)->count(),
            'contracts_count' => SalesContract::query()->where('tenant_id', $tenantId)->count(),
            'orders_count' => SalesOrder::query()->where('tenant_id', $tenantId)->count(),
            'shipments_count' => SalesShipment::query()->where('tenant_id', $tenantId)->count(),
            'invoices_count' => SalesInvoice::query()->where('tenant_id', $tenantId)->count(),
            'open_invoices_total' => (float) SalesInvoice::query()
                ->where('tenant_id', $tenantId)
                ->whereIn('status', ['unpaid', 'partially_paid'])
                ->sum('total'),
        ];
    }
}
