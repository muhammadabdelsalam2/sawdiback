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

    {{-- You Can Start Get Features About Current Subscription Plan auth()->user()->planFeatures() --}}
    <nav class="sidebar-nav mt-4">

        {{-- Dashboard --}}
        <a href="{{ route($dashboardRoute, ['locale' => $activeLocale]) }}"
            class="nav-item {{ request()->routeIs('dashboard') || request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
            <img src="{{ asset('assets/images/sidebar-icon-1.svg') }}" alt="" class="nav-icon">
            <span class="nav-label">{{ __('dashboard.sidebar.dashboard') }}</span>
        </a>

        {{-- Subscription --}}
        <a href="{{ route('customer.subscription.index', ['locale' => $activeLocale]) }}"
            class="nav-item {{ request()->routeIs('customer.subscription.*') ? 'active' : '' }}">
            <span class="nav-icon">
                <i class="fa-solid fa-credit-card"></i>
            </span>
            <span class="nav-label">My Subscription</span>
        </a>

        {{-- Livestock --}}
        <div
            class="nav-dropdown {{ request()->routeIs('customer.livestock.*') || request()->routeIs('livestock.*') || request()->routeIs('superadmin.access-management') ? 'open' : '' }}">
            <a href="javascript:void(0)"
                class="nav-item has-dropdown {{ request()->routeIs('customer.livestock.*') || request()->routeIs('livestock.*') || request()->routeIs('superadmin.access-management') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-2.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.livestock') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
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

        {{-- Production --}}
        <div class="nav-dropdown">
            <a href="#" class="nav-item">
                <img src="{{ asset('assets/images/sidebar-icon-3.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.production') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
        </div>

        {{-- Crops & Feed --}}
        <div
            class="nav-dropdown {{ request()->routeIs('customer.crops-feed.*') || request()->routeIs('crops-feed.*') || request()->routeIs('superadmin.access-management') ? 'open' : '' }}">
            <a href="javascript:void(0)"
                class="nav-item has-dropdown {{ request()->routeIs('customer.crops-feed.*') || request()->routeIs('crops-feed.*') || request()->routeIs('superadmin.access-management') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-4.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.crops_feed') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
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

        {{-- Inventory --}}
        <div class="nav-dropdown">
            <a href="#" class="nav-item">
                <img src="{{ asset('assets/images/sidebar-icon-5.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.inventory') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
        </div>

        {{-- Sales & Distribution --}}
        <div
            class="nav-dropdown {{ request()->routeIs('customer.sales-distribution.*') ? 'open' : '' }}">
            <a href="javascript:void(0)"
                class="nav-item has-dropdown {{ request()->routeIs('customer.sales-distribution.*') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-6.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('sales_dist.sidebar.title') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
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
        <div class="nav-dropdown">
            <a href="#" class="nav-item">
                <img src="{{ asset('assets/images/sidebar-icon-7.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.procurement') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
        </div>

        {{-- Finance --}}
        <div class="nav-dropdown">
            <a href="#" class="nav-item">
                <img src="{{ asset('assets/images/sidebar-icon-8.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.finance') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
        </div>

        {{-- HR Management (Only if enabled in plan features) --}}
        @if ($hrEnabled)
            <div
                class="nav-dropdown {{ request()->routeIs('customer.hr.*') || request()->routeIs('hr.*') || request()->routeIs('superadmin.access-management') ? 'open' : '' }}">
                <a href="javascript:void(0)"
                    class="nav-item has-dropdown {{ request()->routeIs('customer.hr.*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/images/sidebar-icon-9.svg') }}" alt="" class="nav-icon">
                    <span class="nav-label">{{ __('dashboard.sidebar.hr_management') }}</span>
                    <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
                </a>

                <div class="dropdown-container">
                    <a href="{{ route('customer.hr.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('customer.hr.index') ? 'active' : '' }}">
                        HR Dashboard
                    </a>

                    <a href="{{ route('customer.hr.departments.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('customer.hr.departments.*') ? 'active' : '' }}">
                        Departments
                    </a>

                    <a href="{{ route('customer.hr.job-titles.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('customer.hr.job-titles.*') ? 'active' : '' }}">
                        Job Titles
                    </a>

                    <a href="{{ route('customer.hr.employees.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('customer.hr.employees.*') ? 'active' : '' }}">
                        Employees
                    </a>

                    <a href="{{ route('customer.hr.attendance.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('customer.hr.attendance.*') ? 'active' : '' }}">
                        Attendance
                    </a>

                    <a href="{{ route('customer.hr.leaves.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('customer.hr.leaves.*') ? 'active' : '' }}">
                        Leave Requests
                    </a>
                </div>
            </div>
        @endif

        {{-- Maintenance --}}
        <div class="nav-dropdown">
            <a href="#" class="nav-item">
                <img src="{{ asset('assets/images/sidebar-icon-10.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.maintenance') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
        </div>

    </nav>

    {{-- Bottom Section --}}
    <div class="sidebar-bottom">

        @can('roles.manage')

            {{-- System Settings (Dropdown) --}}
            <div class="nav-dropdown">
                <a href="javascript:void(0)"
                    class="nav-item has-dropdown {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/images/sidebar-icon-11.svg') }}" alt="" class="nav-icon">
                    <span class="nav-label">{{ __('dashboard.sidebar.system_settings') }}</span>
                    <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
                </a>

                <div class="dropdown-container">
                    <a href="{{ route('settings.countries.index', ['locale' => $activeLocale]) }}"
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
                </div>
            </div>

            {{-- User Management --}}
            <a href="{{ route('superadmin.users.index', ['locale' => $activeLocale]) }}"
                class="nav-item {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-9.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">User Management</span>
            </a>

        @endcan

        {{-- Logout --}}
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
            class="nav-item d-flex align-items-center">
            <img src="{{ asset('assets/images/sidebar-icon-12.svg') }}" alt="" class="nav-icon me-2">
            <span class="nav-label">{{ __('dashboard.sidebar.logout') }}</span>
        </a>

        <!-- Hidden logout form -->
        <form id="logout-form" action="{{ route('logout', ['locale' => $currentLocale ?? app()->getLocale()]) }}"
            method="POST" class="d-none">
            @csrf
        </form>

    </div>
</aside>
