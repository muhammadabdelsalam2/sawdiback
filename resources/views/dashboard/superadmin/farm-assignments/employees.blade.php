@extends('layouts.customer.dashboard')

@section('title', __('superadmin.farm_assignments.employees_title') . ' | EL-Sawady')

@section('content')
    @php($activeLocale = $currentLocale ?? session('locale_full', 'en-SA'))

    <div class="dashboard-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h1 class="dashboard-title mb-1">{{ __('superadmin.farm_assignments.employees_title') }}</h1>
                <p class="dashboard-desc mb-0">{{ __('superadmin.farm_assignments.employees_desc') }}</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-container">
            <div class="table-responsive">
                <table class="table registry-table mb-0 no-datatable">
                    <thead>
                        <tr>
                            <th>{{ __('hr.fields.id') }}</th>
                            <th>{{ __('hr.fields.full_name') }}</th>
                            <th>{{ __('hr.fields.phone') }}</th>
                            <th>{{ __('hr.fields.farm') }}</th>
                            <th class="text-end">{{ __('hr.fields.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $employee)
                            <tr>
                                <td>{{ $employee->id }}</td>
                                <td>{{ $employee->full_name }}</td>
                                <td>{{ $employee->phone ?? '-' }}</td>
                                <td>
                                    <form id="assign-employee-{{ $employee->id }}" method="POST" action="{{ route('superadmin.farm-assignments.employees.assign', ['locale' => $activeLocale, 'employee' => $employee]) }}">
                                        @csrf
                                        @method('PUT')
                                        <select name="farm_id" class="form-select" required>
                                            <option value="">{{ __('hr.options.select_farm') }}</option>
                                            @foreach(($farmsByTenant[$employee->tenant_id] ?? collect()) as $farm)
                                                <option value="{{ $farm->id }}">{{ $farm->name }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-primary-green" form="assign-employee-{{ $employee->id }}">
                                        {{ __('superadmin.farm_assignments.assign') }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">{{ __('superadmin.farm_assignments.no_unassigned_employees') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3 d-flex justify-content-end">
            {{ $employees->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
