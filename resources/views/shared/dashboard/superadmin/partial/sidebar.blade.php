<!-- Sidebar -->
<aside id="sidebar">
    @php
        $activeLocale = $currentLocale ?? app()->getLocale();
    @endphp

    <nav class="sidebar-nav mt-4">
          {{-- Extra links outside the original ERP document order --}}
        <a href="{{ route('superadmin.dashboard', ['locale' => $activeLocale]) }}" class="nav-item {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
            <img src="{{ asset('assets/images/sidebar-icon-1.svg') }}" alt="" class="nav-icon">
            <span class="nav-label">{{ __('dashboard.sidebar.dashboard') }}</span>
        </a>
        {{-- 1. HR --}}
        <div class="nav-dropdown {{ request()->routeIs('customer.hr.*') ? 'open' : '' }}">
            <a href="javascript:void(0)" class="nav-item has-dropdown {{ request()->routeIs('customer.hr.*') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-9.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.requirements.hr') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
            <div class="dropdown-container">
                <a href="{{ route('customer.hr.employees.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.hr.employees.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.employees') }}</a>
                <a href="{{ route('customer.hr.attendance.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.hr.attendance.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.attendance') }}</a>
                <a href="{{ route('customer.hr.departments.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.hr.departments.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.departments') }}</a>
                <a href="{{ route('customer.hr.job-titles.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.hr.job-titles.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.job_titles') }}</a>
            </div>
        </div>
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
        {{-- 2. Livestock --}}
        <!-- <div class="nav-dropdown {{ request()->routeIs('customer.livestock.animals.*') || request()->routeIs('customer.livestock.reproduction-cycles.*') || request()->routeIs('customer.livestock.species.*') || request()->routeIs('customer.livestock.breeds.*') ? 'open' : '' }}">
            <a href="javascript:void(0)" class="nav-item has-dropdown {{ request()->routeIs('customer.livestock.animals.*') || request()->routeIs('customer.livestock.reproduction-cycles.*') || request()->routeIs('customer.livestock.species.*') || request()->routeIs('customer.livestock.breeds.*') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-2.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.requirements.livestock') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
            <div class="dropdown-container">
                <a href="{{ route('customer.livestock.animals.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.livestock.animals.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.animal_registry') }}</a>
                <a href="{{ route('customer.livestock.reproduction-cycles.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.livestock.reproduction-cycles.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.breeding_cycles') }}</a>
                <a href="{{ route('customer.livestock.species.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.livestock.species.*') ? 'active' : '' }}">{{ __('livestock.titles.species') }}</a>
                <a href="{{ route('customer.livestock.breeds.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.livestock.breeds.*') ? 'active' : '' }}">{{ __('livestock.titles.breeds') }}</a>
            </div>
        </div> -->

        {{-- 3. Crops --}}
        <a href="{{ route('customer.crops-feed.crops.index', ['locale' => $activeLocale]) }}" class="nav-item {{ request()->routeIs('customer.crops-feed.crops.*') ? 'active' : '' }}">
            <img src="{{ asset('assets/images/sidebar-icon-4.svg') }}" alt="" class="nav-icon">
            <span class="nav-label">{{ __('dashboard.sidebar.requirements.crops') }}</span>
        </a>

        {{-- 4. Poultry --}}
        @can('poultry.view')
            <div class="nav-dropdown {{ request()->routeIs('customer.poultry.*') ? 'open' : '' }}">
                <a href="javascript:void(0)" class="nav-item has-dropdown {{ request()->routeIs('customer.poultry.*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/images/sidebar-icon-4.svg') }}" alt="" class="nav-icon">
                    <span class="nav-label">{{ __('dashboard.sidebar.requirements.poultry') }}</span>
                    <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
                </a>
                <div class="dropdown-container">
                    <a href="{{ route('customer.poultry.broiler-cycles.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.poultry.broiler-cycles.*') ? 'active' : '' }}">{{ __('poultry.titles.broiler_cycles') }}</a>
                    <a href="{{ route('customer.poultry.layer-flocks.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.poultry.layer-flocks.*') ? 'active' : '' }}">{{ __('poultry.titles.layer_flocks') }}</a>
                    <a href="{{ route('customer.poultry.hatchery-machines.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.poultry.hatchery-machines.*') ? 'active' : '' }}">{{ __('poultry.titles.hatchery_machines') }}</a>
                    <a href="{{ route('customer.poultry.hatchery-batches.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.poultry.hatchery-batches.*') ? 'active' : '' }}">{{ __('poultry.titles.hatchery_batches') }}</a>
                    <a href="{{ route('customer.poultry.chicken-breeds.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.poultry.chicken-breeds.*') ? 'active' : '' }}">{{ __('poultry.titles.chicken_breeds') }}</a>
                </div>
            </div>
        @endcan

        {{-- 5. Health and vaccinations --}}
        <div class="nav-dropdown {{ request()->routeIs('customer.livestock.vaccines.*') || request()->routeIs('customer.livestock.alerts.*') ? 'open' : '' }}">
            <a href="javascript:void(0)" class="nav-item has-dropdown {{ request()->routeIs('customer.livestock.vaccines.*') || request()->routeIs('customer.livestock.alerts.*') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-2.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.requirements.health_vaccinations') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
            <div class="dropdown-container">
                <a href="{{ route('customer.livestock.vaccines.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.livestock.vaccines.*') ? 'active' : '' }}">{{ __('livestock.titles.vaccines') }}</a>
                <a href="{{ route('customer.livestock.alerts.vaccinations-due', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.livestock.alerts.vaccinations-due') ? 'active' : '' }}">{{ __('livestock.titles.vaccinations_due') }}</a>
                <a href="{{ route('customer.livestock.alerts.vaccinations-overdue', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.livestock.alerts.vaccinations-overdue') ? 'active' : '' }}">{{ __('livestock.titles.vaccinations_overdue') }}</a>
            </div>
        </div>

        {{-- 6. Inventory --}}
        <!-- <div class="nav-dropdown {{ request()->routeIs('customer.inventory.*') || request()->routeIs('customer.crops-feed.feed.*') || request()->routeIs('customer.livestock.feed-types.*') ? 'open' : '' }}">
            <a href="javascript:void(0)" class="nav-item has-dropdown {{ request()->routeIs('customer.inventory.*') || request()->routeIs('customer.crops-feed.feed.*') || request()->routeIs('customer.livestock.feed-types.*') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-5.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.requirements.inventory') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
            <div class="dropdown-container">
                <a href="{{ route('customer.inventory.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.inventory.index') ? 'active' : '' }}">{{ __('warehouse.titles.warehouse') }}</a>
                <a href="{{ route('customer.inventory.products.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.inventory.products.*') ? 'active' : '' }}">{{ __('warehouse.titles.products') }}</a>
                <a href="{{ route('customer.inventory.categories.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.inventory.categories.*') ? 'active' : '' }}">{{ __('warehouse.titles.categories') }}</a>
                <a href="{{ route('customer.crops-feed.feed.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.crops-feed.feed.*') ? 'active' : '' }}">{{ __('crops_feed.titles.feed_management') }}</a>
                <a href="{{ route('customer.livestock.feed-types.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.livestock.feed-types.*') ? 'active' : '' }}">{{ __('livestock.titles.feed_types') }}</a>
            </div>
        </div> -->

        {{-- 7. Sales --}}
        <!-- <div class="nav-dropdown {{ request()->routeIs('customer.sales-distribution.*') ? 'open' : '' }}">
            <a href="javascript:void(0)" class="nav-item has-dropdown {{ request()->routeIs('customer.sales-distribution.*') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-6.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.requirements.sales') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
            <div class="dropdown-container">
                <a href="{{ route('customer.sales-distribution.dashboard', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.sales-distribution.dashboard') ? 'active' : '' }}">{{ __('sales_dist.sidebar.dashboard') }}</a>
                <a href="{{ route('customer.sales-distribution.customers.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.sales-distribution.customers.*') ? 'active' : '' }}">{{ __('sales_dist.sidebar.customers') }}</a>
                <a href="{{ route('customer.sales-distribution.orders.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.sales-distribution.orders.*') ? 'active' : '' }}">{{ __('sales_dist.sidebar.orders') }}</a>
                <a href="{{ route('customer.sales-distribution.invoices.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.sales-distribution.invoices.*') ? 'active' : '' }}">{{ __('sales_dist.sidebar.invoices') }}</a>
            </div>
        </div> -->

        {{-- 8. Financial reports --}}
        <div class="nav-dropdown {{ request()->routeIs('customer.finance.*') ? 'open' : '' }}">
            <a href="javascript:void(0)" class="nav-item has-dropdown {{ request()->routeIs('customer.finance.*') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-8.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.requirements.financial_reports') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
            <div class="dropdown-container">
                <a href="{{ route('customer.finance.profit-loss.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.finance.profit-loss.*') ? 'active' : '' }}">{{ __('finance.sidebar.profit_loss') }}</a>
                <a href="{{ route('customer.finance.dashboard', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.finance.dashboard') ? 'active' : '' }}">{{ __('finance.sidebar.dashboard') }}</a>
                <a href="{{ route('customer.finance.accounts.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('customer.finance.accounts.*') ? 'active' : '' }}">{{ __('finance.sidebar.accounts') }}</a>
            </div>
        </div>

        {{-- 9. Warehouse --}}
        <!-- @can('warehouse.view')
            <a href="{{ route('customer.warehouse-assets.index', ['locale' => $activeLocale]) }}" class="nav-item {{ request()->routeIs('customer.warehouse-assets.*') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-5.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.requirements.warehouse') }}</span>
            </a>
        @endcan -->

        {{-- 10. Analytics --}}
        @can('analytics.view')
            <a href="{{ route('customer.analytics.index', ['locale' => $activeLocale]) }}" class="nav-item {{ request()->routeIs('customer.analytics.*') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-8.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.requirements.analytics') }}</span>
            </a>
        @endcan

        {{-- 11. Production --}}
        <div class="nav-dropdown {{ request()->routeIs('superadmin.plans.*') || request()->routeIs('superadmin.subscriptions.*') ? 'open' : '' }}">
            <a href="javascript:void(0)" class="nav-item has-dropdown {{ request()->routeIs('superadmin.plans.*') || request()->routeIs('superadmin.subscriptions.*') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-3.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.requirements.production') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
            <div class="dropdown-container">
                <a href="{{ route('superadmin.plans.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('superadmin.plans.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.plans') }}</a>
                <a href="{{ route('superadmin.subscriptions.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('superadmin.subscriptions.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.subscriptions') }}</a>
            </div>
        </div>

        @can('roles.manage')
            {{-- 12. System management --}}
            <div class="nav-dropdown {{ request()->routeIs('superadmin.setting.*') || request()->routeIs('superadmin.access-management') || request()->routeIs('superadmin.content.*') ? 'open' : '' }}">
                <a href="javascript:void(0)" class="nav-item has-dropdown {{ request()->routeIs('superadmin.setting.*') || request()->routeIs('superadmin.access-management') || request()->routeIs('superadmin.content.*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/images/sidebar-icon-11.svg') }}" alt="" class="nav-icon">
                    <span class="nav-label">{{ __('dashboard.sidebar.requirements.system_management') }}</span>
                    <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
                </a>
                <div class="dropdown-container">
                    <a href="{{ route('superadmin.content.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('superadmin.content.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.content') }}</a>
                    <a href="{{ route('superadmin.setting.countries.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('superadmin.setting.countries.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.countries') }}</a>
                    <a href="{{ route('superadmin.setting.cities.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('superadmin.setting.cities.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.cities') }}</a>
                    <a href="{{ route('superadmin.setting.theme.index', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('superadmin.setting.theme.*') ? 'active' : '' }}">{{ __('dashboard.sidebar.theme') }}</a>
                    <a href="{{ route('superadmin.access-management', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('superadmin.access-management') ? 'active' : '' }}">{{ __('dashboard.sidebar.settings.permissionsManagement') }}</a>
                    @can('farms.manage')
                        <a href="{{ route('superadmin.farm-assignments.employees', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('superadmin.farm-assignments.employees') ? 'active' : '' }}">{{ __('superadmin.farm_assignments.employees_title') }}</a>
                        <a href="{{ route('superadmin.farm-assignments.products', ['locale' => $activeLocale]) }}" class="dropdown-item {{ request()->routeIs('superadmin.farm-assignments.products') ? 'active' : '' }}">{{ __('superadmin.farm_assignments.products_title') }}</a>
                    @endcan
                </div>
            </div>

            {{-- 13. User management --}}
            <a href="{{ route('superadmin.users.index', ['locale' => $activeLocale]) }}" class="nav-item {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-9.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.requirements.user_management') }}</span>
            </a>
        @endcan

        {{-- 14. Logout --}}
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-item d-flex align-items-center">
            <img src="{{ asset('assets/images/sidebar-icon-12.svg') }}" alt="" class="nav-icon me-2">
            <span class="nav-label">{{ __('dashboard.sidebar.requirements.logout') }}</span>
        </a>



        <form id="logout-form" action="{{ route('logout', ['locale' => $activeLocale]) }}" method="POST" class="d-none">
            @csrf
        </form>
    </nav>
</aside>
