<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top py-3 border-bottom border-light">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" 
           href="{{route('public.home', ['locale' => $currentLocale])}}" 
           style="color: #2D5A27;">EL-Sawady</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link mx-2 fw-medium" href="#features">{{ __('app.nav_features') }}</a></li>
                <li class="nav-item"><a class="nav-link mx-2 fw-medium" href="#pricing">{{ __('app.nav_pricing') }}</a></li>
                <li class="nav-item"><a class="nav-link mx-2 fw-medium" href="#">{{ __('app.nav_docs') }}</a></li>
                
                <li class="nav-item ms-lg-3 d-flex gap-2">
                    <a class="btn btn-outline-dark px-4 rounded-pill fw-bold"
                        href="{{ route('login.form', ['locale' => $currentLocale]) }}">
                        {{ __('app.nav_login') }}
                    </a>
                    
                    <a class="btn px-4 rounded-pill fw-bold shadow-sm"
                        href="{{ route('showRegister', ['locale' => $currentLocale]) }}"
                        style="background-color: #2D5A27; color: white;">
                        {{ __('app.nav_register') ?? 'Register' }}
                    </a>
                </li>

                <li class="nav-item dropdown ms-3 border-start ps-3">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="languageDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-globe2 me-2"></i> {{ session('locale_full', 'en-SA') }}
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="languageDropdown" style="border-radius: 12px;">
                        <li><a class="dropdown-item py-2" href="{{ route('language.switch', 'en-SA') }}">English - SA</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('language.switch', 'ar-SA') }}">العربية - السعودية</a></li>
                        <li><hr class="dropdown-divider opacity-50"></li>
                        <li><a class="dropdown-item py-2" href="{{ route('language.switch', 'en-EG') }}">English - EG</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('language.switch', 'ar-EG') }}">العربية - مصر</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>