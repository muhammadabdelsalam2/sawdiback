@extends('layouts.customer.dashboard')

@section('title', __('account.profile.title') . ' | ' . __('auth.brand_name'))

@section('content')
    <div class="dashboard-body">
        <div class="chart-card">
            <div class="chart-card-header">
                <div>
                    <h3>{{ __('account.profile.title') }}</h3>
                    <p>{{ __('account.profile.subtitle') }}</p>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success mb-3">{{ session('success') }}</div>
            @endif

            <form class="needs-validation" method="POST"
                action="{{ route('customer.profile.update', ['locale' => $activeLocale]) }}"
                enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-lg-4">
                        <div class="detail-section-card p-4 h-100">
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <div class="user-avatar-wrapper">
                                    @php
                                        $avatar = $user?->avatar;
                                        $avatarUrl = $avatar
                                            ? \App\Support\PublicFileUrl::url($avatar)
                                            : asset('assets/images/user.png');
                                    @endphp
                                    <img id="avatarPreview" src="{{ $avatarUrl }}" alt="{{ $user->name }}"
                                        class="user-avatar-enhanced">
                                </div>

                                <div class="min-w-0">
                                    <div class="fw-bold">{{ $user->name }}</div>
                                    <div class="text-muted small">{{ $user->email }}</div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-grid gap-2">
                                <label class="form-label mb-1">{{ __('account.profile.avatar') }}</label>
                                <input class="form-control @error('avatar') is-invalid @enderror" type="file"
                                    name="avatar" accept="image/*" id="avatarInput">
                                <div class="text-muted small">{{ __('account.profile.avatar_hint') }}</div>
                                @error('avatar')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-4">

                            <div class="d-grid gap-2">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">{{ __('account.profile.role') }}</span>
                                    <span class="fw-bold">{{ $user->getRoleNames()->first() ?? '-' }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">{{ __('account.profile.created_at') }}</span>
                                    <span class="fw-bold">{{ optional($user->created_at)->format('Y-m-d') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="detail-section-card p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('account.profile.name') }}</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                        class="form-control @error('name') is-invalid @enderror" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('account.profile.email') }}</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                        class="form-control @error('email') is-invalid @enderror" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('account.profile.phone') }}</label>
                                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                        class="form-control @error('phone') is-invalid @enderror">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">{{ __('account.settings.language') }}</label>
                                    <select name="preferred_language" class="form-select @error('preferred_language') is-invalid @enderror">
                                        <option value="en" @selected(old('preferred_language', $user->preferred_language) === 'en')>{{ __('account.settings.language_en') }}</option>
                                        <option value="ar" @selected(old('preferred_language', $user->preferred_language) === 'ar')>{{ __('account.settings.language_ar') }}</option>
                                    </select>
                                    @error('preferred_language')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
                                <a href="{{ route('customer.password.show', ['locale' => $activeLocale]) }}"
                                    class="btn btn-outline-white">
                                    <i class="bi bi-key"></i>
                                    {{ __('dashboard.navbar.change_password') }}
                                </a>
                                <button class="btn btn-primary-green" type="submit">
                                    {{ __('account.profile.save') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const input = document.getElementById('avatarInput');
                const preview = document.getElementById('avatarPreview');
                if (!input || !preview) return;
                input.addEventListener('change', function() {
                    const file = input.files && input.files[0];
                    if (!file) return;
                    preview.src = URL.createObjectURL(file);
                });
            });
        </script>
    @endpush
@endsection
