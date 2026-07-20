@extends('layouts.landing')

@section('title', __('auth.password_reset_title') . ' | ' . __('auth.brand_name'))

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card login-card">
                    <div class="bg-white p-4 p-lg-5">
                        <div class="mb-4 text-center">
                            <span class="brand-mark d-inline-flex mb-4">
                                <img src="{{ asset('assets/images/logo-full.png') }}" alt="{{ __('auth.brand_name') }}">
                            </span>
                            <h3 class="fw-bold text-dark">{{ __('auth.password_reset_title') }}</h3>
                            <p class="text-muted">{{ __('auth.password_reset_subtitle') }}</p>
                        </div>

                        @if (session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif

                        <form method="POST" action="{{ route('password.email', ['locale' => $activeLocale]) }}">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary">
                                    {{ __('auth.email_label') }}
                                </label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="{{ __('auth.email_placeholder') }}" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-brand w-100 mb-3">
                                {{ __('auth.send_password_reset_link') }}
                            </button>

                            <div class="text-center">
                                <a class="text-decoration-none small fw-bold text-success"
                                    href="{{ route('login.form', ['locale' => $activeLocale]) }}">
                                    {{ __('auth.back_to_login') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
