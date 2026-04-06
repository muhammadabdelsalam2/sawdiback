@extends('layouts.landing')

@section('title', 'Home' . 'EL-Sawady')

@section('content')
 <div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-9">
            <div class="card login-card">
                <div class="row g-0">

                    <!-- Left Side Overlay -->
                    <div class="col-md-5 d-none d-md-block bg-farm-green position-relative">
                        <div
                            class="side-image-overlay h-100 w-100 p-5 d-flex flex-column justify-content-between text-white">
                            <div>
                                <div class="d-flex align-items-center mb-4">
                                    <div class="bg-white rounded p-2 me-2">
                                       <img width="50px" src="{{ asset('assets/images/svg.png')}}" alt="This Is Main LOGO">
                                    </div>
                                    <span class="farm-logo-text fs-5">
                                        {{ __('auth.farm_name') }}
                                    </span>
                                </div>

                                <h2 class="display-6 fw-bold">
                                    {{ __('auth.hero_title') }}
                                </h2>

                                <p class="lead opacity-75 mt-3">
                                    {{ __('auth.hero_text') }}
                                </p>
                            </div>

                            <div class="small opacity-50">
                                &copy; 2026 {{ __('auth.system_name') }} v3.0
                            </div>
                        </div>
                    </div>

                    <!-- Right Side Form -->
                    <div class="col-md-7 bg-white p-4 p-lg-5">

                        <div class="mb-5">
                            <h3 class="fw-bold text-dark">
                                {{ __('auth.sign_in') }}
                            </h3>
                            <p class="text-muted">
                                {{ __('auth.sign_in_subtitle') }}
                            </p>
                        </div>

                        {{-- Global validation errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger small">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form
                            action="{{ route('login.submit', ['locale' => session('locale_full', 'en-SA')]) }}"
                            method="POST">
                            @csrf

                            {{-- Email --}}
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary">
                                    {{ __('auth.email_label') }}
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="{{ __('auth.email_placeholder') }}"
                                    required>

                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label small fw-bold text-secondary">
                                        {{ __('auth.password_label') }}
                                    </label>

                                    <a href="#"
                                        class="text-decoration-none small fw-bold text-success">
                                        {{ __('auth.forgot_password') }}
                                    </a>
                                </div>

                                <input
                                    type="password"
                                    name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="••••••••"
                                    required>

                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Remember --}}
                            <div class="mb-4 form-check">
                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    id="remember"
                                    name="remember"
                                    {{ old('remember') ? 'checked' : '' }}>

                                <label class="form-check-label small text-muted" for="remember">
                                    {{ __('auth.remember_device') }}
                                </label>
                            </div>

                            {{-- Submit --}}
                            <button type="submit" class="btn btn-farm w-100 mb-3">
                                {{ __('auth.login_button') }}
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <p class="small text-muted">
                                {{ __('auth.support_text') }}
                                <a href="#" class="text-dark fw-bold text-decoration-none">
                                    {{ __('auth.contact_it') }}
                                </a>
                            </p>
                        </div>

                        <div class="mt-4">
                            <div class="d-flex align-items-center my-4">
                                <div class="flex-grow-1"><hr></div>
                                <div class="px-3 text-muted small fw-bold text-uppercase">
                                    {{ __('auth.social_login_divider') }}
                                </div>
                                <div class="flex-grow-1"><hr></div>
                            </div>

                            <div class="row g-2">
                                <div class="col-4">
                                    <a href="{{ route('social.redirect', ['locale' => session('locale_full', 'en-SA'), 'provider' => 'google']) }}" class="btn btn-outline-light border text-dark w-100 py-2 d-flex align-items-center justify-content-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48">
                                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24s.92 7.54 2.56 10.78l7.97-6.19z"/>
                                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                                            <path fill="none" d="M0 0h48v48H0z"/>
                                        </svg>
                                    </a>
                                </div>
                                <div class="col-4">
                                    <a href="{{ route('social.redirect', ['locale' => session('locale_full', 'en-SA'), 'provider' => 'facebook']) }}" class="btn btn-outline-light border text-dark w-100 py-2 d-flex align-items-center justify-content-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#1877F2" viewBox="0 0 24 24">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                    </a>
                                </div>
                                <div class="col-4">
                                    <a href="{{ route('social.redirect', ['locale' => session('locale_full', 'en-SA'), 'provider' => 'instagram']) }}" class="btn btn-outline-light border text-dark w-100 py-2 d-flex align-items-center justify-content-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                                            <defs>
                                                <radialGradient id="rg" r="150%" cx="30%" cy="150%">
                                                    <stop stop-color="#fed373" offset="0"></stop>
                                                    <stop stop-color="#f15245" offset="0.3"></stop>
                                                    <stop stop-color="#d92e7f" offset="0.6"></stop>
                                                    <stop stop-color="#9b36b7" offset="0.9"></stop>
                                                    <stop stop-color="#515ecf" offset="1"></stop>
                                                </radialGradient>
                                            </defs>
                                            <path fill="url(#rg)" d="M12 2.163c3.204 0 3.584.012 4.85.07 1.17.054 1.805.249 2.227.412.56.216.96.474 1.38.894.42.42.678.82.894 1.38.163.422.358 1.057.412 2.227.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.054 1.17-.249 1.805-.412 2.227-.216.56-.474.96-.894 1.38-.42.42-.82.678-1.38.894-.422.163-1.057.358-2.227.412-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.17-.054-1.805-.249-2.227-.412-.56-.216-.96-.474-1.38-.894-.42-.42-.678-.82-.894-1.38-.163-.422-.358-1.057-.412-2.227-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.054-1.17.249-1.805.412-2.227.216-.56.474-.96.894-1.38.42-.42.82-.678 1.38-.894.422-.163 1.057-.358 2.227-.412 1.266-.058 1.646-.07 4.85-.07zM12 0C8.741 0 8.333.014 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.741 0 12s.014 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126s1.384 1.078 2.126 1.384c.766.296 1.636.499 2.913.558C8.333 23.986 8.741 24 12 24s3.667-.014 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384s1.078-1.384 1.384-2.126c.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.014-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126s-1.384-1.078-2.126-1.384c-.765-.296-1.636-.499-2.913-.558C15.667.012 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection