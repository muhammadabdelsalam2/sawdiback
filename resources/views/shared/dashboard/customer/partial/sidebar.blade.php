<!-- Sidebar -->

<aside id="sidebar">
    @php
        $isSuperAdmin = auth()->check() && auth()->user()->hasRole('SuperAdmin');
        $dashboardRoute = $isSuperAdmin ? 'superadmin.dashboard' : 'dashboard';
        $activeLocale = $currentLocale ?? app()->getLocale();
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
<li class="nav-item">
    <a class="nav-link" href="{{ route('customer.subscription.index', ['locale' => request()->route('locale')]) }}">
        <span class="nav-icon">
            <i class="fa-solid fa-credit-card"></i>
        </span>
        <span class="nav-label">My Subscription</span>
    </a>
</li>


        {{-- Livestock --}}
        <div class="nav-dropdown">
            <a href="javascript:void(0)" class="nav-item has-dropdown">
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
        <div class="nav-dropdown">
            <a href="#" class="nav-item">
                <img src="{{ asset('assets/images/sidebar-icon-4.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.crops_feed') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
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
        <div class="nav-dropdown">
            <a href="#" class="nav-item">
                <img src="{{ asset('assets/images/sidebar-icon-6.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.sales_distribution') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
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

        {{-- HR Management --}}
        <div class="nav-dropdown">
            <a href="#" class="nav-item">
                <img src="{{ asset('assets/images/sidebar-icon-9.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.hr_management') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
        </div>

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

                    <a href="{{ route('settings.cities.index', ['locale' => $activeLocale]) }}"
                        class="dropdown-item {{ request()->routeIs('settings.cities.*') ? 'active' : '' }}">
                        {{ __('dashboard.sidebar.cities') }}
                    </a>

                    <a href="{{ route('settings.theme.edit', ['locale' => $activeLocale]) }}"
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
        <a href="#"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
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
