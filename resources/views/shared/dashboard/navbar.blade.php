<header class="navbar p-0 d-flex align-items-center justify-content-between">
    <div class="navbar-left d-flex align-items-center">
        <button id="sidebar-toggle" class="btn text-green me-2">
            <i class="fa-solid fa-bars"></i>
        </button>
        <img src="{{ asset('assets/images/userLogo.png') }}" alt="Logo" class="logo">
    </div>

    <div class="navbar-right d-flex align-items-center">
        <div class="search-container me-3">
            <input type="text" placeholder="Search" class="search-input">
            <button class="search-btn">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>

        <div class="navbar-icons d-flex align-items-center me-3">

            <button class="nav-icon-btn"><i class="fa-regular fa-bell"></i></button>

            <!-- Settings Dropdown -->
            <div class="dropdown me-2">
                <button class="nav-icon-btn dropdown-toggle" type="button" id="settingsDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-gear"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsDropdown">
                    @if(auth()?->user()->hasRole('SuperAdmin'))

                        <li><a class="dropdown-item"
                                href="{{ route('superadmin.access-management', ['locale' => $currentLocale]) }}">{{__('superadmin.dashboard.user_management')}}</a>
                        </li>
                    @else
                        <li><a class="dropdown-item" href="#">Module 1</a></li>
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
                        <a class="dropdown-item" href="{{ route('language.switch', 'en-SA') }}">
                            English - SA
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('language.switch', 'ar-SA') }}">
                            العربية - السعودية
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('language.switch', 'en-EG') }}">
                            English - EG
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('language.switch', 'ar-EG') }}">
                            العربية - مصر
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
            <ul class="dropdown-menu @if(in_array($currentLocale, ['ar-SA', 'ar-EG'])) dropdown-menu-end @else dropdown-menu-start @endif"
                aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="#">Profile</a></li>
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('logout', $currentLocale) }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout', $currentLocale) }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>

    </div>
</header>