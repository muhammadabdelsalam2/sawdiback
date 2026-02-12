<!-- Sidebar -->
<aside id="sidebar">
    @php
        $isSuperAdmin = auth()->check() && auth()->user()->hasRole('SuperAdmin');
        $dashboardRoute = $isSuperAdmin ? 'superadmin.dashboard' : 'dashboard';
        $activeLocale = $currentLocale ?? app()->getLocale();
    @endphp
    <nav class="sidebar-nav mt-4">
        <a href="{{ route($dashboardRoute, ['locale' => $activeLocale]) }}"
            class="nav-item {{ request()->routeIs('dashboard') || request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
            <img src="{{ asset('assets/images/sidebar-icon-1.svg') }}" alt="" class="nav-icon">
            <span class="nav-label">{{ __('dashboard.sidebar.dashboard') }}</span>
        </a>

        <div class="nav-dropdown">
            <a href="javascript:void(0)" class="nav-item has-dropdown">
                <img src="{{ asset('assets/images/sidebar-icon-2.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.livestock') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
            <div class="dropdown-container">
                <a href="animal-registry.html" class="dropdown-item">{{ __('dashboard.sidebar.animal_registry') }}</a>
                <a href="#" class="dropdown-item">{{ __('dashboard.sidebar.health_vax') }}</a>
                <a href="#" class="dropdown-item">{{ __('dashboard.sidebar.breeding_cycles') }}</a>
            </div>
        </div>

        <div class="nav-dropdown">
            <a href="#" class="nav-item">
                <img src="{{ asset('assets/images/sidebar-icon-3.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.production') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
        </div>

        <div class="nav-dropdown">
            <a href="#" class="nav-item">
                <img src="{{ asset('assets/images/sidebar-icon-4.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.crops_feed') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
        </div>

        <div class="nav-dropdown">
            <a href="#" class="nav-item">
                <img src="{{ asset('assets/images/sidebar-icon-5.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.inventory') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
        </div>

        <div class="nav-dropdown">
            <a href="#" class="nav-item">
                <img src="{{ asset('assets/images/sidebar-icon-6.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.sales_distribution') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
        </div>

        <div class="nav-dropdown">
            <a href="#" class="nav-item">
                <img src="{{ asset('assets/images/sidebar-icon-7.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.procurement') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
        </div>

        <div class="nav-dropdown">
            <a href="#" class="nav-item">
                <img src="{{ asset('assets/images/sidebar-icon-8.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.finance') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
        </div>

        <div class="nav-dropdown">
            <a href="#" class="nav-item">
                <img src="{{ asset('assets/images/sidebar-icon-9.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.hr_management') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
        </div>

        <div class="nav-dropdown">
            <a href="#" class="nav-item">
                <img src="{{ asset('assets/images/sidebar-icon-10.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.maintenance') }}</span>
                <i class="fa-solid fa-chevron-right ms-auto chevron"></i>
            </a>
        </div>
     @role('SuperAdmin')

<div class="nav-dropdown">
    <a href="{{ route('superadmin.plans.index', ['locale' => $currentLocale ?? app()->getLocale()]) }}"
        class="nav-item {{ request()->routeIs('superadmin.plans.*') ? 'active' : '' }}">
        <i class="bi bi-gem nav-icon"></i>
        <span class="nav-label">{{ __('dashboard.sidebar.plans') }}</span>
    </a>
</div>

<div class="nav-dropdown">
    <a href="{{ route('superadmin.subscriptions.index', ['locale' => $currentLocale ?? app()->getLocale()]) }}"
        class="nav-item {{ request()->routeIs('superadmin.subscriptions.*') ? 'active' : '' }}">
        <i class="bi bi-arrow-repeat nav-icon"></i>
        <span class="nav-label">{{ __('dashboard.sidebar.subscriptions') }}</span>
    </a>
</div>

@endrole


    <div class="sidebar-bottom">
        @can('roles.manage')
            <a href="{{ route('superadmin.access-management', ['locale' => $activeLocale]) }}"
                class="nav-item {{ request()->routeIs('superadmin.access-management') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-11.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">{{ __('dashboard.sidebar.system_settings') }}</span>
            </a>
            <a href="{{ route('superadmin.users.index', ['locale' => $activeLocale]) }}"
                class="nav-item {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}">
                <img src="{{ asset('assets/images/sidebar-icon-9.svg') }}" alt="" class="nav-icon">
                <span class="nav-label">User Management</span>
            </a>
        @endcan
        <!-- Link-style logout -->
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
