<?php

namespace App\Http\Controllers;

use App\Models\LivestockAnimal;
use App\Models\MilkProductionLog;
use App\Models\FeedStockMovement;
use App\Models\SalesDistribution\SalesOrder;
use App\Models\AnimalVaccination;
use App\Models\InventoryProduct;
use App\Models\InventoryProductionRecord;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $locale = session('locale_full', 'en-SA');
        $user = Auth::user();

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
        $growthStart = now()->startOfMonth()->subMonths(5);

        $summary = [
            'customers' => User::query()
                ->whereHas('roles', fn ($query) => $query->where('name', 'Customer'))
                ->count(),
            'orders' => Order::withoutGlobalScopes()->count(),
            'products' => InventoryProduct::withoutGlobalScopes()->count(),
            'revenue' => Order::withoutGlobalScopes()->sum('total'),
            'analytics' => Order::withoutGlobalScopes()->distinct('status')->count('status'),
        ];

        $revenueTrend = Order::withoutGlobalScopes()
            ->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $ordersByStatus = Order::withoutGlobalScopes()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        $customers = User::query()
            ->whereHas('roles', fn ($query) => $query->where('name', 'Customer'))
            ->with(['orders' => fn ($query) => $query->with('items')->latest()])
            ->latest()
            ->get();

        $growthMonths = collect(range(0, 5))
            ->map(fn (int $index) => $growthStart->copy()->addMonths($index));

        $maxCustomerRevenue = max(
            1,
            (float) $customers->map(fn (User $customer) => $customer->orders->sum('total'))->max()
        );

        $maxCustomerOrders = max(
            1,
            (int) $customers->map(fn (User $customer) => $customer->orders->count())->max()
        );

        $customerInsightCards = $customers
            ->map(function (User $customer) use ($maxCustomerRevenue, $maxCustomerOrders, $growthMonths) {
                $orders = $customer->orders;
                $ordersCount = $orders->count();
                $totalRevenue = (float) $orders->sum('total');
                $productsCount = $orders
                    ->flatMap(fn (Order $order) => $order->items)
                    ->pluck('inventory_product_id')
                    ->filter()
                    ->unique()
                    ->count();
                $lastActivity = $orders->first()?->created_at ?? $customer->updated_at ?? $customer->created_at;
                $revenueScore = ($totalRevenue / $maxCustomerRevenue) * 70;
                $ordersScore = ($ordersCount / $maxCustomerOrders) * 30;
                $activityPercent = min(100, (int) round($revenueScore + $ordersScore));
                $sparkline = $growthMonths
                    ->map(function ($month) use ($orders) {
                        $monthKey = $month->format('Y-m');

                        return (float) $orders
                            ->filter(fn (Order $order) => $order->created_at?->format('Y-m') === $monthKey)
                            ->sum('total');
                    })
                    ->values()
                    ->all();

                return [
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'orders_count' => $ordersCount,
                    'total_revenue' => $totalRevenue,
                    'products_count' => $productsCount,
                    'status' => $ordersCount > 0 ? 'active' : 'inactive',
                    'last_activity' => $lastActivity,
                    'last_order_date' => $orders->first()?->created_at,
                    'activity_percent' => $activityPercent,
                    'sparkline' => $sparkline,
                ];
            })
            ->sortByDesc('total_revenue')
            ->take(9)
            ->values();

        $topCustomers = $customerInsightCards
            ->take(3)
            ->values();

        $growthOrders = Order::withoutGlobalScopes()
            ->where('created_at', '>=', $growthStart)
            ->get(['id', 'user_id', 'total', 'created_at']);

        $growthUsers = User::query()
            ->whereHas('roles', fn ($query) => $query->where('name', 'Customer'))
            ->where('created_at', '>=', $growthStart)
            ->get(['id', 'created_at']);

        $customerGrowth = $growthMonths->map(function ($month) use ($growthOrders, $growthUsers) {
            $monthKey = $month->format('Y-m');
            $monthOrders = $growthOrders->filter(fn (Order $order) => $order->created_at?->format('Y-m') === $monthKey);

            return [
                'label' => $month->format('M'),
                'customers' => $growthUsers->filter(fn (User $user) => $user->created_at?->format('Y-m') === $monthKey)->count(),
                'orders' => $monthOrders->count(),
                'revenue' => (float) $monthOrders->sum('total'),
                'activity' => $monthOrders->pluck('user_id')->filter()->unique()->count(),
            ];
        })->values();

        $currentMonthCustomers = (int) ($customerGrowth->last()['customers'] ?? 0);
        $previousMonthCustomers = (int) ($customerGrowth->slice(-2, 1)->first()['customers'] ?? 0);
        $monthlyGrowth = $previousMonthCustomers > 0
            ? (int) round((($currentMonthCustomers - $previousMonthCustomers) / $previousMonthCustomers) * 100)
            : ($currentMonthCustomers > 0 ? 100 : 0);

        $returningCustomers = $customers
            ->filter(fn (User $customer) => $customer->orders->count() > 1)
            ->count();

        $analyticsCards = [
            [
                'label' => __('superadmin.customer_insights.top_customer'),
                'value' => $topCustomers->first()['name'] ?? __('superadmin.messages.no_data'),
                'meta' => number_format((float) ($topCustomers->first()['total_revenue'] ?? 0), 2) . ' SAR',
                'icon' => 'bi-trophy',
                'tone' => 'success',
            ],
            [
                'label' => __('superadmin.customer_insights.highest_revenue'),
                'value' => number_format((float) ($topCustomers->first()['total_revenue'] ?? 0), 2) . ' SAR',
                'meta' => $topCustomers->first()['name'] ?? '-',
                'icon' => 'bi-cash-stack',
                'tone' => 'info',
            ],
            [
                'label' => __('superadmin.customer_insights.most_orders'),
                'value' => number_format((int) ($customerInsightCards->sortByDesc('orders_count')->first()['orders_count'] ?? 0)),
                'meta' => $customerInsightCards->sortByDesc('orders_count')->first()['name'] ?? '-',
                'icon' => 'bi-cart-check',
                'tone' => 'warning',
            ],
            [
                'label' => __('superadmin.customer_insights.active_customers'),
                'value' => number_format($customerInsightCards->where('status', 'active')->count()),
                'meta' => __('superadmin.customer_insights.active_now'),
                'icon' => 'bi-activity',
                'tone' => 'success',
            ],
            [
                'label' => __('superadmin.customer_insights.monthly_growth'),
                'value' => ($monthlyGrowth > 0 ? '+' : '') . $monthlyGrowth . '%',
                'meta' => __('superadmin.customer_insights.current_month'),
                'icon' => 'bi-graph-up-arrow',
                'tone' => 'primary',
            ],
            [
                'label' => __('superadmin.customer_insights.returning_customers'),
                'value' => number_format($returningCustomers),
                'meta' => __('superadmin.customer_insights.repeat_orders'),
                'icon' => 'bi-arrow-repeat',
                'tone' => 'secondary',
            ],
        ];

        return view('dashboard.superadmin', compact(
            'locale',
            'summary',
            'revenueTrend',
            'ordersByStatus',
            'topCustomers',
            'customerGrowth',
            'customerInsightCards',
            'analyticsCards'
        ));
    }

    public function accessManagement(Request $request)
    {
        $locale = session('locale_full', 'en-SA');
        return view('dashboard.access-management', compact('locale'));
    }
}
