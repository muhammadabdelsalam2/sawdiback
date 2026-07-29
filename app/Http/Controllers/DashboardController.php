<?php

namespace App\Http\Controllers;

use App\Models\LivestockAnimal;
use App\Models\Farm;
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
use Illuminate\Support\Facades\Schema;

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
        $farms = Farm::withoutGlobalScopes()
            ->withCount('pens')
            ->orderBy('id')
            ->take(4)
            ->get()
            ->map(fn (Farm $farm) => $this->farmCardData($farm));

        return view('dashboard.superadmin', compact('locale', 'farms'));
    }

    public function superAdminFarmDashboard(Request $request, string $locale, int $farm)
    {
        $farm = Farm::withoutGlobalScopes()
            ->with(['pens' => fn ($query) => $query->withCount('animals')])
            ->findOrFail($farm);

        $dashboard = $this->farmDashboardData($farm);

        return view('dashboard.superadmin-farm', compact('locale', 'farm', 'dashboard'));
    }

    private function farmCardData(Farm $farm): array
    {
        $penIds = $this->farmPenIds($farm->id);
        $tenantId = (string) $farm->tenant_id;

        return [
            'id' => $farm->id,
            'name' => $farm->name,
            'type' => $farm->type,
            'location' => $farm->location,
            'status' => $farm->is_active ? 'active' : 'inactive',
            'image_url' => $this->farmImageUrl($farm->id),
            'animals_count' => $this->countTable('livestock_animals', fn ($query) => $query->whereIn('pen_id', $penIds)),
            'workers_count' => $this->farmLinkedCount('employees', $farm->id, $penIds),
            'pens_count' => count($penIds),
            'products_count' => $this->countTable('inventory_products', fn ($query) => $query->where('farm_id', $farm->id)),
            'orders_count' => $this->farmOrdersCount($farm->id),
            'milk_total' => $this->farmMilkTotal($tenantId, $penIds),
            'poultry_groups_count' => $this->farmPoultryGroupsCount($farm->id),
            'inventory_stock' => $this->farmInventoryStock($farm->id),
        ];
    }

    private function farmDashboardData(Farm $farm): array
    {
        $penIds = $this->farmPenIds($farm->id);
        $tenantId = (string) $farm->tenant_id;
        $financials = $this->farmFinancials($farm->id, $penIds);

        return [
            'card' => $this->farmCardData($farm),
            'stats' => [
                'reports' => $this->farmReportsCount($farm->id, $penIds),
                'workers' => $this->farmLinkedCount('employees', $farm->id, $penIds),
                'attendance_today' => $this->farmAttendanceToday($farm->id, $penIds),
                'animals' => $this->countTable('livestock_animals', fn ($query) => $query->whereIn('pen_id', $penIds)),
                'pens' => count($penIds),
                'dairy_products' => $this->countTable('inventory_products', fn ($query) => $query->where('farm_id', $farm->id)->where('category', 'animal_product')),
                'products' => $this->countTable('inventory_products', fn ($query) => $query->where('farm_id', $farm->id)),
                'inventory' => $this->farmInventoryStock($farm->id),
                'orders' => $this->farmOrdersCount($farm->id),
                'customers' => $this->farmCustomersCount($farm->id),
                'revenue' => $financials['revenue'],
                'expenses' => $financials['expenses'],
                'milk_total' => $this->farmMilkTotal($tenantId, $penIds),
                'alerts' => count($this->farmAlerts($farm->id, $tenantId, $penIds)),
            ],
            'pens' => DB::table('farm_pens')
                ->whereIn('id', $penIds)
                ->orderBy('pen_number')
                ->get(),
            'workers' => $this->farmWorkers($farm->id, $penIds),
            'attendance' => $this->farmAttendance($farm->id, $penIds),
            'animals' => $this->farmAnimals($penIds),
            'products' => $this->farmProducts($farm->id),
            'orders' => $this->farmOrders($farm->id),
            'customers' => $this->farmCustomers($farm->id),
            'reports' => $this->farmReports($farm->id, $penIds),
            'activities' => $this->farmActivities($farm->id, $penIds),
            'alerts' => $this->farmAlerts($farm->id, $tenantId, $penIds),
            'charts' => [
                'milk' => $this->farmMilkChart($tenantId, $penIds),
                'orders' => $this->farmOrdersChart($farm->id),
                'animals' => $this->farmAnimalStatusChart($penIds),
                'finance' => $this->farmFinanceChart($farm->id, $penIds),
            ],
            'notes' => [
                'workers' => $this->hasFarmLink('employees') ? null : __('superadmin.farm_dashboard.not_linked_to_farm'),
                'attendance' => $this->hasFarmLink('employees') ? null : __('superadmin.farm_dashboard.not_linked_to_farm'),
            ],
        ];
    }

    private function farmPenIds(int $farmId): array
    {
        if (! Schema::hasTable('farm_pens')) {
            return [];
        }

        return DB::table('farm_pens')
            ->where('farm_id', $farmId)
            ->whereNull('deleted_at')
            ->pluck('id')
            ->all();
    }

    private function countTable(string $table, callable $scope): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        return (int) $scope(DB::table($table))->count();
    }

    private function sumTable(string $table, string $column, callable $scope): float
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return 0.0;
        }

        return (float) $scope(DB::table($table))->sum($column);
    }

    private function hasFarmLink(string $table): bool
    {
        return Schema::hasTable($table) && Schema::hasColumn($table, 'farm_id');
    }

    private function farmLinkedCount(string $table, int $farmId, array $penIds): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        if (Schema::hasColumn($table, 'farm_id')) {
            return (int) DB::table($table)->where('farm_id', $farmId)->count();
        }

        if (Schema::hasColumn($table, 'pen_id')) {
            return (int) DB::table($table)->whereIn('pen_id', $penIds)->count();
        }

        return 0;
    }

    private function farmImageUrl(int $farmId): ?string
    {
        if (
            ! Schema::hasTable('warehouse_assets')
            || ! Schema::hasTable('warehouse_asset_attachments')
            || ! Schema::hasColumn('warehouse_asset_attachments', 'path')
        ) {
            return null;
        }

        $path = DB::table('warehouse_asset_attachments')
            ->join('warehouse_assets', 'warehouse_asset_attachments.warehouse_asset_id', '=', 'warehouse_assets.id')
            ->where('warehouse_assets.farm_id', $farmId)
            ->orderByDesc('warehouse_asset_attachments.id')
            ->value('warehouse_asset_attachments.path');

        return $path ? asset('storage/' . ltrim((string) $path, '/')) : null;
    }

    private function farmMilkTotal(string $tenantId, array $penIds): float
    {
        if (! Schema::hasTable('milk_production_logs') || ! Schema::hasTable('livestock_animals')) {
            return 0.0;
        }

        return (float) DB::table('milk_production_logs')
            ->join('livestock_animals', 'milk_production_logs.animal_id', '=', 'livestock_animals.id')
            ->where('milk_production_logs.tenant_id', $tenantId)
            ->whereIn('livestock_animals.pen_id', $penIds)
            ->sum('milk_production_logs.quantity_liters');
    }

    private function farmPoultryGroupsCount(int $farmId): int
    {
        if (! Schema::hasTable('farm_pens')) {
            return 0;
        }

        $penLinkedGroups = collect(['poultry_broiler_cycles', 'poultry_layer_flocks', 'poultry_chicken_breeds'])
            ->sum(function (string $table) use ($farmId) {
                if (! Schema::hasTable($table)) {
                    return 0;
                }

                $query = DB::table($table)
                    ->join('farm_pens', "{$table}.pen_id", '=', 'farm_pens.id')
                    ->where('farm_pens.farm_id', $farmId)
                    ->whereNull('farm_pens.deleted_at');

                if (Schema::hasColumn($table, 'deleted_at')) {
                    $query->whereNull("{$table}.deleted_at");
                }

                return (int) $query->count();
            });

        $hatcheryBatches = 0;
        if (
            Schema::hasTable('poultry_hatchery_batches')
            && Schema::hasTable('poultry_hatchery_machines')
            && Schema::hasColumn('poultry_hatchery_machines', 'farm_id')
        ) {
            $query = DB::table('poultry_hatchery_batches')
                ->join('poultry_hatchery_machines', 'poultry_hatchery_batches.hatchery_machine_id', '=', 'poultry_hatchery_machines.id')
                ->where('poultry_hatchery_machines.farm_id', $farmId);

            if (Schema::hasColumn('poultry_hatchery_batches', 'deleted_at')) {
                $query->whereNull('poultry_hatchery_batches.deleted_at');
            }
            if (Schema::hasColumn('poultry_hatchery_machines', 'deleted_at')) {
                $query->whereNull('poultry_hatchery_machines.deleted_at');
            }

            $hatcheryBatches = (int) $query->count();
        }

        return $penLinkedGroups + $hatcheryBatches;
    }

    private function farmInventoryStock(int $farmId): float
    {
        if (! Schema::hasTable('inventory_batches') || ! Schema::hasTable('inventory_products')) {
            return 0.0;
        }

        return (float) DB::table('inventory_batches')
            ->join('inventory_products', 'inventory_batches.inventory_product_id', '=', 'inventory_products.id')
            ->where('inventory_products.farm_id', $farmId)
            ->sum('inventory_batches.quantity_available');
    }

    private function farmOrdersCount(int $farmId): int
    {
        return $this->farmEcommerceOrders($farmId)->count() + $this->farmSalesOrders($farmId)->count();
    }

    private function farmEcommerceOrders(int $farmId)
    {
        if (! Schema::hasTable('orders') || ! Schema::hasTable('order_items') || ! Schema::hasTable('inventory_products')) {
            return collect();
        }

        return Order::query()
            ->linkedToFarm($farmId)
            ->orderByDesc('orders.created_at')
            ->get(['id', 'order_no', 'status', 'created_at'])
            ->map(fn (Order $order) => (object) [
                'id' => $order->id,
                'order_no' => $order->order_no,
                'status' => $order->status,
                'created_at' => $order->created_at,
                'total' => $order->farmLineTotal($farmId),
            ]);
    }

    private function farmSalesOrders(int $farmId)
    {
        if (! Schema::hasTable('sales_orders') || ! Schema::hasTable('sales_order_items') || ! Schema::hasTable('inventory_products')) {
            return collect();
        }

        return SalesOrder::query()
            ->linkedToFarm($farmId)
            ->orderByDesc('sales_orders.order_date')
            ->get(['id', 'order_no', 'status', 'order_date'])
            ->map(fn (SalesOrder $order) => (object) [
                'id' => $order->id,
                'order_no' => $order->order_no,
                'status' => $order->status,
                'created_at' => $order->order_date,
                'total' => $order->farmLineTotal($farmId),
            ]);
    }

    private function farmOrders(int $farmId)
    {
        return $this->farmEcommerceOrders($farmId)
            ->map(fn ($order) => (object) array_merge((array) $order, ['source' => 'ecommerce']))
            ->merge($this->farmSalesOrders($farmId)->map(fn ($order) => (object) array_merge((array) $order, ['source' => 'sales'])))
            ->sortByDesc('created_at')
            ->take(8)
            ->values();
    }

    private function farmCustomersCount(int $farmId): int
    {
        return $this->farmCustomers($farmId)->count();
    }

    private function farmCustomers(int $farmId)
    {
        $customers = collect();

        if (Schema::hasTable('orders') && Schema::hasTable('users') && Schema::hasTable('order_items') && Schema::hasTable('inventory_products')) {
            $customers = $customers->merge(Order::query()
                ->linkedToFarm($farmId)
                ->with('user:id,name,email')
                ->get()
                ->map(fn (Order $order) => (object) [
                    'name' => $order->user?->name,
                    'email' => $order->user?->email,
                ])
                ->filter(fn ($customer) => $customer->name));
        }

        if (Schema::hasTable('sales_customers') && Schema::hasTable('sales_orders') && Schema::hasTable('sales_order_items') && Schema::hasTable('inventory_products')) {
            $customers = $customers->merge(SalesOrder::query()
                ->linkedToFarm($farmId)
                ->with('customer:id,name')
                ->get()
                ->map(fn (SalesOrder $order) => (object) [
                    'name' => $order->customer?->name,
                    'email' => null,
                ])
                ->filter(fn ($customer) => $customer->name));
        }

        return $customers->unique(fn ($customer) => $customer->name . '|' . $customer->email)->values();
    }

    private function farmFinancials(int $farmId, array $penIds): array
    {
        $orderRevenue = (float) $this->farmEcommerceOrders($farmId)->sum('total') + (float) $this->farmSalesOrders($farmId)->sum('total');
        $penRevenue = $this->sumTable('livestock_pen_financial_entries', 'amount', fn ($query) => $query->whereIn('pen_id', $penIds)->where('type', 'sale'));
        $penExpenses = $this->sumTable('livestock_pen_financial_entries', 'amount', fn ($query) => $query->whereIn('pen_id', $penIds)->where('type', 'slaughter_packaging'));
        $cropExpenses = $this->sumTable('crop_cost_items', 'amount', fn ($query) => $query
            ->join('crops', 'crop_cost_items.crop_id', '=', 'crops.id')
            ->where('crops.farm_id', $farmId));
        $feedingExpenses = $this->sumTable('animal_feeding_logs', 'total_cost', fn ($query) => $query
            ->join('livestock_animals', 'animal_feeding_logs.animal_id', '=', 'livestock_animals.id')
            ->whereIn('livestock_animals.pen_id', $penIds));
        $journalRevenue = $this->farmJournalAmount($farmId, 'revenue', true);
        $journalExpenses = $this->farmJournalAmount($farmId, 'expense', false);
        $unpostedExpenses = $this->sumTable('expenses', 'amount', fn ($query) => $query
            ->where('farm_id', $farmId)
            ->where('status', '!=', 'posted'));

        return [
            'revenue' => round($orderRevenue + $penRevenue + $journalRevenue, 2),
            'expenses' => round($penExpenses + $cropExpenses + $feedingExpenses + $journalExpenses + $unpostedExpenses, 2),
        ];
    }

    private function farmJournalAmount(int $farmId, string $accountType, bool $creditMinusDebit): float
    {
        if (! Schema::hasTable('journal_entries') || ! Schema::hasTable('journal_entry_lines') || ! Schema::hasTable('accounts')) {
            return 0.0;
        }

        if (! Schema::hasColumn('journal_entries', 'farm_id')) {
            return 0.0;
        }

        $row = DB::table('journal_entry_lines as l')
            ->join('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
            ->join('accounts as a', 'a.id', '=', 'l.account_id')
            ->where('e.farm_id', $farmId)
            ->where('a.type', $accountType)
            ->selectRaw('SUM(l.debit) as total_debit, SUM(l.credit) as total_credit')
            ->first();

        $debit = (float) ($row->total_debit ?? 0);
        $credit = (float) ($row->total_credit ?? 0);

        return $creditMinusDebit ? ($credit - $debit) : ($debit - $credit);
    }

    private function farmAttendanceToday(int $farmId, array $penIds): int
    {
        if (! Schema::hasTable('attendances') || ! Schema::hasTable('employees')) {
            return 0;
        }

        $query = DB::table('attendances')->join('employees', 'attendances.employee_id', '=', 'employees.id')->whereDate('attendances.day', now()->toDateString());

        if (Schema::hasColumn('employees', 'farm_id')) {
            $query->where('employees.farm_id', $farmId);
        } elseif (Schema::hasColumn('employees', 'pen_id')) {
            $query->whereIn('employees.pen_id', $penIds);
        } else {
            return 0;
        }

        return (int) $query->count();
    }

    private function farmWorkers(int $farmId, array $penIds)
    {
        if (! Schema::hasTable('employees') || ! $this->hasFarmLink('employees')) {
            return collect();
        }

        return DB::table('employees')
            ->where('farm_id', $farmId)
            ->whereNull('deleted_at')
            ->latest()
            ->take(8)
            ->get();
    }

    private function farmAttendance(int $farmId, array $penIds)
    {
        if (! Schema::hasTable('attendances') || ! Schema::hasTable('employees') || ! $this->hasFarmLink('employees')) {
            return collect();
        }

        return DB::table('attendances')
            ->join('employees', 'attendances.employee_id', '=', 'employees.id')
            ->where('employees.farm_id', $farmId)
            ->select('attendances.*', 'employees.full_name')
            ->orderByDesc('attendances.day')
            ->take(8)
            ->get();
    }

    private function farmAnimals(array $penIds)
    {
        if (! Schema::hasTable('livestock_animals')) {
            return collect();
        }

        return DB::table('livestock_animals')
            ->whereIn('pen_id', $penIds)
            ->orderByDesc('created_at')
            ->take(8)
            ->get();
    }

    private function farmProducts(int $farmId)
    {
        if (! Schema::hasTable('inventory_products')) {
            return collect();
        }

        return InventoryProduct::withoutGlobalScopes()
            ->where('farm_id', $farmId)
            ->latest()
            ->take(8)
            ->get();
    }

    private function farmReportsCount(int $farmId, array $penIds): int
    {
        return $this->farmReports($farmId, $penIds)->count();
    }

    private function farmReports(int $farmId, array $penIds)
    {
        $reports = collect();

        if (Schema::hasTable('animal_health_records') && Schema::hasTable('livestock_animals')) {
            $reports = $reports->merge(DB::table('animal_health_records')
                ->join('livestock_animals', 'animal_health_records.animal_id', '=', 'livestock_animals.id')
                ->whereIn('livestock_animals.pen_id', $penIds)
                ->select('animal_health_records.created_at', 'animal_health_records.record_type as title', 'animal_health_records.diagnosis as desc')
                ->latest('animal_health_records.created_at')
                ->take(5)
                ->get());
        }

        if (Schema::hasTable('crop_growth_stages') && Schema::hasTable('crops')) {
            $reports = $reports->merge(DB::table('crop_growth_stages')
                ->join('crops', 'crop_growth_stages.crop_id', '=', 'crops.id')
                ->where('crops.farm_id', $farmId)
                ->select('crop_growth_stages.created_at', 'crop_growth_stages.stage_name as title', 'crop_growth_stages.notes as desc')
                ->latest('crop_growth_stages.created_at')
                ->take(5)
                ->get());
        }

        return $reports->sortByDesc('created_at')->take(8)->values();
    }

    private function farmActivities(int $farmId, array $penIds)
    {
        return collect()
            ->merge($this->farmAnimals($penIds)->map(fn ($row) => ['title' => __('superadmin.farm_dashboard.animal_registered'), 'desc' => $row->tag_number ?? '-', 'date' => $row->created_at ?? null]))
            ->merge($this->farmOrders($farmId)->map(fn ($row) => ['title' => __('superadmin.farm_dashboard.order_created'), 'desc' => $row->order_no ?? '-', 'date' => $row->created_at ?? null]))
            ->merge($this->farmProducts($farmId)->map(fn ($row) => ['title' => __('superadmin.farm_dashboard.product_added'), 'desc' => $row->localized_title ?? $row->name ?? '-', 'date' => $row->created_at ?? null]))
            ->sortByDesc('date')
            ->take(10)
            ->values();
    }

    private function farmAlerts(int $farmId, string $tenantId, array $penIds): array
    {
        $alerts = [];

        if (Schema::hasTable('animal_vaccinations') && Schema::hasColumn('animal_vaccinations', 'pen_id')) {
            $overdue = DB::table('animal_vaccinations')
                ->where('tenant_id', $tenantId)
                ->whereIn('pen_id', $penIds)
                ->whereNotNull('next_due_date')
                ->whereDate('next_due_date', '<', now()->toDateString())
                ->count();

            if ($overdue > 0) {
                $alerts[] = ['type' => 'danger', 'title' => __('dashboard.alerts.vaccination_overdue'), 'desc' => __('superadmin.farm_dashboard.overdue_vaccinations', ['count' => $overdue])];
            }
        }

        if ($this->farmInventoryStock($farmId) <= 0) {
            $alerts[] = ['type' => 'warning', 'title' => __('dashboard.alerts.low_feed_stock'), 'desc' => __('superadmin.farm_dashboard.no_inventory_stock')];
        }

        return $alerts ?: [['type' => 'success', 'title' => __('dashboard.alerts.no_critical_alerts'), 'desc' => __('dashboard.alerts.no_critical_alerts_desc')]];
    }

    private function farmMilkChart(string $tenantId, array $penIds)
    {
        if (! Schema::hasTable('milk_production_logs') || ! Schema::hasTable('livestock_animals')) {
            return collect();
        }

        return DB::table('milk_production_logs')
            ->join('livestock_animals', 'milk_production_logs.animal_id', '=', 'livestock_animals.id')
            ->where('milk_production_logs.tenant_id', $tenantId)
            ->whereIn('livestock_animals.pen_id', $penIds)
            ->where('milk_production_logs.production_date', '>=', now()->subDays(7)->toDateString())
            ->selectRaw('milk_production_logs.production_date as label, SUM(milk_production_logs.quantity_liters) as value')
            ->groupBy('milk_production_logs.production_date')
            ->orderBy('milk_production_logs.production_date')
            ->get();
    }

    private function farmOrdersChart(int $farmId)
    {
        return $this->farmOrders($farmId)
            ->groupBy(fn ($order) => \Illuminate\Support\Carbon::parse($order->created_at)->format('M'))
            ->map(fn ($orders, $label) => ['label' => $label, 'value' => $orders->count()])
            ->values();
    }

    private function farmAnimalStatusChart(array $penIds)
    {
        if (! Schema::hasTable('livestock_animals')) {
            return collect();
        }

        return DB::table('livestock_animals')
            ->whereIn('pen_id', $penIds)
            ->selectRaw('health_status as label, COUNT(*) as value')
            ->groupBy('health_status')
            ->get();
    }

    private function farmFinanceChart(int $farmId, array $penIds)
    {
        $financials = $this->farmFinancials($farmId, $penIds);

        return collect([
            ['label' => __('superadmin.farm_dashboard.revenue'), 'value' => $financials['revenue']],
            ['label' => __('superadmin.farm_dashboard.expenses'), 'value' => $financials['expenses']],
        ]);
    }

    public function accessManagement(Request $request)
    {
        $locale = session('locale_full', 'en-SA');
        return view('dashboard.access-management', compact('locale'));
    }
}
