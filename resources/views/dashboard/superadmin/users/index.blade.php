@extends('layouts.customer.dashboard')

@section('title', __('dashboard.users.title') . ' | EL-Sawady')

@section('content')
    @php
        $activeLocale = $currentLocale ?? session('locale_full', 'en-SA');
    @endphp

    <div class="dashboard-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="dashboard-title mb-1">{{ __('dashboard.users.title') }}</h1>
                <p class="dashboard-desc mb-0">{{ __('dashboard.users.desc') }}</p>
            </div>
            <a href="{{ route('superadmin.users.create', ['locale' => $activeLocale]) }}" class="btn btn-primary-green">
                <i class="fa-solid fa-plus me-2"></i>{{ __('dashboard.users.create') }}
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="table-container">
            <div class="table-responsive">
                <table class="table registry-table mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('dashboard.users.id') }}</th>
                            <th>{{ __('dashboard.users.name') }}</th>
                            <th>{{ __('dashboard.users.email') }}</th>
                            <th>{{ __('dashboard.users.roles') }}</th>
                            <th>{{ __('dashboard.users.direct_permissions') }}</th>
                            <th class="text-end">{{ __('dashboard.users.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td class="animal-id">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @forelse ($user->roles as $role)
                                        <span class="badge badge-lactating me-1">{{ $role->name }}</span>
                                    @empty
                                        <span class="text-muted">{{ __('dashboard.users.no_role') }}</span>
                                    @endforelse
                                </td>
                                <td>
                                    @forelse ($user->permissions as $permission)
                                        <span class="badge badge-dry me-1">{{ $permission->name }}</span>
                                    @empty
                                        <span class="text-muted">{{ __('dashboard.users.no_direct_permission') }}</span>
                                    @endforelse
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('superadmin.users.edit', ['locale' => $activeLocale, 'user' => $user]) }}" class="btn btn-sm btn-outline-white me-1">
                                        {{ __('dashboard.users.edit') }}
                                    </a>
                                    <form
                                        action="{{ route('superadmin.users.destroy', ['locale' => $activeLocale, 'user' => $user]) }}"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('{{ __('dashboard.users.confirm_delete') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">{{ __('dashboard.users.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">{{ __('dashboard.users.no_users') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $users->links() }}
        </div>
    </div>
@endsection
