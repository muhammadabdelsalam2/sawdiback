<header class="navbar p-0 d-flex align-items-center justify-content-between">
    @php
        $activeLocale = $currentLocale ?? app()->getLocale();
        $isSuperAdmin = auth()->check() && auth()->user()->hasRole('SuperAdmin');
    @endphp
    <div class="navbar-left d-flex align-items-center">
        <button id="sidebar-toggle" class="btn text-green me-2">
            <i class="fa-solid fa-bars"></i>
        </button>
        <img src="{{ asset('assets/images/userLogo.png') }}" alt="Logo" class="logo">
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
    <div class="navbar-right d-flex align-items-center">
        <form action="{{ route('customer.global.search', ['locale' => $activeLocale]) }}" method="GET"
            class="search-container me-3 position-relative">

            <input type="text" name="q" id="globalSearch" autocomplete="off"
                placeholder="{{ __('dashboard.navbar.search') }}" class="search-input">

            <button class="search-btn">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>

            <!-- Dropdown -->
            <div id="searchDropdown" class="search-dropdown d-none"></div>
        </form>

        <div class="navbar-icons d-flex align-items-center me-3">

            <button class="nav-icon-btn"><i class="fa-regular fa-bell"></i></button>

            <!-- Settings Dropdown -->
            <div class="dropdown me-2">
                <button class="nav-icon-btn dropdown-toggle" type="button" id="settingsDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-gear"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsDropdown">
                    @if($isSuperAdmin)

                        <li><a class="dropdown-item"
                                href="{{ route('superadmin.access-management', ['locale' => $activeLocale]) }}">{{ __('dashboard.sidebar.settings.permissionsManagement') }}</a>
                        </li>
                    @else
                        <li><a class="dropdown-item" href="#">{{ __('dashboard.navbar.module') }} 1</a></li>
                    @endif
                    <!-- add more modules here -->
                </ul>
            </div>

            <!-- Language Dropdown -->
            <div class="dropdown me-2">
                <button class="nav-icon-btn dropdown-toggle" type="button" id="languageDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    {{ session('locale_full', 'en-SA') }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                    <li>
                        <a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}"
                            href="{{ route('language.switch', 'en-SA') }}" title="{{ __('dashboard.navbar.english') }}">
                            {{ __('dashboard.navbar.english') }}
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ app()->getLocale() == 'ar' ? 'active' : '' }}"
                            href="{{ route('language.switch', 'ar-SA') }}" title="{{ __('dashboard.navbar.arabic') }}">
                            {{ __('dashboard.navbar.arabic') }}
                        </a>
                    </li>

                </ul>
            </div>

            <button class="nav-icon-btn"><i class="fa-regular fa-sun"></i></button>
        </div>

        <!-- User Profile Dropdown -->
        <div class="user-profile dropdown">
            <a href="#" class="d-flex align-items-center" id="userDropdown" data-bs-toggle="dropdown"
                aria-expanded="false">
                <img src="{{ asset('assets/images/user.png') }}" alt="User" class="user-avatar">
            </a>
            <ul class="dropdown-menu @if(in_array($activeLocale, ['ar-SA', 'ar-EG'])) dropdown-menu-end @else dropdown-menu-start @endif"
                aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="#">{{ __('dashboard.navbar.profile') }}</a></li>
                <li><a class="dropdown-item" href="#">{{ __('dashboard.navbar.settings') }}</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('logout', ['locale' => $activeLocale]) }}"
                        onclick="event.preventDefault(); document.getElementById('navbar-logout-form').submit();">
                        {{ __('dashboard.navbar.logout') }}
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
    const input = document.getElementById('globalSearch');
    const dropdown = document.getElementById('searchDropdown');

    const activeLocale = "{{ $activeLocale }}";

    let timeout = null;
    let lastQuery = '';

    // =========================
    // ONCE STATE CONTROL
    // =========================
    let hasOpenedOnce = false;

    const defaultModules = [
        { name: 'Animals', url: '{{ route('customer.livestock.animals.index', ['locale' => $activeLocale]) }}', icon: 'fa fa-paw' },
        { name: 'Orders', url: '{{ route('customer.ecommerce.orders.index', ['locale' => $activeLocale]) }}', icon: 'fa fa-shopping-cart' },
        { name: 'Products', url: '{{ route('customer.inventory.products.index', ['locale' => $activeLocale]) }}', icon: 'fa fa-box' },
    ];

    function renderModules(modules) {
        dropdown.innerHTML = modules.map(m =>
            `<div class="search-item" onclick="window.location='${m.url}'">
                <i class="${m.icon}"></i> ${m.name}
            </div>`
        ).join('');
    }

    // =========================
    // OPEN DROPDOWN (FOCUS)
    // =========================
    input.addEventListener('focus', () => {
        dropdown.classList.remove('d-none');
        dropdown.classList.add('show');

        renderModules(defaultModules);

        // =========================
        // ONCE ACTION ONLY
        // =========================
        if (!hasOpenedOnce) {
            hasOpenedOnce = true;

            // 👇 هنا أي action مرة واحدة فقط عند أول focus
            console.log('Search opened first time');

            // مثال: ممكن تعمل preload أو analytics أو fetch default AI context
            // search(''); // لو عايز أول مرة يجيب AI results
        }
    });

    // =========================
    // CLOSE ON OUTSIDE CLICK
    // =========================
    document.addEventListener('click', function (e) {
        const container = document.querySelector('.search-container');

        if (!container.contains(e.target)) {
            dropdown.classList.add('d-none');
            dropdown.classList.remove('show');
        }
    });

    // =========================
    // INPUT ACTION (FULL SEARCH)
    // =========================
    input.addEventListener('input', function () {

        clearTimeout(timeout);

        let q = this.value.trim();

        if (!q) {
            renderModules(defaultModules);
            return;
        }

        if (q === lastQuery) return;
        lastQuery = q;

        timeout = setTimeout(() => {
            search(q);
        }, 500);
    });

    // =========================
    // SEARCH FUNCTION
    // =========================
    function search(q) {

        const token = localStorage.getItem('auth_token');

        const url = `{{ route('customer.global.search', ['locale' => $activeLocale]) }}?q=${encodeURIComponent(q)}`;

        dropdown.innerHTML = `
            <div class="search-item ai-loader">
                <div class="ai-spinner"></div>
                AI Searching...
            </div>
        `;

        dropdown.classList.remove('d-none');
        dropdown.classList.add('show');

        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token
            }
        })
            .then(res => {
                if (!res.ok) {
                    throw new Error('Unauthorized or Rate limit issue');
                }
                return res.json();
            })
            .then(data => {

                dropdown.innerHTML = '';

                if (!data || data.length === 0) {
                    dropdown.innerHTML = `<div class="search-item">No results found</div>`;
                    return;
                }

                data.forEach(item => {
                    dropdown.innerHTML += `
                    <div class="search-item" onclick="window.location='${item.url}'">
                        <strong>${item.type}</strong> - ${item.name}
                    </div>
                `;
                });
            })
            .catch(err => {
                console.error(err);

                dropdown.innerHTML = `
                <div class="search-item text-danger">
                    Something went wrong
                </div>
            `;
            });
    }
</script>