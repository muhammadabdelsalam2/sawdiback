<!-- Sidebar -->
<aside id="sidebar">
    @php
        $isSuperAdmin = auth()->check() && auth()->user()->hasRole('SuperAdmin');
        $dashboardRoute = $isSuperAdmin ? 'superadmin.dashboard' : 'dashboard';
        $activeLocale = $currentLocale ?? app()->getLocale();
    @endphp

    <nav class="sidebar-nav mt-4">

        {{-- Dashboard --}}
        <a href="{{ route($dashboardRoute, ['locale' => $activeLocale]) }}"
            class="nav-item {{ request()->routeIs('dashboard') || request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
            <img src="{{ asset('assets/images/sidebar-icon-1.svg') }}" alt="" class="nav-icon">
            <span class="nav-label">{{ __('dashboard.sidebar.dashboard') }}</span>
        </a>

        {{-- Livestock --}}
        <div class="nav-dropdown {{ request()->routeIs('customer.livestock.*') ? 'open' : '' }}">
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

        {{-- Production Dropdown (Contains Subscriptions/Plans) --}}
        <div class="nav-dropdown {{ request()->routeIs('superadmin.plans.*') || request()->routeIs('superadmin.subscriptions.*') ? 'open' : '' }}">
            <a href="javascript:void(0)" class="nav-item has-dropdown">
                <img src="{{ asset('assets/images/sidebar-icon-3.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.production') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
            <div class="dropdown-container">
                <a href="{{ route('superadmin.plans.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('superadmin.plans.*') ? 'active' : '' }}">
                    <i class="bi bi-gem me-2"></i> {{ __('dashboard.sidebar.plans') }}
                </a>
                <a href="{{ route('superadmin.subscriptions.index', ['locale' => $activeLocale]) }}"
                    class="dropdown-item {{ request()->routeIs('superadmin.subscriptions.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-repeat me-2"></i> {{ __('dashboard.sidebar.subscriptions') }}
                </a>
            </div>
        </div>

        <div class="sidebar-bottom">

            @can('roles.manage')

                {{-- System Settings (Dropdown) --}}
                <div class="nav-dropdown {{ request()->routeIs('superadmin.setting.*') || request()->routeIs('superadmin.access-management') ? 'open' : '' }}">
                    <a href="javascript:void(0)"
                        class="nav-item has-dropdown {{request()->routeIs('superadmin.setting.*') || request()->routeIs('superadmin.access-management') ? 'active' : '' }}">
                        <img src="{{ asset('assets/images/sidebar-icon-11.svg') }}" alt="" class="nav-icon">
                        <span class="nav-label">{{ __('dashboard.sidebar.system_settings') }}</span>
                        <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
                    </a>

                    <div class="dropdown-container">
                        <a href="{{ route('superadmin.setting.countries.index', ['locale' => $activeLocale]) }}"
                            class="dropdown-item {{ request()->routeIs('superadmin.setting.countries.*') ? 'active' : '' }}">
                            {{ __('dashboard.sidebar.countries') }}
                        </a>

                        <a href="{{ route('superadmin.setting.cities.index', ['locale' => $activeLocale]) }}"
                            class="dropdown-item {{ request()->routeIs('superadmin.setting.cities.*') ? 'active' : '' }}">
                            {{ __('dashboard.sidebar.cities') }}
                        </a>

                        <a href="{{ route('superadmin.setting.theme.index', ['locale' => $activeLocale]) }}"
                            class="dropdown-item {{ request()->routeIs('superadmin.setting.theme.*') ? 'active' : '' }}">
                            {{ __('dashboard.sidebar.theme') }}
                        </a>

                        @can('roles.manage')
                            <a href="{{ route('superadmin.access-management', ['locale' => $activeLocale]) }}"
                                class="dropdown-item {{ request()->routeIs('superadmin.access-management') ? 'active' : '' }}">
                                {{ __('dashboard.sidebar.settings.permissionsManagement') }}
                            </a>
                        @endcan
                    </div>
                </div>

                {{-- User Management --}}
                <a href="{{ route('superadmin.users.index', ['locale' => $activeLocale]) }}"
                    class="nav-item {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}">
                    <img src="{{ asset('assets/images/sidebar-icon-9.svg') }}" alt="" class="nav-icon">
                    <span class="nav-label">{{ __('dashboard.sidebar.user_management') }}</span>
                </a>

            @endcan

            <!-- Link-style logout -->
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                class="nav-item d-flex align-items-center">
                <img src="{{ asset('assets/images/sidebar-icon-12.svg') }}" alt="" class="nav-icon me-2">
                <span class="nav-label">{{ __('dashboard.sidebar.logout') }}</span>
            </a>

            <!-- Hidden logout form -->
            <form id="logout-form" action="{{ route('logout', ['locale' => $activeLocale]) }}" method="POST"
                class="d-none">
                @csrf
            </form>
        </div>
    </nav>
</aside>