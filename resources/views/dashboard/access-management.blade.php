@extends('layouts.customer.dashboard')

@section('title', 'Access Management | EL-Sawady')

@section('content')
    @php
        $activeLocale = $currentLocale ?? session('locale_full', 'en-SA');
    @endphp

    <div class="dashboard-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="dashboard-title mb-1">Roles & Permissions Management</h1>
                <p class="dashboard-desc mb-0">Create roles, create permissions, and map permissions to each role.</p>
            </div>
            <a href="{{ route('superadmin.users.index', ['locale' => $activeLocale]) }}" class="btn btn-outline-white">
                Open User Management
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
                    <h3 class="chart-title mb-3">Create New Role</h3>
                    <form method="POST" action="{{ route('superadmin.access-management.roles.store', ['locale' => $activeLocale]) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="role_name" class="form-label fw-semibold">Role Name</label>
                            <input type="text" id="role_name" name="name" class="form-control" placeholder="Manager" required>
                            <small class="text-muted">Use letters/numbers, start with a letter.</small>
                        </div>
                        <button type="submit" class="btn btn-primary-green">Create Role</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="chart-card h-100">
                    <h3 class="chart-title mb-3">Create New Permission</h3>
                    <form method="POST" action="{{ route('superadmin.access-management.permissions.store', ['locale' => $activeLocale]) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="permission_name" class="form-label fw-semibold">Permission Name</label>
                            <input type="text" id="permission_name" name="name" class="form-control" placeholder="reports.view" required>
                            <small class="text-muted">Recommended format: module.action (example: users.manage).</small>
                        </div>
                        <button type="submit" class="btn btn-primary-green">Create Permission</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="chart-card mb-4">
            <h3 class="chart-title mb-3">Available Permissions</h3>
            <div class="d-flex flex-wrap gap-2">
                @forelse ($permissions as $permission)
                    <span class="badge badge-dry">{{ $permission->name }}</span>
                @empty
                    <span class="text-muted">No permissions found.</span>
                @endforelse
            </div>
        </div>

        <div class="table-container">
            <div class="p-4 border-bottom">
                <h3 class="chart-title mb-0">Role Permission Mapping</h3>
            </div>
            <div class="table-responsive">
                <table class="table registry-table mb-0">
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th>Assigned Permissions</th>
                            <th class="text-end">Actions</th>
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
                                            <span class="text-muted">No permissions assigned</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-white" data-bs-toggle="collapse"
                                        data-bs-target="#role-permissions-{{ $role->id }}" aria-expanded="false">
                                        Edit Mapping
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
                                                            {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                                        <span class="checkbox-text">{{ $permission->name }}</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="submit" class="btn btn-primary-green btn-sm">Save Permissions</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">No roles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
