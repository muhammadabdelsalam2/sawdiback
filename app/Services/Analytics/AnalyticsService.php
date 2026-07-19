<?php

namespace App\Services\Analytics;

use App\Models\SalesDistribution\SalesCustomer;
use App\Services\Finance\ProfitLossService;
use App\Services\Livestock\LivestockPenProfitService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function __construct(
        private readonly LivestockPenProfitService $livestockPenProfitService,
        private readonly ProfitLossService $profitLossService
    )
    {
    }

    public function dashboard(string $tenantId): array
    {
        return [
            'best_customer' => $this->bestCustomer($tenantId),
            'best_product' => $this->bestProduct($tenantId),
            'highest_margin' => $this->highestMargin($tenantId),
            'lowest_margin' => $this->lowestMargin($tenantId),
        ];
    }

    private function bestCustomer(string $tenantId): ?array
    {
        $customer = SalesCustomer::query()
            ->where('tenant_id', $tenantId)
            ->select('id', 'name')
            ->withSum(['orders' => fn($q) => $q->where('tenant_id', $tenantId)], 'total')
            ->orderByDesc('orders_sum_total')
            ->first();

        if (!$customer) {
            return null;
        }

        return [
            'id' => $customer->id,
            'name' => $customer->name,
            'value' => round((float) $customer->orders_sum_total, 2),
        ];
    }

    private function bestProduct(string $tenantId): ?array
    {
        $product = DB::table('sales_order_items as i')
            ->join('sales_orders as o', 'o.id', '=', 'i.sales_order_id')
            ->leftJoin('inventory_products as p', 'p.id', '=', 'i.product_id')
            ->where('o.tenant_id', $tenantId)
            ->selectRaw('COALESCE(p.name, CONCAT("Product #", i.product_id)) as product_name, SUM(i.qty) as total_quantity, SUM(i.line_total) as total_value')
            ->groupBy('i.product_id', 'p.name')
            ->orderByDesc('total_value')
            ->first();

        if (!$product) {
            return null;
        }

        return [
            'name' => $product->product_name,
            'quantity' => round((float) $product->total_quantity, 2),
            'value' => round((float) $product->total_value, 2),
        ];
    }

    private function highestMargin(string $tenantId): ?array
    {
        $items = $this->marginRows($tenantId);

        return $items->sortByDesc(fn($item) => $item['margin'])->first();
    }

    private function lowestMargin(string $tenantId): ?array
    {
        $items = $this->marginRows($tenantId);

        return $items->sortBy(fn($item) => $item['margin'])->first();
    }

    private function marginRows(string $tenantId): Collection
    {
        $departmentRows = collect($this->profitLossService->report($tenantId)['department_profit']['rows'] ?? []);

        return $departmentRows
            ->filter(fn ($row) => (float) ($row['revenue'] ?? 0) > 0)
            ->map(fn ($row) => [
                'name' => $row['name'],
                'margin' => round(((float) $row['profit'] / (float) $row['revenue']) * 100, 2),
            ])
            ->values();
    }
}
