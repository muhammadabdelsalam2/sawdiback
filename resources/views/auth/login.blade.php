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
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="#2D5A27" class="bi bi-patch-check-fill" viewBox="0 0 16 16">
                                            <path
                                                d="M10.067.87a2.89 2.89 0 0 0-4.134 0l-.622.638-.89-.011a2.89 2.89 0 0 0-2.924 2.924l.01.89-.636.622a2.89 2.89 0 0 0 0 4.134l.637.622-.011.89a2.89 2.89 0 0 0 2.924 2.924l.89-.01.622.636a2.89 2.89 0 0 0 4.134 0l.622-.637.89.011a2.89 2.89 0 0 0 2.924-2.924l-.01-.89.636-.622a2.89 2.89 0 0 0 0-4.134l-.637-.622.011-.89a2.89 2.89 0 0 0-2.924-2.924l-.89.01-.622-.636z" />
                                        </svg>
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

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection