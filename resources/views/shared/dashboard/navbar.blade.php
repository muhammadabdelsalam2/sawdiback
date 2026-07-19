<header class="navbar navbar-enhanced p-0 d-flex align-items-center justify-content-between">
    @php
        $activeLocale = $currentLocale ?? app()->getLocale();
        $isSuperAdmin = auth()->check() && auth()->user()->hasRole('SuperAdmin');
        $user = auth()->user();
        $avatar = $user?->avatar;
        $avatarUrl = null;
        if ($avatar) {
            $avatarUrl = filter_var($avatar, FILTER_VALIDATE_URL) ? $avatar : asset('storage/' . ltrim($avatar, '/'));
        } else {
            $avatarUrl = asset('assets/images/user.png');
        }
    @endphp
    <div class="navbar-left d-flex align-items-center">
        <button id="sidebar-toggle" class="btn btn-navbar-toggle text-green me-3">
            <i class="fa-solid fa-bars"></i>
        </button>
        <span class="brand-mark" aria-label="EL-Sawady">
            <img src="{{ asset('assets/images/logo3.jpeg') }}" alt="EL-Sawady" class="logo logo-enhanced">
        </span>
    </div>
    <style>
        /* =========================
   AI LOADING SPINNER
========================= */

        .ai-loader {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            font-weight: 500;
            color: #555;
        }

        .ai-spinner {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: linear-gradient(45deg, #00c6ff, #7f00ff, #ff4ecd, #00c6ff);
            background-size: 300% 300%;
            animation: gradientMove 1.2s ease infinite, spin 0.8s linear infinite;
            box-shadow: 0 0 10px rgba(127, 0, 255, 0.4);
        }

        @keyframes gradientMove {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    <style>
        .search-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            width: 350px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            z-index: 999;
            padding: 10px;
        }

        .search-dropdown {
            opacity: 0;
            transform: translateY(-10px);
            visibility: hidden;
            transition: all 0.2s ease;
        }

        .search-dropdown.show {
            opacity: 1;
            transform: translateY(0);
            visibility: visible;
        }

        .search-item {
            padding: 8px;
            border-radius: 6px;
            cursor: pointer;
        }

        .search-item:hover {
            background: #f5f5f5;
        }
    </style>
    <div class="navbar-right d-flex align-items-center gap-3">
        <form action="{{ route('customer.global.search', ['locale' => $activeLocale]) }}" method="GET"
            class="search-container search-container-enhanced me-2 position-relative">

            <i class="fa-solid fa-magnifying-glass search-icon-left"></i>
            <input type="text" name="q" id="globalSearch" autocomplete="off"
                placeholder="{{ __('dashboard.navbar.search_placeholder') }}" class="search-input-enhanced">

            <button type="button" class="search-btn-enhanced">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>

            <!-- Dropdown -->
            <div id="searchDropdown" class="search-dropdown d-none"></div>
        </form>

        <div class="navbar-icons-enhanced d-flex align-items-center">

            <!-- <button class="nav-icon-btn-enhanced" title="{{ __('dashboard.navbar.notifications') }}">
                <i class="fa-regular fa-bell"></i>
                <span class="notification-badge">3</span>
            </button> -->

            <!-- Settings Dropdown -->
            @if($isSuperAdmin)
            <div class="dropdown">
                <button class="nav-icon-btn-enhanced dropdown-toggle" type="button" id="settingsDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('dashboard.navbar.settings') }}">
                    <i class="fa-solid fa-gear"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-enhanced dropdown-menu-end" aria-labelledby="settingsDropdown">

                        <li><a class="dropdown-item" href="{{ route('superadmin.access-management', ['locale' => $activeLocale]) }}">
                            <i class="bi bi-shield-lock"></i>
                            {{ __('dashboard.sidebar.settings.permissionsManagement') }}
                        </a></li>

                    <!-- <li><a class="dropdown-item" href="#">
                        <i class="bi bi-gear"></i>
                        {{ __('dashboard.navbar.system_settings') }}
                    </a></li> -->
                </ul>
            </div>
            @endif
            <!-- Language Dropdown -->
            <div class="dropdown">
                <button class="nav-icon-btn-enhanced nav-language-btn-enhanced dropdown-toggle" type="button" id="languageDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('dashboard.navbar.language') }}">
                    <i class="bi bi-globe2"></i>
                    <span class="lang-code">{{ app()->getLocale() == 'ar' ? 'العربية' : 'English' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-enhanced dropdown-menu-end" aria-labelledby="languageDropdown">
                    <li>
                        <a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}"
                            href="{{ route('language.switch', 'en-SA') }}" title="{{ __('dashboard.navbar.english') }}">
                            <i class="bi bi-check2"></i> English
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ app()->getLocale() == 'ar' ? 'active' : '' }}"
                            href="{{ route('language.switch', 'ar-SA') }}" title="{{ __('dashboard.navbar.arabic') }}">
                            <i class="bi bi-check2"></i> العربية
                        </a>
                    </li>
                </ul>
            </div>

            <!-- <button class="nav-icon-btn-enhanced" title="{{ __('dashboard.navbar.dark_mode') }}">
                <i class="fa-regular fa-moon"></i>
            </button> -->
        </div>

        <!-- User Profile Dropdown -->
        <div class="user-profile-enhanced dropdown">
            <a href="#" class="d-flex align-items-center" id="userDropdown" data-bs-toggle="dropdown"
                aria-expanded="false" title="{{ auth()->user()->name }}">
                <div class="user-avatar-wrapper">
                    <img src="{{ $avatarUrl }}" alt="{{ auth()->user()->name }}" class="user-avatar-enhanced">
                    <span class="user-status-dot"></span>
                </div>
                <span class="user-name-navbar">{{ auth()->user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-enhanced @if(in_array($activeLocale, ['ar-SA', 'ar-EG'])) dropdown-menu-end @else dropdown-menu-start @endif"
                aria-labelledby="userDropdown">
                <li class="dropdown-header-custom">
                    <div class="user-info-header">
                        <img src="{{ $avatarUrl }}" alt="{{ auth()->user()->name }}" class="user-avatar-dropdown">
                        <div>
                            <p class="user-name">{{ auth()->user()->name }}</p>
                            <p class="user-email">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ $isSuperAdmin ? route('superadmin.profile.show', ['locale' => $activeLocale]) : route('customer.profile.show', ['locale' => $activeLocale]) }}">
                    <i class="bi bi-person"></i> {{ __('dashboard.navbar.profile') }}
                </a></li>
                @if($isSuperAdmin)
                    <li><a class="dropdown-item" href="{{ route('superadmin.settings.show', ['locale' => $activeLocale]) }}">
                        <i class="bi bi-sliders"></i> {{ __('dashboard.navbar.settings_link') }}
                    </a></li>
                @endif
                <li><a class="dropdown-item" href="{{ $isSuperAdmin ? route('superadmin.password.show', ['locale' => $activeLocale]) : route('customer.password.show', ['locale' => $activeLocale]) }}">
                    <i class="bi bi-key"></i> {{ __('dashboard.navbar.change_password') }}
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="{{ route('logout', ['locale' => $activeLocale]) }}"
                        onclick="event.preventDefault(); document.getElementById('navbar-logout-form').submit();">
                        <i class="bi bi-box-arrow-right"></i> {{ __('dashboard.navbar.logout') }}
                    </a>
                    <form id="navbar-logout-form" action="{{ route('logout', ['locale' => $activeLocale]) }}"
                        method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>

    </div>
</header>
<script>
    window.appConfig = {
        activeLocale: "{{ $activeLocale }}",
        searchUrl: "{{ route('customer.global.search', ['locale' => $activeLocale]) }}",
        animalsUrl: "{{ route('customer.livestock.animals.index', ['locale' => $activeLocale]) }}",
        ordersUrl: "{{ route('customer.ecommerce.orders.index', ['locale' => $activeLocale]) }}",
        productsUrl: "{{ route('customer.inventory.products.index', ['locale' => $activeLocale]) }}",
        token: localStorage.getItem('auth_token')
    };
</script>
