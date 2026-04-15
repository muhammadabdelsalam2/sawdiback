<?php

namespace App\Http\Controllers;

use App\Models\LivestockAnimal;
use App\Models\MilkProductionLog;
use App\Models\FeedStockMovement;
use App\Models\SalesDistribution\SalesOrder;
use App\Models\AnimalVaccination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $locale = session('locale_full', 'en-SA');
        $user = auth()->user();
        
        // 1. Stats Calculation
        $stats = [
            'total_livestock' => LivestockAnimal::count(),
            'new_born_this_month' => LivestockAnimal::where('source_type', 'born')
                ->whereMonth('birth_date', now()->month)
                ->count(),
            'daily_milk_yield' => MilkProductionLog::whereDate('production_date', now()->today())
                ->sum('quantity_liters'),
            'milk_yield_delta' => -2, // Mock delta for UI
            'feed_inventory' => 12, // Mock tons
            'feed_delta' => -24, // Mock delta for UI
            'average_profit' => 45200, // Mock profit
            'profit_delta' => 12, // Mock delta for UI
        ];

        // 2. Production Chart Data (Last 7 days)
        $productionData = MilkProductionLog::select(
            DB::raw('DATE(production_date) as date'),
            DB::raw('SUM(quantity_liters) as total_yield')
        )
        ->where('production_date', '>=', now()->subDays(7))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // 3. Herd Composition Data
        $herdComposition = LivestockAnimal::select('health_status', DB::raw('count(*) as count'))
            ->groupBy('health_status')
            ->get()
            ->pluck('count', 'health_status');

        // 4. Critical Alerts (Mocked for Demo based on Vaccination Overdue)
        $alerts = $this->getDemoAlerts();

        return view('dashboard.index', compact('locale', 'stats', 'productionData', 'herdComposition', 'alerts'));
    }

    private function getDemoAlerts(): array
    {
        return [
            'critical' => [
                [
                    'title' => __('dashboard.alerts.low_feed_stock'),
                    'desc' => __('dashboard.alerts.warehouse_c_only_2_tons'),
                    'icon' => 'card-icon-3.svg',
                    'type' => 'warning'
                ],
                [
                    'title' => __('dashboard.alerts.vaccination_overdue'),
                    'desc' => __('dashboard.alerts.group_b_3_days_late'),
                    'icon' => 'card-icon-2.svg',
                    'type' => 'urgent'
                ]
            ],
            'sales' => [
                [
                    'title' => __('dashboard.alerts.machinery_maintenance'),
                    'desc' => __('dashboard.alerts.tractor_oil_filter_change'),
                    'icon' => 'card-icon-4.svg'
                ]
            ],
            'operations' => [
                [
                    'title' => __('dashboard.alerts.high_scc_detected'),
                    'desc' => __('dashboard.alerts.tank2_quality_risk'),
                    'icon' => 'card-icon-1.svg'
                ]
            ]
        ];
    }

    public function superAdminIndex(Request $request)
    {
        $locale = session('locale_full', 'en-SA');
        return view('dashboard.superadmin', compact('locale'));
    }

    public function accessManagement(Request $request)
    {
        $locale = session('locale_full', 'en-SA');
        return view('dashboard.access-management', compact('locale'));
    }
}
