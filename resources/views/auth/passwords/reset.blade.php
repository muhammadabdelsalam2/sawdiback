@extends('layouts.landing')

@section('title', __('auth.set_new_password_title') . ' | ' . __('auth.brand_name'))

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card login-card">
                    <div class="bg-white p-4 p-lg-5">
                        <div class="mb-4 text-center">
                            <span class="brand-mark d-inline-flex mb-4">
                                <img src="{{ asset('assets/images/logo3.jpeg') }}" alt="{{ __('auth.brand_name') }}">
                            </span>
                            <h3 class="fw-bold text-dark">{{ __('auth.set_new_password_title') }}</h3>
                            <p class="text-muted">{{ __('auth.set_new_password_subtitle') }}</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger small">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.update', ['locale' => $activeLocale]) }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary">
                                    {{ __('auth.email_label') }}
                                </label>
                                <input type="email" name="email" value="{{ old('email', $email) }}"
                                    class="form-control @error('email') is-invalid @enderror" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary">
                                    {{ __('auth.new_password_label') }}
                                </label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-secondary">
                                    {{ __('auth.confirm_password') }}
                                </label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-brand w-100">
                                {{ __('auth.reset_password_button') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
