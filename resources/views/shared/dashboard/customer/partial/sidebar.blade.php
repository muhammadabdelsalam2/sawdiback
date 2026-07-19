<!-- Sidebar -->

<aside id="sidebar">
    @php
        $isSuperAdmin = auth()->check() && auth()->user()->hasRole('SuperAdmin');
        $dashboardRoute = $isSuperAdmin ? 'superadmin.dashboard' : 'dashboard';
        $activeLocale = $currentLocale ?? app()->getLocale();

        $features = auth()->check() ? auth()->user()->planFeatures() : [];

        // Feature flag (new structure: ['hr_management' => ['enabled' => bool, ...]])
        $hrEnabled = (bool) ($features['hr_management']['enabled'] ?? false);
    @endphp
    <style>
        .sidebar-nav {
            max-height: 100vh;
            overflow-y: auto;
            padding-right: 6px;
        }

        /* Scrollbar */
        .sidebar-nav::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: rgba(35, 76, 20, 0.1);
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg,
                    #234c14,
                    #3e7a2f,
                    #6aa84f);
            border-radius: 10px;
        }

        .sidebar-nav {
            max-height: 100vh;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #234c14 transparent;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: #234c14;
            border-radius: 8px;
        }

        .sidebar {
            background: #1b3a10;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: #234c14;
            border: 1px solid #6aa84f;
            box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.3);
            border-radius: 10px;
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
    {{-- You Can Start Get Features About Current Subscription Plan auth()->user()->planFeatures() --}}
    <nav class="sidebar-nav mt-4">

        {{-- Dashboard --}}
        <a href="{{ route($dashboardRoute, ['locale' => $activeLocale]) }}"
            class="nav-item {{ request()->routeIs('dashboard') || request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
            <img src="{{ asset('assets/images/sidebar-icon-1.svg') }}" alt="" class="nav-icon">
            <span class="nav-label">{{ __('dashboard.sidebar.dashboard') }}</span>
        </a>

        {{-- Livestock --}}
        <div
            class="nav-dropdown {{ request()->routeIs('customer.livestock.*') || request()->routeIs('livestock.*') || request()->routeIs('superadmin.access-management') ? 'open' : '' }}">
            <a href="javascript:void(0)"
                class="nav-item has-dropdown {{ request()->routeIs('customer.livestock.*') || request()->routeIs('livestock.*') || request()->routeIs('superadmin.access-management') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-2.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.livestock') }}</span>
                <i
                    class="fa-solid fa-chevron-right  chevron m-1 {{ $currentLang == 'en' ? 'me-auto' : 'ms-auto' }}  "></i>
            </a>
            <div class="dropdown-container">
                <a href="{{ route('customer.livestock.animals.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.livestock.animals.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.animal_registry') }}</a>
                <a href="{{ route('customer.livestock.alerts.under-treatment', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.livestock.alerts.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.health_vax') }}</a>
                <a href="{{ route('customer.livestock.reproduction-cycles.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.livestock.reproduction-cycles.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.breeding_cycles') }}</a>
                <a href="{{ route('customer.livestock.species.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.livestock.species.*') ? 'active' : '' }}">{{ __('livestock.titles.species') }}</a>
                <a href="{{ route('customer.livestock.breeds.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.livestock.breeds.*') ? 'active' : '' }}">{{ __('livestock.titles.breeds') }}</a>
                <a href="{{ route('customer.livestock.feed-types.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.livestock.feed-types.*') ? 'active' : '' }}">{{ __('livestock.titles.feed_types') }}</a>
                <a href="{{ route('customer.livestock.vaccines.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.livestock.vaccines.*') ? 'active' : '' }}">{{ __('livestock.titles.vaccines') }}</a>
            </div>
        </div>

        @can('farms.view')
            <div class="nav-dropdown {{ request()->routeIs('customer.farms.*') || request()->routeIs('customer.farm-pens.*') ? 'open' : '' }}">
                <a href="javascript:void(0)"
                    class="nav-item has-dropdown {{ request()->routeIs('customer.farms.*') || request()->routeIs('customer.farm-pens.*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/images/sidebar-icon-5.svg') }}" alt="" class="nav-icon">
                    <span class="nav-label">{{ __('dashboard.sidebar.farms') }}</span>
                    <i class="fa-solid fa-chevron-right chevron m-1 {{ $currentLang == 'en' ? 'me-auto' : 'ms-auto' }}"></i>
                </a>
                <div class="dropdown-container">
                    <a href="{{ route('customer.farms.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('customer.farms.*') ? 'active' : '' }}">{{ __('farms.titles.farms') }}</a>
                    <a href="{{ route('customer.farm-pens.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('customer.farm-pens.*') ? 'active' : '' }}">{{ __('farms.titles.pens') }}</a>
                </div>
            </div>
        @endcan

        {{-- Production Dropdown (Contains Subscriptions/Plans) --}}
        <div
            class="nav-dropdown {{ request()->routeIs('customer.subscription.*') || request()->routeIs('superadmin.plans.*') || request()->routeIs('superadmin.subscriptions.*') ? 'open' : '' }}">
            <a href="javascript:void(0)" class="nav-item has-dropdown">
                <img src="{{ asset('assets/images/sidebar-icon-3.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.production') }}</span>
                <i
                    class="fa-solid fa-chevron-right  chevron m-1 {{ $currentLang == 'en' ? 'me-auto' : 'ms-auto' }}  "></i>
            </a>
            <div class="dropdown-container">
                <a href="{{ route('customer.subscription.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.subscription.*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card me-2"></i> {{ __('dashboard.sidebar.my_subscription') }}
                </a>

                @if ($isSuperAdmin)
                    <a href="{{ route('superadmin.plans.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('superadmin.plans.*') ? 'active' : '' }}">
                        <i class="bi bi-gem me-2"></i> {{ __('dashboard.sidebar.plans') }}
                    </a>
                    <a href="{{ route('superadmin.subscriptions.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('superadmin.subscriptions.*') ? 'active' : '' }}">
                        <i class="bi bi-arrow-repeat me-2"></i> {{ __('dashboard.sidebar.subscriptions') }}
                    </a>
                @endif
            </div>
        </div>

        {{-- Crops & Feed --}}
        <div
            class="nav-dropdown {{ request()->routeIs('customer.crops-feed.*') || request()->routeIs('crops-feed.*') || request()->routeIs('superadmin.access-management') ? 'open' : '' }}">
            <a href="javascript:void(0)"
                class="nav-item has-dropdown {{ request()->routeIs('customer.crops-feed.*') || request()->routeIs('crops-feed.*') || request()->routeIs('superadmin.access-management') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-4.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.crops_feed') }}</span>
                <i
                    class="fa-solid fa-chevron-right  chevron m-1 {{ $currentLang == 'en' ? 'me-auto' : 'ms-auto' }}"></i>
            </a>
            <div class="dropdown-container">
                <a href="{{ route('customer.crops-feed.crops.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.crops-feed.crops.*') ? 'active' : '' }}">{{ __('crops_feed.titles.crops') }}</a>
                <a href="{{ route('customer.crops-feed.feed.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.crops-feed.feed.*') ? 'active' : '' }}">{{ __('crops_feed.titles.feed_management') }}</a>
                <a href="{{ route('customer.crops-feed.reports.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.crops-feed.reports.*') ? 'active' : '' }}">{{ __('crops_feed.titles.reports') }}</a>
                <a href="{{ route('customer.livestock.feed-types.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.livestock.feed-types.*') ? 'active' : '' }}">{{ __('livestock.titles.feed_types') }}</a>
            </div>
        </div>
        {{-- Poultry --}}
        @can('poultry.view')
            <div class="nav-dropdown {{ request()->routeIs('customer.poultry.*') ? 'open' : '' }}">
                <a href="javascript:void(0)"
                    class="nav-item has-dropdown {{ request()->routeIs('customer.poultry.*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/images/sidebar-icon-4.svg') }}" alt="" class="nav-icon">
                    <span class="nav-label">{{ __('dashboard.sidebar.poultry') }}</span>
                    <i
                        class="fa-solid fa-chevron-right  chevron m-1 {{ $currentLang == 'en' ? 'me-auto' : 'ms-auto' }}"></i>
                </a>
                <div class="dropdown-container">
                    <a href="{{ route('customer.poultry.broiler-cycles.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('customer.poultry.broiler-cycles.*') ? 'active' : '' }}">{{ __('poultry.titles.broiler_cycles') }}</a>
                    <a href="{{ route('customer.poultry.layer-flocks.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('customer.poultry.layer-flocks.*') ? 'active' : '' }}">{{ __('poultry.titles.layer_flocks') }}</a>
                    <a href="{{ route('customer.poultry.hatchery-machines.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('customer.poultry.hatchery-machines.*') ? 'active' : '' }}">{{ __('poultry.titles.hatchery_machines') }}</a>
                    <a href="{{ route('customer.poultry.hatchery-batches.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('customer.poultry.hatchery-batches.*') ? 'active' : '' }}">{{ __('poultry.titles.hatchery_batches') }}</a>
                    <a href="{{ route('customer.poultry.chicken-breeds.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('customer.poultry.chicken-breeds.*') ? 'active' : '' }}">{{ __('poultry.titles.chicken_breeds') }}</a>
                    <a href="{{ route('customer.poultry.alerts.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('customer.poultry.alerts.*') ? 'active' : '' }}">{{ __('poultry.titles.alerts') }}</a>
                </div>
            </div>
        @endcan
        {{-- Inventory --}}
        <div class="nav-dropdown {{ request()->routeIs('customer.inventory.*') ? 'open' : '' }}">
            <a href="javascript:void(0)"
                class="nav-item has-dropdown {{ request()->routeIs('customer.inventory.*') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-5.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.inventory') }}</span>
                <i
                    class="fa-solid fa-chevron-right  chevron m-1 {{ $currentLang == 'en' ? 'me-auto' : 'ms-auto' }}"></i>
            </a>
            <div class="dropdown-container">
                <a href="{{ route('customer.inventory.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.inventory.index') ? 'active' : '' }}">{{ __('warehouse.titles.warehouse') }}</a>
                <a href="{{ route('customer.inventory.products.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.inventory.products.*') ? 'active' : '' }}">{{ __('warehouse.titles.products') }}</a>
                <a href="{{ route('customer.inventory.categories.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.inventory.categories.*') ? 'active' : '' }}">{{ __('warehouse.titles.categories') }}</a>
                <a href="{{ route('customer.inventory.alerts.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.inventory.alerts.*') ? 'active' : '' }}">{{ __('warehouse.titles.alerts') }}</a>
                <a href="{{ route('customer.inventory.traceability.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.inventory.traceability.*') ? 'active' : '' }}">{{ __('warehouse.titles.traceability') }}</a>
                @can('warehouse.view')
                    <a href="{{ route('customer.warehouse-assets.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('customer.warehouse-assets.*') ? 'active' : '' }}">{{ __('warehouse.title') }}</a>
                @endcan
                @can('analytics.view')
                    <a href="{{ route('customer.analytics.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('customer.analytics.*') ? 'active' : '' }}">{{ __('analytics.title') }}</a>
                @endcan
            </div>
        </div>

        {{-- E-Commerce --}}
        <div class="nav-dropdown {{ request()->routeIs('customer.ecommerce.*') ? 'open' : '' }}">
            <a href="javascript:void(0)"
                class="nav-item has-dropdown {{ request()->routeIs('customer.ecommerce.*') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-6.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.ecommerce') }}</span>
                <i
                    class="fa-solid fa-chevron-right  chevron m-1 {{ $currentLang == 'en' ? 'me-auto' : 'ms-auto' }}"></i>
            </a>
            <div class="dropdown-container">
                <a href="{{ route('customer.ecommerce.orders.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.ecommerce.orders.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.ecommerce_orders') }}</a>
            </div>
        </div>

        {{-- Sales & Distribution --}}
        <div class="nav-dropdown {{ request()->routeIs('customer.sales-distribution.*') ? 'open' : '' }}">
            <a href="javascript:void(0)"
                class="nav-item has-dropdown {{ request()->routeIs('customer.sales-distribution.*') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-6.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('sales_dist.sidebar.title') }}</span>
                <i
                    class="fa-solid fa-chevron-right  chevron m-1 {{ $currentLang == 'en' ? 'me-auto' : 'ms-auto' }}"></i>
            </a>
            <div class="dropdown-container">
                <a href="{{ route('customer.sales-distribution.dashboard', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.sales-distribution.dashboard') ? 'active' : '' }}">{{ __('sales_dist.sidebar.dashboard') }}</a>
                <a href="{{ route('customer.sales-distribution.customers.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.sales-distribution.customers.*') ? 'active' : '' }}">{{ __('sales_dist.sidebar.customers') }}</a>
                <a href="{{ route('customer.sales-distribution.contracts.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.sales-distribution.contracts.*') ? 'active' : '' }}">{{ __('sales_dist.sidebar.contracts') }}</a>
                <a href="{{ route('customer.sales-distribution.orders.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.sales-distribution.orders.*') ? 'active' : '' }}">{{ __('sales_dist.sidebar.orders') }}</a>
                <a href="{{ route('customer.sales-distribution.shipments.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.sales-distribution.shipments.*') ? 'active' : '' }}">{{ __('sales_dist.sidebar.shipments') }}</a>
                <a href="{{ route('customer.sales-distribution.invoices.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.sales-distribution.invoices.*') ? 'active' : '' }}">{{ __('sales_dist.sidebar.invoices') }}</a>
            </div>
        </div>

        {{-- Procurement --}}
        <div class="nav-dropdown {{ request()->routeIs('customer.procurement.*') ? 'open' : '' }}">
            <a href="javascript:void(0)"
                class="nav-item has-dropdown {{ request()->routeIs('customer.procurement.*') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-7.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('procurement.sidebar.title') }}</span>
                <i
                    class="fa-solid fa-chevron-right  chevron m-1 {{ $currentLang == 'en' ? 'me-auto' : 'ms-auto' }}"></i>
            </a>
            <div class="dropdown-container">
                <a href="{{ route('customer.procurement.suppliers.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.procurement.suppliers.*') ? 'active' : '' }}">{{ __('procurement.sidebar.suppliers') }}</a>
                <a href="{{ route('customer.procurement.requisitions.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.procurement.requisitions.*') ? 'active' : '' }}">{{ __('procurement.sidebar.requisitions') }}</a>
                <a href="{{ route('customer.procurement.rfqs.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.procurement.rfqs.*') ? 'active' : '' }}">{{ __('procurement.sidebar.rfqs') }}</a>
                <a href="{{ route('customer.procurement.quotations.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.procurement.quotations.*') ? 'active' : '' }}">{{ __('procurement.sidebar.quotations') }}</a>
                <a href="{{ route('customer.procurement.purchase-orders.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.procurement.purchase-orders.*') ? 'active' : '' }}">{{ __('procurement.sidebar.purchase_orders') }}</a>
                <a href="{{ route('customer.procurement.goods-receipts.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.procurement.goods-receipts.*') ? 'active' : '' }}">{{ __('procurement.sidebar.goods_receipts') }}</a>
                <a href="{{ route('customer.procurement.invoices.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.procurement.invoices.*') ? 'active' : '' }}">{{ __('procurement.sidebar.invoices') }}</a>
            </div>
        </div>

        {{-- Finance --}}
        <div class="nav-dropdown {{ request()->routeIs('customer.finance.*') ? 'open' : '' }}">
            <a href="javascript:void(0)"
                class="nav-item has-dropdown {{ request()->routeIs('customer.finance.*') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-8.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.finance') }}</span>
                <i
                    class="fa-solid fa-chevron-right  chevron m-1 {{ $currentLang == 'en' ? 'me-auto' : 'ms-auto' }}"></i>
            </a>
            <div class="dropdown-container">
                <a href="{{ route('customer.finance.dashboard', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.finance.dashboard') ? 'active' : '' }}">{{ __('finance.sidebar.dashboard') }}</a>
                <a href="{{ route('customer.finance.accounts.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.finance.accounts.*') ? 'active' : '' }}">{{ __('finance.sidebar.accounts') }}</a>
                <a href="{{ route('customer.finance.journal-entries.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.finance.journal-entries.*') ? 'active' : '' }}">{{ __('finance.sidebar.journal_entries') }}</a>
                <a href="{{ route('customer.finance.ledger.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.finance.ledger.*') ? 'active' : '' }}">{{ __('finance.sidebar.ledger') }}</a>
                <a href="{{ route('customer.finance.expenses.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.finance.expenses.*') ? 'active' : '' }}">{{ __('finance.sidebar.expenses') }}</a>
                <a href="{{ route('customer.finance.profit-loss.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('customer.finance.profit-loss.*') ? 'active' : '' }}">{{ __('finance.sidebar.profit_loss') }}</a>
            </div>
        </div>

        {{-- HR Management (Only if enabled in plan features) --}}
        @if ($hrEnabled)
            <div
                class="nav-dropdown {{ request()->routeIs('customer.hr.*') || request()->routeIs('hr.*') || request()->routeIs('superadmin.access-management') ? 'open' : '' }}">
                <a href="javascript:void(0)"
                    class="nav-item has-dropdown {{ request()->routeIs('customer.hr.*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/images/sidebar-icon-9.svg') }}" alt="" class="nav-icon">
                    <span class="nav-label">{{ __('dashboard.sidebar.hr_management') }}</span>
                    <i
                        class="fa-solid fa-chevron-right  chevron m-1 {{ $currentLang == 'en' ? 'me-auto' : 'ms-auto' }}"></i>
                </a>

                <div class="dropdown-container">
                    <a href="{{ route('customer.hr.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('customer.hr.index') ? 'active' : '' }}">
                        {{ __('dashboard.sidebar.hr_dashboard') }}
                    </a>

                    <a href="{{ route('customer.hr.departments.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('customer.hr.departments.*') ? 'active' : '' }}">
                        {{ __('dashboard.sidebar.departments') }}
                    </a>

                    <a href="{{ route('customer.hr.job-titles.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('customer.hr.job-titles.*') ? 'active' : '' }}">
                        {{ __('dashboard.sidebar.job_titles') }}
                    </a>

                    <a href="{{ route('customer.hr.employees.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('customer.hr.employees.*') ? 'active' : '' }}">
                        {{ __('dashboard.sidebar.employees') }}
                    </a>

                    <a href="{{ route('customer.hr.attendance.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('customer.hr.attendance.*') ? 'active' : '' }}">
                        {{ __('dashboard.sidebar.attendance') }}
                    </a>

                    <a href="{{ route('customer.hr.leaves.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('customer.hr.leaves.*') ? 'active' : '' }}">
                        {{ __('dashboard.sidebar.leave_requests') }}
                    </a>
                </div>
            </div>
        @endif

    </nav>

    {{-- Bottom Section --}}
    <div class="sidebar-bottom">

        @can('roles.manage')

        @if ($isSuperAdmin)
            {{-- System Settings (Dropdown) --}}
            <div class="nav-dropdown {{ request()->routeIs('settings.*') ? 'open' : '' }}">
                <a href="javascript:void(0)"
                    class="nav-item has-dropdown {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/images/sidebar-icon-11.svg') }}" alt="" class="nav-icon">
                    <span class="nav-label">{{ __('dashboard.sidebar.system_settings') }}</span>
                    <i
                        class="fa-solid fa-chevron-right  chevron m-1 {{ $currentLang == 'en' ? 'me-auto' : 'ms-auto' }}"></i>
                </a>

                <div class="dropdown-container">
                    <a href="{{ route('superadmin.setting.countries.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('settings.countries.*') ? 'active' : '' }}">
                        {{ __('dashboard.sidebar.countries') }}
                    </a>

                    <a href="{{ route('superadmin.setting.cities.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('settings.cities.*') ? 'active' : '' }}">
                        {{ __('dashboard.sidebar.cities') }}
                    </a>

                    <a href="{{ route('superadmin.setting.theme.edit', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('settings.theme.*') ? 'active' : '' }}">
                        {{ __('dashboard.sidebar.theme') }}
                    </a>

                    @can('settings.manage')
                        <a href="{{ route('superadmin.contact-info.edit', ['locale' => $activeLocale]) }}"
                            class="dropdown-item {{ request()->routeIs('superadmin.contact-info.*') ? 'active' : '' }}">
                            {{ __('dashboard.sidebar.contact_info') }}
                        </a>
                    @endcan

                    @if ($isSuperAdmin)
                        <a href="{{ route('superadmin.access-management', ['locale' => $activeLocale]) }}"
                            class="dropdown-item {{ request()->routeIs('superadmin.access-management') ? 'active' : '' }}">
                            {{ __('dashboard.sidebar.settings.permissionsManagement') }}
                        </a>
                    @endif
                </div>
            </div>

            {{-- User Management --}}
            <a href="{{ route('superadmin.users.index', ['locale' => $activeLocale]) }}"
                class="nav-item {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-9.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.user_management') }}</span>
            </a>

            @endcan
        @endif

        {{-- Logout --}}
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
            class="nav-item d-flex align-items-center">
            <img src="{{ asset('assets/images/sidebar-icon-12.svg') }}" alt="" class="nav-icon me-2">
            <span class="nav-label">{{ __('dashboard.sidebar.logout') }}</span>
        </a>

        <!-- Hidden logout form -->
        <form id="logout-form" action="{{ route('logout', ['locale' => $activeLocale]) }}" method="POST" class="d-none">
            @csrf
        </form>

    </div>
</aside>
