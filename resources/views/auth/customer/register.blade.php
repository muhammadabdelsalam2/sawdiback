@extends('layouts.landing')

@section('title', 'Home - ' . 'EL-Sawady')

@section('content')
    <style>
        /* Fix for Bootstrap Validation Icons in Floating Labels & RTL */
        .form-floating>.form-control:validated,
        .form-floating>.form-control.is-invalid {
            padding-inline-end: calc(1.5em + 0.75rem) !important;
        }

        [dir="rtl"] .form-control {
            background-position: left calc(0.375em + 0.1875rem) center !important;
        }

        /* Ensure the icon doesn't hide behind our custom background */
        .login-input {
            border-radius: 12px !important;
            border: 1px solid #eee !important;
            background-color: #fcfcfc !important;
        }

        /* Smooth transition for validation states */
        .was-validated .form-control:valid {
            border-color: #198754 !important;
        }

        .was-validated .form-control:invalid {
            border-color: #dc3545 !important;
        }
    </style>

    <div class="container py-5" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
        <div class="row justify-content-center">
            <div class="col-md-11 col-lg-10">
                <div class="card border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
                    <div class="row g-0">
                        <div class="col-md-5 d-none d-md-block position-relative">
                            <div class="h-100 w-100 p-5 d-flex flex-column justify-content-between text-white"
                                style="background: linear-gradient(135deg, rgba(45, 90, 39, 0.95) 0%, rgba(20, 40, 18, 0.9) 100%); min-height: 600px;">
                                <div>
                                    <div class="d-flex align-items-center mb-4">
                                        <div
                                            class="bg-white rounded p-2 {{ app()->getLocale() == 'ar' ? 'ms-2' : 'me-2' }}">
                                            <img width="50px" src="{{ asset('assets/images/svg.png')}}" alt="Logo">
                                        </div>
                                        <span class="fs-5 fw-bold">EL-SAWADY ERP</span>
                                    </div>
                                    <h2 class="display-6 fw-bold text-white">{{ __('auth.side_title') }}</h2>
                                    <p class="lead opacity-75 mt-3">{{ __('auth.side_text') }}</p>

                                    <ul class="list-unstyled mt-5">
                                        @foreach(['trial_features', 'no_credit_card', 'onboarding'] as $feature)
                                            <li class="mb-3 d-flex align-items-center">
                                                <i
                                                    class="bi bi-check-circle {{ app()->getLocale() == 'ar' ? 'ms-2' : 'me-2' }}"></i>
                                                {{ __("auth.$feature") }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="small opacity-50">{{ __('auth.trusted_by') }}</div>
                            </div>
                        </div>

                        <div class="col-md-7 bg-white p-4 p-lg-5">
                            <div class="mb-4">
                                <h3 class="fw-bold text-dark">{{ __('auth.register_title') }}</h3>
                                <p class="text-muted">{{ __('auth.register_subtitle') }}</p>
                            </div>

                            <form action="{{ route('auth.register', $currentLocale) }}" method="POST"
                                class="needs-validation" novalidate>
                                @csrf

                                <div class="form-floating mb-3">
                                    <input type="text" name="name" id="name"
                                        class="form-control login-input @error('name') is-invalid @enderror"
                                        placeholder="{{ __('auth.full_name') }}" value="{{ old('name') }}" required>
                                    <label for="name">{{ __('auth.full_name') }}</label>
                                    <div class="invalid-feedback">
                                        {{ __('auth.validation_required', ['field' => __('auth.full_name')]) }}
                                    </div>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="email" name="email" id="email"
                                        class="form-control login-input @error('email') is-invalid @enderror"
                                        placeholder="name@example.com" value="{{ old('email') }}" required>
                                    <label for="email">{{ __('auth.business_email') }}</label>
                                    <div class="invalid-feedback">
                                        {{ __('auth.validation_required', ['field' => __('auth.business_email')]) }}
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="password" name="password" id="password"
                                                class="form-control login-input @error('password') is-invalid @enderror"
                                                placeholder="{{ __('auth.password') }}" required minlength="8">
                                            <label for="password">{{ __('auth.password') }}</label>
                                            <div class="invalid-feedback">
                                                {{ __('auth.validation_required', ['field' => __('auth.password')]) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="password" name="password_confirmation" id="password_confirm"
                                                class="form-control login-input"
                                                placeholder="{{ __('auth.confirm_password') }}" required>
                                            <label for="password_confirm">{{ __('auth.confirm_password') }}</label>
                                            <div class="invalid-feedback">
                                                {{ __('auth.validation_required', ['field' => __('auth.confirm_password')]) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input @error('terms') is-invalid @enderror"
                                            id="terms" name="terms" required>

                                        <label class="form-check-label small text-muted" for="terms">
                                            {{ __('auth.agree_to') }}
                                            <a href="{{ route('terms.show', app()->getLocale()) }}"
                                                class="text-dark fw-bold">{{ __('auth.terms') }}</a>
                                            {{ __('auth.and') }}
                                            <a href="{{ route('privacy.show', app()->getLocale()) }}"
                                                class="text-dark fw-bold">{{ __('auth.privacy') }}</a>.
                                        </label>

                                        <div class="invalid-feedback">
                                            {{ __('auth.must_agree') }}
                                        </div>

                                        @error('terms')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-farm w-100 py-3 mb-3 shadow-sm"
                                    style="background-color: #2D5A27; color: white; border-radius: 12px; font-weight: 600;">
                                    {{ __('auth.submit_btn') }}
                                </button>
                            </form>

                            <div class="text-center mt-4">
                                <p class="small text-muted">{{ __('auth.already_have_account') }}
                                    <a href="{{ route('login.form', $currentLocale) }}" class="fw-bold text-decoration-none"
                                        style="color: #2D5A27;">
                                        {{ __('auth.sign_in') }}
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
@endsection