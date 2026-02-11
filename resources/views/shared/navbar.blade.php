<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top py-3 border-bottom border-light">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4 text-green"
            href="{{route('public.home', ['locale' => $currentLocale])}}">EL-Sawady</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link mx-2" href="#features">{{ __('app.nav_features') }}</a></li>
                <li class="nav-item"><a class="nav-link mx-2" href="#pricing">{{ __('app.nav_pricing') }}</a></li>
                <li class="nav-item"><a class="nav-link mx-2" href="#">{{ __('app.nav_docs') }}</a></li>
                <li class="nav-item ms-lg-3">
                    <a class="btn btn-outline-dark px-4 rounded-pill"
                        href="{{ route('login.form', ['locale' => $currentLocale]) }}">
                        {{ __('app.nav_login') }}
                    </a>
                </li>



                <!-- Language Dropdown -->
                <li class="nav-item dropdown ms-3">
                    <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        {{ session('locale_full', 'en-SA') }}
                    </a>

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
                </li>

            </ul>
        </div>
    </div>
</nav>