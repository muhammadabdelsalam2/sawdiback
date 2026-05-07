@extends('layouts.customer.dashboard')

@section('title', __('account.password.title') . ' | EL-Sawady')

@section('content')
    <div class="dashboard-body">
        <div class="chart-card">
            <div class="chart-card-header">
                <div>
                    <h3>{{ __('account.password.title') }}</h3>
                    <p>{{ __('account.password.subtitle') }}</p>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success mb-3">{{ session('success') }}</div>
            @endif

            <form class="needs-validation" method="POST"
                action="{{ route('superadmin.password.update', ['locale' => $activeLocale]) }}" novalidate>
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('account.password.current') }}</label>
                        <div class="input-group">
                            <input type="password" name="current_password"
                                class="form-control @error('current_password') is-invalid @enderror" required
                                id="currentPassword">
                            <button class="btn btn-outline-white" type="button" data-toggle-password="#currentPassword">
                                {{ __('account.password.toggle_show') }}
                            </button>
                        </div>
                        @error('current_password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('account.password.new') }}</label>
                        <div class="input-group">
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" required id="newPassword">
                            <button class="btn btn-outline-white" type="button" data-toggle-password="#newPassword">
                                {{ __('account.password.toggle_show') }}
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('account.password.confirm') }}</label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation"
                                class="form-control @error('password_confirmation') is-invalid @enderror" required
                                id="confirmPassword">
                            <button class="btn btn-outline-white" type="button" data-toggle-password="#confirmPassword">
                                {{ __('account.password.toggle_show') }}
                            </button>
                        </div>
                        @error('password_confirmation')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button class="btn btn-primary-green" type="submit">
                        {{ __('account.password.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('[data-toggle-password]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const sel = btn.getAttribute('data-toggle-password');
                    const input = document.querySelector(sel);
                    if (!input) return;
                    const isPassword = input.type === 'password';
                    input.type = isPassword ? 'text' : 'password';
                    btn.textContent = isPassword ? @json(__('account.password.toggle_hide')) : @json(__('account.password.toggle_show'));
                });
            });
        </script>
    @endpush
@endsection

