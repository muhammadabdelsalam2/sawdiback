<?php

namespace App\Services\Livestock;

use App\Models\FarmPen;
use Illuminate\Support\Facades\DB;

class LivestockPenProfitService
{
    public function totalSales(FarmPen $pen): float
    {
        return (float) $pen->financialEntries()
            ->where('type', 'sale')
            ->sum('amount');
    }

    public function feedCosts(FarmPen $pen): float
    {
        return (float) $pen->animals()
            ->join('animal_feeding_logs', 'animal_feeding_logs.animal_id', '=', 'livestock_animals.id')
            ->sum('animal_feeding_logs.total_cost');
    }

    public function slaughterPackagingCosts(FarmPen $pen): float
    {
        return (float) $pen->financialEntries()
            ->where('type', 'slaughter_packaging')
            ->sum('amount');
    }

    public function summary(FarmPen $pen): array
    {
        $sales = $this->totalSales($pen);
        $feedCosts = $this->feedCosts($pen);
        $slaughterPackagingCosts = $this->slaughterPackagingCosts($pen);

        return [
            'total_sales' => number_format($sales, 2, '.', ''),
            'feed_costs' => number_format($feedCosts, 2, '.', ''),
            'slaughter_packaging_costs' => number_format($slaughterPackagingCosts, 2, '.', ''),
            'net_profit' => number_format($sales - ($feedCosts + $slaughterPackagingCosts), 2, '.', ''),
            'mortality_rate' => $this->mortalityRate($pen),
        ];
    }

    public function summaryForTenant(string $tenantId, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $entries = DB::table('livestock_pen_financial_entries')
            ->where('tenant_id', $tenantId);

        if ($dateFrom) {
            $entries->whereDate('entry_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $entries->whereDate('entry_date', '<=', $dateTo);
        }

        $sales = (float) (clone $entries)->where('type', 'sale')->sum('amount');
        $slaughterPackagingCosts = (float) (clone $entries)->where('type', 'slaughter_packaging')->sum('amount');

        $feedCosts = DB::table('animal_feeding_logs')
            ->where('tenant_id', $tenantId);

        if ($dateFrom) {
            $feedCosts->whereDate('feeding_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $feedCosts->whereDate('feeding_date', '<=', $dateTo);
        }

        $feedCosts = (float) $feedCosts->sum('total_cost');

        return [
            'total_sales' => round($sales, 2),
            'feed_costs' => round($feedCosts, 2),
            'slaughter_packaging_costs' => round($slaughterPackagingCosts, 2),
            'net_profit' => round($sales - ($feedCosts + $slaughterPackagingCosts), 2),
            'revenue' => round($sales, 2),
            'cost' => round($feedCosts + $slaughterPackagingCosts, 2),
        ];
    }

    public function netProfit(FarmPen $pen): string
    {
        $profit = $this->totalSales($pen) - ($this->feedCosts($pen) + $this->slaughterPackagingCosts($pen));

        return number_format($profit, 2, '.', '');
    }

    public function mortalityRate(FarmPen $pen): string
    {
        $total = $pen->animals()->count();
        if ($total === 0) {
            return '0.00';
        }

        $dead = $pen->animals()->where('status', 'dead')->count();

        return number_format(($dead / $total) * 100, 2, '.', '');
    }
}
