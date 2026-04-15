@extends('layouts.customer.dashboard')

@section('title', __('dashboard.access.title') . ' | EL-Sawady')

@section('content')
    @php
        $activeLocale = $currentLocale ?? session('locale_full', 'en-SA');
    @endphp

    <div class="dashboard-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="dashboard-title mb-1">{{ __('dashboard.access.title') }}</h1>
                <p class="dashboard-desc mb-0">{{ __('dashboard.access.desc') }}</p>
            </div>
            <a href="{{ route('superadmin.users.index', ['locale' => $activeLocale]) }}" class="btn btn-outline-white">
                {{ __('dashboard.access.open_users') }}
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="chart-card h-100">
                    <h3 class="chart-title mb-3">{{ __('dashboard.access.create_role') }}</h3>
                    <form method="POST" action="{{ route('superadmin.access-management.roles.store', ['locale' => $activeLocale]) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="role_name" class="form-label fw-semibold">{{ __('dashboard.access.role_name') }}</label>
                            <input type="text" id="role_name" name="name" class="form-control" placeholder="{{ __('dashboard.access.role_placeholder') }}" required>
                            <small class="text-muted">{{ __('dashboard.access.role_format_hint') }}</small>
                        </div>
                        <button type="submit" class="btn btn-primary-green">{{ __('dashboard.access.create_role_btn') }}</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="chart-card h-100">
                    <h3 class="chart-title mb-3">{{ __('dashboard.access.create_permission') }}</h3>
                    <form method="POST" action="{{ route('superadmin.access-management.permissions.store', ['locale' => $activeLocale]) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="permission_name" class="form-label fw-semibold">{{ __('dashboard.access.permission_name') }}</label>
                            <input type="text" id="permission_name" name="name" class="form-control" placeholder="{{ __('dashboard.access.perm_placeholder') }}" required>
                            <small class="text-muted">{{ __('dashboard.access.perm_format_hint') }}</small>
                        </div>
                        <button type="submit" class="btn btn-primary-green">{{ __('dashboard.access.create_permission_btn') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="chart-card mb-4">
            <h3 class="chart-title mb-3">{{ __('dashboard.access.available_permissions') }}</h3>
            <div class="d-flex flex-wrap gap-2">
                @forelse ($permissions as $permission)
                    <span class="badge badge-dry">{{ $permission->name }}</span>
                @empty
                    <span class="text-muted">{{ __('dashboard.access.no_permissions') }}</span>
                @endforelse
            </div>
        </div>

        <div class="table-container">
            <div class="p-4 border-bottom">
                <h3 class="chart-title mb-0">{{ __('dashboard.access.role_mapping') }}</h3>
            </div>
            <div class="table-responsive">
                <table class="table registry-table mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('dashboard.users.roles') }}</th>
                            <th>{{ __('dashboard.access.assigned_permissions') }}</th>
                            <th class="text-end">{{ __('dashboard.users.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr>
                                <td class="fw-semibold">{{ $role->name }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @forelse ($role->permissions as $permission)
                                            <span class="badge badge-lactating">{{ $permission->name }}</span>
                                        @empty
                                            <span class="text-muted">{{ __('dashboard.access.no_permissions_assigned') }}</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-white" data-bs-toggle="collapse"
                                        data-bs-target="#role-permissions-{{ $role->id }}" aria-expanded="false">
                                        {{ __('dashboard.access.edit_mapping') }}
                                    </button>
                                </td>
                            </tr>
                            <tr class="collapse" id="role-permissions-{{ $role->id }}">
                                <td colspan="3">
                                    <form method="POST"
                                        action="{{ route('superadmin.access-management.roles.permissions.update', ['locale' => $activeLocale, 'role' => $role]) }}"
                                        class="p-3">
                                        @csrf
                                        @method('PUT')
                                        <div class="row g-2 mb-3">
                                            @foreach ($permissions as $permission)
                                                <div class="col-md-3 col-sm-6">
                                                    <label class="custom-checkbox-label">
                                                        <input
                                                            type="checkbox"
                                                            name="permissions[]"
                                                            value="{{ $permission->name }}"
                                                            class="custom-checkbox"
                                                            {{ $role->permissions->contains('name', $permission->name) ? 'checked' : '' }}>
                                                        <span class="checkbox-text">{{ $permission->name }}</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="submit" class="btn btn-primary-green btn-sm">{{ __('dashboard.access.save_permissions') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">{{ __('dashboard.access.no_roles') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
