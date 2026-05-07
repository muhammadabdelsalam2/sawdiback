@extends('layouts.customer.dashboard')

@section('title', __('account.settings.title') . ' | EL-Sawady')

@section('content')
    <div class="dashboard-body">
        <div class="chart-card">
            <div class="chart-card-header">
                <div>
                    <h3>{{ __('account.settings.title') }}</h3>
                    <p>{{ __('account.settings.subtitle') }}</p>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success mb-3">{{ session('success') }}</div>
            @endif

            <form class="needs-validation" method="POST"
                action="{{ route('superadmin.settings.update', ['locale' => $activeLocale]) }}"
                enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="detail-section-card p-4 h-100">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="fw-bold">{{ __('account.settings.language') }}</div>
                            </div>
                            <select name="preferred_language" class="form-select">
                                <option value="en" @selected(old('preferred_language', $user->preferred_language) === 'en')>English</option>
                                <option value="ar" @selected(old('preferred_language', $user->preferred_language) === 'ar')>العربية</option>
                            </select>
                            @error('preferred_language')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="detail-section-card p-4 h-100">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="fw-bold">{{ __('account.settings.appearance') }}</div>
                            </div>
                            <select name="appearance_mode" class="form-select">
                                <option value="light" @selected(old('appearance_mode', $user->appearance_mode) === 'light')>{{ __('account.settings.appearance_light') }}</option>
                                <option value="dark" @selected(old('appearance_mode', $user->appearance_mode) === 'dark')>{{ __('account.settings.appearance_dark') }}</option>
                                <option value="system" @selected(old('appearance_mode', $user->appearance_mode) === 'system')>{{ __('account.settings.appearance_system') }}</option>
                            </select>
                            @error('appearance_mode')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror

                            <div class="text-muted small mt-3">
                                {{ __('dashboard.navbar.dark_mode') }}
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="detail-section-card p-4">
                            <div class="fw-bold mb-2">{{ __('account.profile.avatar') }}</div>
                            @php
                                $avatar = $user?->avatar;
                                $avatarUrl = $avatar
                                    ? (filter_var($avatar, FILTER_VALIDATE_URL) ? $avatar : asset('storage/' . ltrim($avatar, '/')))
                                    : asset('assets/images/user.png');
                            @endphp
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <img id="avatarPreviewSettings" src="{{ $avatarUrl }}" alt="{{ $user->name }}"
                                    class="user-avatar-enhanced">
                                <div class="flex-grow-1">
                                    <input class="form-control @error('avatar') is-invalid @enderror" type="file"
                                        name="avatar" accept="image/*" id="avatarInputSettings">
                                    <div class="text-muted small mt-2">{{ __('account.profile.avatar_hint') }}</div>
                                    @error('avatar')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button class="btn btn-primary-green" type="submit">
                        {{ __('account.settings.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const input = document.getElementById('avatarInputSettings');
                const preview = document.getElementById('avatarPreviewSettings');
                if (!input || !preview) return;
                input.addEventListener('change', function() {
                    const file = input.files && input.files[0];
                    if (!file) return;
                    const url = URL.createObjectURL(file);
                    preview.src = url;
                });
            });
        </script>
    @endpush
@endsection

