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
use App\Services\Finance\ProfitLossService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $locale = session('locale_full', 'en-SA');
        $user = Auth::user();
        $tenantId = (string) $user->tenant_id;
        $profitLoss = app(ProfitLossService::class)->report($tenantId);

        $stats = [
            'total_livestock' => LivestockAnimal::where('tenant_id', $tenantId)->count(),
            'new_born_this_month' => LivestockAnimal::where('source_type', 'born')
                ->where('tenant_id', $tenantId)
                ->whereMonth('birth_date', now()->month)
                ->count(),
            'daily_milk_yield' => MilkProductionLog::whereDate('production_date', now()->today())
                ->where('tenant_id', $tenantId)
                ->sum('quantity_liters'),
            'milk_yield_delta' => $this->monthDelta(
                (float) MilkProductionLog::where('tenant_id', $tenantId)->whereBetween('production_date', [now()->startOfMonth(), now()])->sum('quantity_liters'),
                (float) MilkProductionLog::where('tenant_id', $tenantId)->whereBetween('production_date', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->sum('quantity_liters')
            ),
            'feed_inventory' => $this->feedInventoryTons($tenantId),
            'feed_delta' => 0,
            'average_profit' => round(($profitLoss['net_profit'] ?? 0) / max(1, now()->day), 2),
            'profit_delta' => 0,
        ];

        $productionData = MilkProductionLog::select(
            DB::raw('DATE(production_date) as date'),
            DB::raw('SUM(quantity_liters) as total_yield')
        )
        ->where('tenant_id', $tenantId)
        ->where('production_date', '>=', now()->subDays(7))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        $herdComposition = LivestockAnimal::select('health_status', DB::raw('count(*) as count'))
            ->where('tenant_id', $tenantId)
            ->groupBy('health_status')
            ->get()
            ->pluck('count', 'health_status');

        $alerts = $this->dashboardAlerts($tenantId);
        $profitabilityData = [
            'labels' => collect($profitLoss['department_profit']['rows'] ?? [])->pluck('name')->values(),
            'revenue' => collect($profitLoss['department_profit']['rows'] ?? [])->pluck('revenue')->values(),
            'cost' => collect($profitLoss['department_profit']['rows'] ?? [])->pluck('cost')->values(),
        ];

        return view('dashboard.index', compact('locale', 'stats', 'productionData', 'herdComposition', 'alerts', 'profitabilityData'));
    }

    private function dashboardAlerts(string $tenantId): array
    {
        $critical = [];
        $lowFeed = DB::table('feed_stock_movements')
            ->where('tenant_id', $tenantId)
            ->selectRaw('feed_type_id, SUM(CASE WHEN movement_type = "in" THEN quantity ELSE -quantity END) as stock')
            ->groupBy('feed_type_id')
            ->havingRaw('stock <= 0')
            ->first();
        if ($lowFeed) {
            $critical[] = [
                'title' => __('dashboard.alerts.low_feed_stock'),
                'desc' => __('dashboard.alerts.feed_stock_depleted'),
                'icon' => 'card-icon-3.svg',
                'type' => 'warning',
            ];
        }

        $overdueVaccination = AnimalVaccination::where('tenant_id', $tenantId)
            ->whereNotNull('next_due_date')
            ->whereDate('next_due_date', '<', now()->toDateString())
            ->first();
        if ($overdueVaccination) {
            $critical[] = [
                'title' => __('dashboard.alerts.vaccination_overdue'),
                'desc' => __('dashboard.alerts.vaccination_overdue_real'),
                'icon' => 'card-icon-2.svg',
                'type' => 'urgent',
            ];
        }

        $sales = SalesOrder::where('tenant_id', $tenantId)
            ->whereIn('status', ['draft', 'confirmed'])
            ->latest('order_date')
            ->take(3)
            ->get()
            ->map(fn (SalesOrder $order) => [
                'title' => $order->order_no,
                'desc' => __('dashboard.alerts.sales_order_total', ['total' => number_format((float) $order->total, 2)]),
                'icon' => 'card-icon-4.svg',
            ])->all();

        $operations = collect();
        $today = now()->toDateString();
        $operations = $operations->merge(DB::table('animal_feeding_logs')->where('tenant_id', $tenantId)->whereDate('feeding_date', $today)->take(2)->get()->map(fn () => [
            'title' => __('dashboard.alerts.feeding_logged_today'),
            'desc' => __('dashboard.alerts.real_operation_from_feeding'),
            'icon' => 'card-icon-1.svg',
        ]));
        $operations = $operations->merge(DB::table('poultry_broiler_mortalities')->where('tenant_id', $tenantId)->whereDate('mortality_date', $today)->take(2)->get()->map(fn ($row) => [
            'title' => __('dashboard.alerts.poultry_mortality_logged'),
            'desc' => __('dashboard.alerts.quantity_value', ['quantity' => $row->quantity]),
            'icon' => 'card-icon-1.svg',
        ]));

        return [
            'critical' => $critical ?: [[
                'title' => __('dashboard.alerts.no_critical_alerts'),
                'desc' => __('dashboard.alerts.no_critical_alerts_desc'),
                'icon' => 'card-icon-3.svg',
                'type' => 'warning',
            ]],
            'sales' => $sales ?: [[
                'title' => __('dashboard.alerts.no_active_sales_orders'),
                'desc' => __('dashboard.alerts.no_active_sales_orders_desc'),
                'icon' => 'card-icon-4.svg',
            ]],
            'operations' => $operations->values()->all() ?: [[
                'title' => __('dashboard.alerts.no_operations_today'),
                'desc' => __('dashboard.alerts.no_operations_today_desc'),
                'icon' => 'card-icon-1.svg',
            ]],
        ];
    }

    private function feedInventoryTons(string $tenantId): float
    {
        $stock = (float) FeedStockMovement::where('tenant_id', $tenantId)
            ->selectRaw('SUM(CASE WHEN movement_type = "in" THEN quantity ELSE -quantity END) as total')
            ->value('total');

        return round($stock, 2);
    }

    private function monthDelta(float $current, float $previous): float
    {
        if ($previous <= 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 2);
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
            'farms' => DB::table('farms')->whereNull('deleted_at')->count(),
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

        $farmSummaryCards = DB::table('farms')
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->get(['id', 'name', 'type', 'location'])
            ->map(function ($farm) {
                $pensCount = DB::table('farm_pens')
                    ->where('farm_id', $farm->id)
                    ->whereNull('deleted_at')
                    ->count();

                $animalsCount = DB::table('livestock_animals')
                    ->join('farm_pens', 'livestock_animals.pen_id', '=', 'farm_pens.id')
                    ->where('farm_pens.farm_id', $farm->id)
                    ->whereNull('farm_pens.deleted_at')
                    ->count();

                $poultryGroupsCount = collect([
                    'poultry_broiler_cycles',
                    'poultry_layer_flocks',
                    'poultry_chicken_breeds',
                ])->sum(fn (string $table) => DB::table($table)
                    ->join('farm_pens', "{$table}.pen_id", '=', 'farm_pens.id')
                    ->where('farm_pens.farm_id', $farm->id)
                    ->whereNull('farm_pens.deleted_at')
                    ->whereNull("{$table}.deleted_at")
                    ->count());

                return [
                    'name' => $farm->name,
                    'type' => $farm->type,
                    'location' => $farm->location,
                    'pens_count' => $pensCount,
                    'animals_count' => $animalsCount,
                    'poultry_groups_count' => $poultryGroupsCount,
                ];
            });

        return view('dashboard.superadmin', compact(
            'locale',
            'summary',
            'revenueTrend',
            'ordersByStatus',
            'topCustomers',
            'customerGrowth',
            'customerInsightCards',
            'analyticsCards',
            'farmSummaryCards'
        ));
    }

    public function accessManagement(Request $request)
    {
        $locale = session('locale_full', 'en-SA');
        return view('dashboard.access-management', compact('locale'));
    }
}
