<?php

namespace App\Services\Finance;

use App\Models\Employee;
use App\Services\Livestock\LivestockPenProfitService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProfitLossService
{
    public function __construct(private readonly LivestockPenProfitService $livestockPenProfitService)
    {
    }

    public function report(string $tenantId, array $filters = []): array
    {
        $dateFrom = $filters['date_from'] ?? null;
        $dateTo = $filters['date_to'] ?? null;

        $revenues = $this->sumByType($tenantId, 'revenue', $dateFrom, $dateTo, true);
        $expenses = $this->sumByType($tenantId, 'expense', $dateFrom, $dateTo, false);
        $livestockPens = $this->livestockPenProfitService->summaryForTenant($tenantId, $dateFrom, $dateTo);
        $departmentProfit = $this->departmentProfitSummary($tenantId, $dateFrom, $dateTo);
        $salesInsights = $this->salesInsights($tenantId, $dateFrom, $dateTo);
        $mortalityRate = $this->farmMortalityRate($tenantId, $dateFrom, $dateTo);
        $staffPerformance = $this->staffPerformanceSummary($tenantId, $dateFrom, $dateTo);
        $highestCost = $this->highestCost($tenantId, $dateFrom, $dateTo);

        $totalRevenue = array_sum(array_column($revenues, 'amount')) + $departmentProfit['totals']['revenue'] + $salesInsights['sales_revenue'];
        $totalExpenses = array_sum(array_column($expenses, 'amount'))
            + $departmentProfit['totals']['cost'];

        return [
            'revenues' => $revenues,
            'expenses' => $expenses,
            'total_revenue' => round($totalRevenue, 2),
            'total_expenses' => round($totalExpenses, 2),
            'net_profit' => round($totalRevenue - $totalExpenses, 2),
            'livestock_pens' => $livestockPens,
            'department_profit' => $departmentProfit,
            'sales_insights' => $salesInsights,
            'highest_cost' => $highestCost,
            'mortality_rate' => $mortalityRate,
            'staff_performance' => $staffPerformance,
        ];
    }

    private function sumByType(string $tenantId, string $type, ?string $dateFrom, ?string $dateTo, bool $creditMinusDebit): array
    {
        $query = DB::table('journal_entry_lines as l')
            ->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->join('accounts as a', 'a.id', '=', 'l.account_id')
            ->where('e.tenant_id', $tenantId)
            ->where('a.type', $type)
            ->groupBy('a.id', 'a.code', 'a.name')
            ->orderBy('a.code');

        if ($dateFrom) {
            $query->whereDate('e.entry_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('e.entry_date', '<=', $dateTo);
        }

        $rows = $query->selectRaw('a.id, a.code, a.name, SUM(l.debit) as total_debit, SUM(l.credit) as total_credit')->get();

        return $rows->map(function ($row) use ($creditMinusDebit) {
            $debit = (float) $row->total_debit;
            $credit = (float) $row->total_credit;
            $amount = $creditMinusDebit ? ($credit - $debit) : ($debit - $credit);

            return [
                'id' => $row->id,
                'code' => $row->code,
                'name' => $row->name,
                'amount' => round($amount, 2),
            ];
        })->toArray();
    }

    private function departmentProfitSummary(string $tenantId, ?string $dateFrom, ?string $dateTo): array
    {
        $livestock = $this->livestockPenProfitService->summaryForTenant($tenantId, $dateFrom, $dateTo);
        $poultry = $this->poultryFinancialSummary($tenantId, $dateFrom, $dateTo);
        $crops = $this->cropFinancialSummary($tenantId, $dateFrom, $dateTo);

        $rows = [
            ['key' => 'poultry', 'name' => __('finance.profit_loss.departments.poultry'), 'profit' => $poultry['net_profit'], 'revenue' => $poultry['revenue'], 'cost' => $poultry['cost']],
            ['key' => 'crops', 'name' => __('finance.profit_loss.departments.crops'), 'profit' => $crops['net_profit'], 'revenue' => $crops['revenue'], 'cost' => $crops['cost']],
            ['key' => 'livestock', 'name' => __('finance.profit_loss.departments.livestock'), 'profit' => $livestock['net_profit'], 'revenue' => $livestock['revenue'], 'cost' => $livestock['cost']],
        ];

        return [
            'rows' => $rows,
            'totals' => [
                'revenue' => round(array_sum(array_column($rows, 'revenue')), 2),
                'cost' => round(array_sum(array_column($rows, 'cost')), 2),
                'profit' => round(array_sum(array_column($rows, 'profit')), 2),
            ],
        ];
    }

    private function salesInsights(string $tenantId, ?string $dateFrom, ?string $dateTo): array
    {
        $query = DB::table('sales_order_items as i')
            ->join('sales_orders as o', 'o.id', '=', 'i.sales_order_id')
            ->leftJoin('inventory_products as p', 'p.id', '=', 'i.product_id')
            ->where('o.tenant_id', $tenantId);

        if ($dateFrom) {
            $query->whereDate('o.order_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('o.order_date', '<=', $dateTo);
        }

        $bestProduct = (clone $query)
            ->selectRaw('COALESCE(p.name, CONCAT("Product #", i.product_id)) as product_name, SUM(i.qty) as quantity, SUM(i.line_total) as value')
            ->groupBy('i.product_id', 'p.name')
            ->orderByDesc('value')
            ->first();

        $salesRevenue = (float) (clone $query)->sum('i.line_total');

        return [
            'best_product' => $bestProduct ? [
                'name' => $bestProduct->product_name,
                'quantity' => round((float) $bestProduct->quantity, 2),
                'value' => round((float) $bestProduct->value, 2),
            ] : null,
            'sales_revenue' => round($salesRevenue, 2),
        ];
    }

    private function farmMortalityRate(string $tenantId, ?string $dateFrom, ?string $dateTo): array
    {
        $broilerMortalities = DB::table('poultry_broiler_mortalities')->where('tenant_id', $tenantId);
        $layerMortalities = DB::table('poultry_layer_mortalities')->where('tenant_id', $tenantId);

        if ($dateFrom) {
            $broilerMortalities->whereDate('mortality_date', '>=', $dateFrom);
            $layerMortalities->whereDate('mortality_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $broilerMortalities->whereDate('mortality_date', '<=', $dateTo);
            $layerMortalities->whereDate('mortality_date', '<=', $dateTo);
        }

        $poultryDeaths = (float) $broilerMortalities->sum('quantity') + (float) $layerMortalities->sum('quantity');
        $poultryTotal = (float) DB::table('poultry_broiler_cycles')->where('tenant_id', $tenantId)->sum('chick_count')
            + (float) DB::table('poultry_layer_flocks')->where('tenant_id', $tenantId)->sum('chicken_count');
        $livestockDeaths = (float) DB::table('livestock_animals')->where('tenant_id', $tenantId)->where('status', 'dead')->count();
        $livestockTotal = (float) DB::table('livestock_animals')->where('tenant_id', $tenantId)->count();
        $deaths = $poultryDeaths + $livestockDeaths;
        $total = $poultryTotal + $livestockTotal;

        return [
            'deaths' => round($deaths, 2),
            'total' => round($total, 2),
            'rate' => $total > 0 ? round(($deaths / $total) * 100, 2) : 0,
        ];
    }

    private function staffPerformanceSummary(string $tenantId, ?string $dateFrom, ?string $dateTo): array
    {
        $employees = Employee::query()->where('tenant_id', $tenantId)->get();

        if ($employees->isEmpty()) {
            return [
                'message' => 'No employee operational data is available yet for a real performance metric.',
            ];
        }

        $rows = [];
        foreach ($employees->groupBy('operational_department') as $department => $items) {
            $attendanceQuery = DB::table('attendances')
                ->where('tenant_id', $tenantId)
                ->whereIn('employee_id', $items->pluck('id'));

            if ($dateFrom) {
                $attendanceQuery->whereDate('day', '>=', $dateFrom);
            }
            if ($dateTo) {
                $attendanceQuery->whereDate('day', '<=', $dateTo);
            }

            $attendanceCount = (clone $attendanceQuery)->count();
            $days = $this->staffPerformanceDays($tenantId, $items->pluck('id')->all(), $dateFrom, $dateTo);
            $capacity = $items->count() * $days;

            $rows[] = [
                'department' => $department ?? 'Unassigned',
                'employee_count' => $items->count(),
                'attendance_count' => $attendanceCount,
                'attendance_rate' => $capacity > 0 ? min(100, round(($attendanceCount / $capacity) * 100, 2)) : 0,
            ];
        }

        return $rows;
    }

    private function staffPerformanceDays(string $tenantId, array $employeeIds, ?string $dateFrom, ?string $dateTo): int
    {
        if ($dateFrom && $dateTo) {
            return max(1, Carbon::parse($dateFrom)->diffInDays(Carbon::parse($dateTo)) + 1);
        }

        $query = DB::table('attendances')
            ->where('tenant_id', $tenantId)
            ->whereIn('employee_id', $employeeIds);

        if ($dateFrom) {
            $query->whereDate('day', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('day', '<=', $dateTo);
        }

        return max(1, (int) $query->distinct()->count('day'));
    }

    private function poultryFinancialSummary(string $tenantId, ?string $dateFrom, ?string $dateTo): array
    {
        $broilerSales = DB::table('poultry_broiler_sales')->where('tenant_id', $tenantId);
        $broilerCosts = DB::table('poultry_broiler_costs')->where('tenant_id', $tenantId);
        $layerEggs = DB::table('poultry_layer_egg_production_logs')->where('tenant_id', $tenantId);
        $layerFlocks = DB::table('poultry_layer_flocks')->where('tenant_id', $tenantId);

        if ($dateFrom) {
            $broilerSales->whereDate('sale_date', '>=', $dateFrom);
            $broilerCosts->whereDate('cost_date', '>=', $dateFrom);
            $layerEggs->whereDate('production_date', '>=', $dateFrom);
            $layerFlocks->whereDate('started_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $broilerSales->whereDate('sale_date', '<=', $dateTo);
            $broilerCosts->whereDate('cost_date', '<=', $dateTo);
            $layerEggs->whereDate('production_date', '<=', $dateTo);
            $layerFlocks->whereDate('started_at', '<=', $dateTo);
        }

        $broilerRevenue = (float) $broilerSales->sum('total_amount');
        $broilerCost = (float) $broilerCosts->sum('amount');
        $layerRevenue = (float) (clone $layerEggs)->selectRaw('SUM((eggs_count - damaged_count) * sale_price) as total')->value('total');
        $layerFeedCost = (float) $layerEggs->sum('daily_feed_cost');
        $layerPurchaseCost = (float) $layerFlocks->sum('purchase_cost');
        $revenue = $broilerRevenue + $layerRevenue;
        $cost = $broilerCost + $layerFeedCost + $layerPurchaseCost;

        return ['revenue' => round($revenue, 2), 'cost' => round($cost, 2), 'net_profit' => round($revenue - $cost, 2)];
    }

    private function cropFinancialSummary(string $tenantId, ?string $dateFrom, ?string $dateTo): array
    {
        $crops = DB::table('crops')->where('tenant_id', $tenantId);
        $costs = DB::table('crop_cost_items')->where('tenant_id', $tenantId);
        $materials = DB::table('crop_material_usages')->where('tenant_id', $tenantId);

        if ($dateFrom) {
            $crops->whereDate('planting_date', '>=', $dateFrom);
            $costs->whereDate('cost_date', '>=', $dateFrom);
            $materials->whereDate('used_on', '>=', $dateFrom);
        }
        if ($dateTo) {
            $crops->whereDate('planting_date', '<=', $dateTo);
            $costs->whereDate('cost_date', '<=', $dateTo);
            $materials->whereDate('used_on', '<=', $dateTo);
        }

        $revenue = (float) (clone $crops)->selectRaw('SUM(COALESCE(yield_tons, 0) * COALESCE(sale_price_per_ton, 0)) as total')->value('total');
        $directCosts = (float) $costs->sum('amount');
        $materialCosts = (float) $materials->sum('amount');
        $operationalCosts = (float) $crops->sum('water_cost') + (float) $crops->sum('labor_cost');
        $cost = $directCosts + $materialCosts + $operationalCosts;

        return ['revenue' => round($revenue, 2), 'cost' => round($cost, 2), 'net_profit' => round($revenue - $cost, 2)];
    }

    private function highestCost(string $tenantId, ?string $dateFrom, ?string $dateTo): ?array
    {
        $items = collect();
        $this->appendHighest($items, 'poultry_broiler_costs', 'cost_type', 'amount', 'cost_date', $tenantId, $dateFrom, $dateTo);
        $this->appendHighest($items, 'crop_cost_items', 'item', 'amount', 'cost_date', $tenantId, $dateFrom, $dateTo);
        $this->appendHighest($items, 'crop_material_usages', 'name', 'amount', 'used_on', $tenantId, $dateFrom, $dateTo);
        $this->appendHighest($items, 'animal_feeding_logs', 'feed_type_id', 'total_cost', 'feeding_date', $tenantId, $dateFrom, $dateTo);
        $this->appendHighest($items, 'livestock_pen_financial_entries', 'type', 'amount', 'entry_date', $tenantId, $dateFrom, $dateTo);

        return $items->sortByDesc('amount')->first();
    }

    private function appendHighest($items, string $table, string $labelColumn, string $amountColumn, string $dateColumn, string $tenantId, ?string $dateFrom, ?string $dateTo): void
    {
        $query = DB::table($table)->where('tenant_id', $tenantId);
        if ($dateFrom) {
            $query->whereDate($dateColumn, '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate($dateColumn, '<=', $dateTo);
        }

        $row = $query->selectRaw("{$labelColumn} as label, {$amountColumn} as amount")->orderByDesc($amountColumn)->first();
        if ($row && (float) $row->amount > 0) {
            $items->push(['source' => $table, 'label' => (string) $row->label, 'amount' => round((float) $row->amount, 2)]);
        }
    }
}
