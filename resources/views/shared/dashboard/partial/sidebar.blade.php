<!-- Sidebar (Unified: SuperAdmin + Customer/Admin, permission-driven) -->
<aside id="sidebar">
    @php
        $isSuperAdmin = auth()->check() && auth()->user()->hasRole('SuperAdmin');
        $dashboardRoute = $isSuperAdmin ? 'superadmin.dashboard' : 'dashboard';
        $activeLocale = $currentLocale ?? app()->getLocale();
        $features = $planFeatures ?? (auth()->check() ? auth()->user()->planFeatures() : []);
        $hrEnabled = (bool) ($featureFlags['hr_management'] ?? ($features['hr_management']['enabled'] ?? false));
        $chevronClass = 'fa-solid fa-chevron-right chevron m-1 ' . (($currentLang ?? app()->getLocale()) === 'en' ? 'me-auto' : 'ms-auto');
    @endphp

    <style>
        .sidebar-nav {
            max-height: 100vh;
            overflow-y: auto;
            padding-right: 6px;
            scrollbar-width: thin;
            scrollbar-color: #234c14 transparent;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: rgba(35, 76, 20, 0.1);
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: #234c14;
            border: 1px solid #6aa84f;
            box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.3);
            border-radius: 10px;
        }

        .sidebar {
            background: #1b3a10;
        }

        .sidebar-nav .active {
            background: linear-gradient(135deg, rgba(35, 76, 20, 0.12), rgba(200, 166, 75, 0.12));
            border-left: 3px solid #234c14;
            color: #234c14;
        }

        .sidebar-nav a:hover {
            background: linear-gradient(135deg, #eef6ea, #f8fbf7);
            transition: 0.2s ease;
        }
    </style>

    <nav class="sidebar-nav mt-4">

        {{-- Dashboard --}}
        <a href="{{ route($dashboardRoute, ['locale' => $activeLocale]) }}" class="nav-item {{ request()->routeIs('dashboard') || request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
            <img src="{{ asset('assets/images/sidebar-icon-1.svg') }}" alt="" class="nav-icon">
            <span class="nav-label">{{ __('dashboard.sidebar.dashboard') }}</span>
        </a>

        {{-- 1. HR --}}
        @can('hr.view')
            @if ($isSuperAdmin || $hrEnabled)
                <div class="nav-dropdown {{ request()->routeIs('customer.hr.*') ? 'open' : '' }}">
                    <a href="javascript:void(0)" class="nav-item has-dropdown {{ request()->routeIs('customer.hr.*') ? 'active' : '' }}">
                        <img src="{{ asset('assets/images/sidebar-icon-9.svg') }}" alt="" class="nav-icon">
                        <span class="nav-label">{{ __('dashboard.sidebar.requirements.hr') }}</span>
                        <i class="{{ $chevronClass }}"></i>
                    </a>
                    <div class="dropdown-container">
                        <a href="{{ route('customer.hr.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.hr.index') ? 'active' : '' }}">{{ __('dashboard.sidebar.hr_dashboard') }}</a>
                        <a href="{{ route('customer.hr.departments.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.hr.departments.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.departments') }}</a>
                        <a href="{{ route('customer.hr.job-titles.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.hr.job-titles.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.job_titles') }}</a>
                        <a href="{{ route('customer.hr.employees.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.hr.employees.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.employees') }}</a>
                        <a href="{{ route('customer.hr.attendance.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.hr.attendance.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.attendance') }}</a>
                        <a href="{{ route('customer.hr.leaves.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.hr.leaves.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.leave_requests') }}</a>
                    </div>
                </div>
            @endif
        @endcan

        {{-- 2. Farms --}}
        @can('farms.view')
            <div class="nav-dropdown {{ request()->routeIs('customer.farms.*') || request()->routeIs('customer.farm-pens.*') ? 'open' : '' }}">
                <a href="javascript:void(0)" class="nav-item has-dropdown {{ request()->routeIs('customer.farms.*') || request()->routeIs('customer.farm-pens.*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/images/sidebar-icon-5.svg') }}" alt="" class="nav-icon">
                    <span class="nav-label">{{ __('dashboard.sidebar.farms') }}</span>
                    <i class="{{ $chevronClass }}"></i>
                </a>
                <div class="dropdown-container">
                    <a href="{{ route('customer.farms.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.farms.*') ? 'active' : '' }}">{{ __('farms.titles.farms') }}</a>
                    <a href="{{ route('customer.farm-pens.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.farm-pens.*') ? 'active' : '' }}">{{ __('farms.titles.pens') }}</a>
                </div>
            </div>
        @endcan

        {{-- 3. Livestock --}}
        @can('animals.view')
            <div class="nav-dropdown {{ request()->routeIs('customer.livestock.animals.*') || request()->routeIs('customer.livestock.reproduction-cycles.*') || request()->routeIs('customer.livestock.species.*') || request()->routeIs('customer.livestock.breeds.*') ? 'open' : '' }}">
                <a href="javascript:void(0)" class="nav-item has-dropdown {{ request()->routeIs('customer.livestock.animals.*') || request()->routeIs('customer.livestock.reproduction-cycles.*') || request()->routeIs('customer.livestock.species.*') || request()->routeIs('customer.livestock.breeds.*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/images/sidebar-icon-2.svg') }}" alt="" class="nav-icon">
                    <span class="nav-label">{{ __('dashboard.sidebar.requirements.livestock') }}</span>
                    <i class="{{ $chevronClass }}"></i>
                </a>
                <div class="dropdown-container">
                    <a href="{{ route('customer.livestock.animals.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.livestock.animals.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.animal_registry') }}</a>
                    <a href="{{ route('customer.livestock.reproduction-cycles.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.livestock.reproduction-cycles.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.breeding_cycles') }}</a>
                    <a href="{{ route('customer.livestock.species.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.livestock.species.*') ? 'active' : '' }}">{{ __('livestock.titles.species') }}</a>
                    <a href="{{ route('customer.livestock.breeds.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.livestock.breeds.*') ? 'active' : '' }}">{{ __('livestock.titles.breeds') }}</a>
                </div>
            </div>
        @endcan

        {{-- 4. Crops --}}
        @can('crops.view')
            <div class="nav-dropdown {{ request()->routeIs('customer.crops-feed.crops.*') ? 'open' : '' }}">
                <a href="javascript:void(0)" class="nav-item has-dropdown {{ request()->routeIs('customer.crops-feed.crops.*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/images/sidebar-icon-4.svg') }}" alt="" class="nav-icon">
                    <span class="nav-label">{{ __('dashboard.sidebar.requirements.crops') }}</span>
                    <i class="{{ $chevronClass }}"></i>
                </a>
                <div class="dropdown-container">
                    <a href="{{ route('customer.crops-feed.crops.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.crops-feed.crops.*') ? 'active' : '' }}">{{ __('crops_feed.titles.crops') }}</a>
                </div>
            </div>
        @endcan

        {{-- 5. Poultry --}}
        @can('poultry.view')
            <div class="nav-dropdown {{ request()->routeIs('customer.poultry.*') ? 'open' : '' }}">
                <a href="javascript:void(0)" class="nav-item has-dropdown {{ request()->routeIs('customer.poultry.*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/images/sidebar-icon-4.svg') }}" alt="" class="nav-icon">
                    <span class="nav-label">{{ __('dashboard.sidebar.requirements.poultry') }}</span>
                    <i class="{{ $chevronClass }}"></i>
                </a>
                <div class="dropdown-container">
                    <a href="{{ route('customer.poultry.broiler-cycles.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.poultry.broiler-cycles.*') ? 'active' : '' }}">{{ __('poultry.titles.broiler_cycles') }}</a>
                    <a href="{{ route('customer.poultry.layer-flocks.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.poultry.layer-flocks.*') ? 'active' : '' }}">{{ __('poultry.titles.layer_flocks') }}</a>
                    <a href="{{ route('customer.poultry.hatchery-machines.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.poultry.hatchery-machines.*') ? 'active' : '' }}">{{ __('poultry.titles.hatchery_machines') }}</a>
                    <a href="{{ route('customer.poultry.hatchery-batches.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.poultry.hatchery-batches.*') ? 'active' : '' }}">{{ __('poultry.titles.hatchery_batches') }}</a>
                    <a href="{{ route('customer.poultry.chicken-breeds.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.poultry.chicken-breeds.*') ? 'active' : '' }}">{{ __('poultry.titles.chicken_breeds') }}</a>
                    <a href="{{ route('customer.poultry.alerts.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.poultry.alerts.*') ? 'active' : '' }}">{{ __('poultry.titles.alerts') }}</a>
                </div>
            </div>
        @endcan

        {{-- 6. Health and vaccinations (grouped under livestock's 'animals.view' permission in routes) --}}
        @can('animals.view')
            <div class="nav-dropdown {{ request()->routeIs('customer.livestock.vaccines.*') || request()->routeIs('customer.livestock.alerts.*') ? 'open' : '' }}">
                <a href="javascript:void(0)" class="nav-item has-dropdown {{ request()->routeIs('customer.livestock.vaccines.*') || request()->routeIs('customer.livestock.alerts.*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/images/sidebar-icon-2.svg') }}" alt="" class="nav-icon">
                    <span class="nav-label">{{ __('dashboard.sidebar.requirements.health_vaccinations') }}</span>
                    <i class="{{ $chevronClass }}"></i>
                </a>
                <div class="dropdown-container">
                    <a href="{{ route('customer.livestock.vaccines.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.livestock.vaccines.*') ? 'active' : '' }}">{{ __('livestock.titles.vaccines') }}</a>
                    <a href="{{ route('customer.livestock.alerts.vaccinations-due', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.livestock.alerts.vaccinations-due') ? 'active' : '' }}">{{ __('livestock.titles.vaccinations_due') }}</a>
                    <a href="{{ route('customer.livestock.alerts.vaccinations-overdue', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.livestock.alerts.vaccinations-overdue') ? 'active' : '' }}">{{ __('livestock.titles.vaccinations_overdue') }}</a>
                    <a href="{{ route('customer.livestock.alerts.under-treatment', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.livestock.alerts.under-treatment') ? 'active' : '' }}">{{ __('livestock.titles.under_treatment') }}</a>
                </div>
            </div>
        @endcan

        {{-- 7. Inventory --}}
        @can('inventory.view')
            <div class="nav-dropdown {{ request()->routeIs('customer.inventory.*') || request()->routeIs('customer.crops-feed.feed.*') || request()->routeIs('customer.livestock.feed-types.*') ? 'open' : '' }}">
                <a href="javascript:void(0)" class="nav-item has-dropdown {{ request()->routeIs('customer.inventory.*') || request()->routeIs('customer.crops-feed.feed.*') || request()->routeIs('customer.livestock.feed-types.*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/images/sidebar-icon-5.svg') }}" alt="" class="nav-icon">
                    <span class="nav-label">{{ __('dashboard.sidebar.requirements.inventory') }}</span>
                    <i class="{{ $chevronClass }}"></i>
                </a>
                <div class="dropdown-container">
                    <a href="{{ route('customer.inventory.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.inventory.index') ? 'active' : '' }}">{{ __('warehouse.titles.warehouse') }}</a>
                    <a href="{{ route('customer.inventory.products.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.inventory.products.*') ? 'active' : '' }}">{{ __('warehouse.titles.products') }}</a>
                    <a href="{{ route('customer.inventory.categories.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.inventory.categories.*') ? 'active' : '' }}">{{ __('warehouse.titles.categories') }}</a>
                    <a href="{{ route('customer.crops-feed.feed.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.crops-feed.feed.*') ? 'active' : '' }}">{{ __('crops_feed.titles.feed_management') }}</a>
                    <a href="{{ route('customer.livestock.feed-types.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.livestock.feed-types.*') ? 'active' : '' }}">{{ __('livestock.titles.feed_types') }}</a>
                    <a href="{{ route('customer.inventory.alerts.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.inventory.alerts.*') ? 'active' : '' }}">{{ __('warehouse.titles.alerts') }}</a>
                    <a href="{{ route('customer.inventory.traceability.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.inventory.traceability.*') ? 'active' : '' }}">{{ __('warehouse.titles.traceability') }}</a>
                </div>
            </div>
        @endcan

        {{-- 8. Sales --}}
        @can('sales.view')
            <div class="nav-dropdown {{ request()->routeIs('customer.sales-distribution.*') ? 'open' : '' }}">
                <a href="javascript:void(0)" class="nav-item has-dropdown {{ request()->routeIs('customer.sales-distribution.*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/images/sidebar-icon-6.svg') }}" alt="" class="nav-icon">
                    <span class="nav-label">{{ __('dashboard.sidebar.requirements.sales') }}</span>
                    <i class="{{ $chevronClass }}"></i>
                </a>
                <div class="dropdown-container">
                    <a href="{{ route('customer.sales-distribution.dashboard', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.sales-distribution.dashboard') ? 'active' : '' }}">{{ __('sales_dist.sidebar.dashboard') }}</a>
                    <a href="{{ route('customer.sales-distribution.customers.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.sales-distribution.customers.*') ? 'active' : '' }}">{{ __('sales_dist.sidebar.customers') }}</a>
                    <a href="{{ route('customer.sales-distribution.orders.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.sales-distribution.orders.*') ? 'active' : '' }}">{{ __('sales_dist.sidebar.orders') }}</a>
                    <a href="{{ route('customer.sales-distribution.invoices.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.sales-distribution.invoices.*') ? 'active' : '' }}">{{ __('sales_dist.sidebar.invoices') }}</a>
                    <a href="{{ route('customer.sales-distribution.shipments.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.sales-distribution.shipments.*') ? 'active' : '' }}">{{ __('sales_dist.sidebar.shipments') }}</a>
                    <a href="{{ route('customer.sales-distribution.contracts.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.sales-distribution.contracts.*') ? 'active' : '' }}">{{ __('sales_dist.sidebar.contracts') }}</a>
                </div>
            </div>
        @endcan

        {{-- 9. Financial reports --}}
        @can('finance.view')
            <div class="nav-dropdown {{ request()->routeIs('customer.finance.*') ? 'open' : '' }}">
                <a href="javascript:void(0)" class="nav-item has-dropdown {{ request()->routeIs('customer.finance.*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/images/sidebar-icon-8.svg') }}" alt="" class="nav-icon">
                    <span class="nav-label">{{ __('dashboard.sidebar.requirements.financial_reports') }}</span>
                    <i class="{{ $chevronClass }}"></i>
                </a>
                <div class="dropdown-container">
                    <a href="{{ route('customer.finance.profit-loss.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.finance.profit-loss.*') ? 'active' : '' }}">{{ __('finance.sidebar.profit_loss') }}</a>
                    <a href="{{ route('customer.finance.dashboard', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.finance.dashboard') ? 'active' : '' }}">{{ __('finance.sidebar.dashboard') }}</a>
                    <a href="{{ route('customer.finance.accounts.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.finance.accounts.*') ? 'active' : '' }}">{{ __('finance.sidebar.accounts') }}</a>
                    <a href="{{ route('customer.finance.journal-entries.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.finance.journal-entries.*') ? 'active' : '' }}">{{ __('finance.sidebar.journal_entries') }}</a>
                    <a href="{{ route('customer.finance.ledger.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.finance.ledger.*') ? 'active' : '' }}">{{ __('finance.sidebar.ledger') }}</a>
                    <a href="{{ route('customer.finance.expenses.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.finance.expenses.*') ? 'active' : '' }}">{{ __('finance.sidebar.expenses') }}</a>
                </div>
            </div>
        @endcan

        {{-- 10. Warehouse --}}
        @can('warehouse.view')
            <a href="{{ route('customer.warehouse-assets.index', ['locale' => $activeLocale]) }}" class="nav-item {{ request()->routeIs('customer.warehouse-assets.*') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-5.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.requirements.warehouse') }}</span>
            </a>
        @endcan

        {{-- 11. Analytics --}}
        @can('analytics.view')
            <a href="{{ route('customer.analytics.index', ['locale' => $activeLocale]) }}" class="nav-item {{ request()->routeIs('customer.analytics.*') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-8.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.requirements.analytics') }}</span>
            </a>
        @endcan

        {{-- 12. Ecommerce --}}
        @can('ecommerce.view')
            <div class="nav-dropdown {{ request()->routeIs('customer.ecommerce.*') ? 'open' : '' }}">
                <a href="javascript:void(0)" class="nav-item has-dropdown {{ request()->routeIs('customer.ecommerce.*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/images/sidebar-icon-6.svg') }}" alt="" class="nav-icon">
                    <span class="nav-label">{{ __('dashboard.sidebar.ecommerce') }}</span>
                    <i class="{{ $chevronClass }}"></i>
                </a>
                <div class="dropdown-container">
                    <a href="{{ route('customer.ecommerce.orders.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.ecommerce.orders.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.ecommerce_orders') }}</a>
                </div>
            </div>
        @endcan

        {{-- 13. Procurement --}}
        @can('procurement.view')
            <div class="nav-dropdown {{ request()->routeIs('customer.procurement.*') ? 'open' : '' }}">
                <a href="javascript:void(0)" class="nav-item has-dropdown {{ request()->routeIs('customer.procurement.*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/images/sidebar-icon-7.svg') }}" alt="" class="nav-icon">
                    <span class="nav-label">{{ __('procurement.sidebar.title') }}</span>
                    <i class="{{ $chevronClass }}"></i>
                </a>
                <div class="dropdown-container">
                    <a href="{{ route('customer.procurement.suppliers.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.procurement.suppliers.*') ? 'active' : '' }}">{{ __('procurement.sidebar.suppliers') }}</a>
                    <a href="{{ route('customer.procurement.requisitions.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.procurement.requisitions.*') ? 'active' : '' }}">{{ __('procurement.sidebar.requisitions') }}</a>
                    <a href="{{ route('customer.procurement.rfqs.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.procurement.rfqs.*') ? 'active' : '' }}">{{ __('procurement.sidebar.rfqs') }}</a>
                    <a href="{{ route('customer.procurement.quotations.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.procurement.quotations.*') ? 'active' : '' }}">{{ __('procurement.sidebar.quotations') }}</a>
                    <a href="{{ route('customer.procurement.purchase-orders.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.procurement.purchase-orders.*') ? 'active' : '' }}">{{ __('procurement.sidebar.purchase_orders') }}</a>
                    <a href="{{ route('customer.procurement.goods-receipts.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.procurement.goods-receipts.*') ? 'active' : '' }}">{{ __('procurement.sidebar.goods_receipts') }}</a>
                    <a href="{{ route('customer.procurement.invoices.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.procurement.invoices.*') ? 'active' : '' }}">{{ __('procurement.sidebar.invoices') }}</a>
                </div>
            </div>
        @endcan

        {{-- 14. Production / Subscription (customer.subscription.* has NO permission middleware in routes — always visible to any logged-in Customer/SuperAdmin) --}}
        @auth
            <div class="nav-dropdown {{ request()->routeIs('customer.subscription.*') || request()->routeIs('superadmin.plans.*') || request()->routeIs('superadmin.subscriptions.*') ? 'open' : '' }}">
                <a href="javascript:void(0)" class="nav-item has-dropdown {{ request()->routeIs('customer.subscription.*') || request()->routeIs('superadmin.plans.*') || request()->routeIs('superadmin.subscriptions.*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/images/sidebar-icon-3.svg') }}" alt="" class="nav-icon">
                    <span class="nav-label">{{ __('dashboard.sidebar.requirements.production') }}</span>
                    <i class="{{ $chevronClass }}"></i>
                </a>
                <div class="dropdown-container">
                    <a href="{{ route('customer.subscription.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.subscription.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.my_subscription') }}</a>
                    @if ($isSuperAdmin)
                        @can('plans.manage')
                            <a href="{{ route('superadmin.plans.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('superadmin.plans.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.plans') }}</a>
                        @endcan
                        @can('subscriptions.manage')
                            <a href="{{ route('superadmin.subscriptions.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('superadmin.subscriptions.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.subscriptions') }}</a>
                        @endcan
                    @endif
                </div>
            </div>
        @endauth

        {{-- 15. System management (superadmin.* routes require role:SuperAdmin only — hard-gate here too) --}}
        @if ($isSuperAdmin)
        @can('roles.manage')
            <div class="nav-dropdown {{ request()->routeIs('superadmin.setting.*') || request()->routeIs('superadmin.access-management') || request()->routeIs('superadmin.content.*') || request()->routeIs('superadmin.contact-info.*') ? 'open' : '' }}">
                <a href="javascript:void(0)" class="nav-item has-dropdown {{ request()->routeIs('superadmin.setting.*') || request()->routeIs('superadmin.access-management') || request()->routeIs('superadmin.content.*') || request()->routeIs('superadmin.contact-info.*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/images/sidebar-icon-11.svg') }}" alt="" class="nav-icon">
                    <span class="nav-label">{{ __('dashboard.sidebar.requirements.system_management') }}</span>
                    <i class="{{ $chevronClass }}"></i>
                </a>
                <div class="dropdown-container">
                    <a href="{{ route('superadmin.content.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('superadmin.content.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.content') }}</a>
                    <a href="{{ route('superadmin.setting.countries.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('superadmin.setting.countries.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.countries') }}</a>
                    <a href="{{ route('superadmin.setting.cities.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('superadmin.setting.cities.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.cities') }}</a>
                    <a href="{{ route('superadmin.setting.theme.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('superadmin.setting.theme.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.theme') }}</a>
                    @can('settings.manage')
                        <a href="{{ route('superadmin.contact-info.edit', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('superadmin.contact-info.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.contact_info') }}</a>
                    @endcan
                    <a href="{{ route('superadmin.access-management', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('superadmin.access-management') ? 'active' : '' }}">{{ __('dashboard.sidebar.settings.permissionsManagement') }}</a>
                    @can('farms.manage')
                        <a href="{{ route('superadmin.farm-assignments.employees', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('superadmin.farm-assignments.employees') ? 'active' : '' }}">{{ __('superadmin.farm_assignments.employees_title') }}</a>
                        <a href="{{ route('superadmin.farm-assignments.products', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('superadmin.farm-assignments.products') ? 'active' : '' }}">{{ __('superadmin.farm_assignments.products_title') }}</a>
                    @endcan
                </div>
            </div>
        @endcan
        @endif

        {{-- 16. User management (superadmin.users.* requires role:SuperAdmin only — hard-gate here too) --}}
        @if ($isSuperAdmin)
        @can('users.manage')
            <a href="{{ route('superadmin.users.index', ['locale' => $activeLocale]) }}" class="nav-item {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-9.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.requirements.user_management') }}</span>
            </a>
        @endcan
        @endif

        {{-- 17. Logout --}}
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-item d-flex align-items-center">
            <img src="{{ asset('assets/images/sidebar-icon-12.svg') }}" alt="" class="nav-icon me-2">
            <span class="nav-label">{{ __('dashboard.sidebar.requirements.logout') }}</span>
        </a>

        <form id="logout-form" action="{{ route('logout', ['locale' => $activeLocale]) }}" method="POST" class="d-none">
            @csrf
        </form>
    </nav>
</aside>
