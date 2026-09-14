@extends('layouts.customer.dashboard')

@section('title', __('hr.annual_leave_alerts'))

@section('content')
@php
    $currentLocale = app()->getLocale();
@endphp

<div class="container-fluid my-4">
    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h2 class="h3 font-weight-bold text-gray-800 mb-1">
                <i class="fas fa-calendar-alt text-warning mr-2"></i> {{ __('hr.annual_leave_alerts') }}
            </h2>
            <p class="text-muted mb-0">
                {{ __('hr.annual_leave_alerts_desc', ['days' => $thresholdDays]) }}
            </p>
        </div>
        <div>
            <a href="{{ route('customer.hr.employees.index', ['locale' => $currentLocale]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> {{ __('hr.actions.back') }}
            </a>
        </div>
    </div>

    {{-- Alerts Table Card --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('hr.fields.worker_number') }}</th>
                            <th>{{ __('hr.fields.employee') }}</th>
                            <th>{{ __('hr.fields.department') }}</th>
                            <th>{{ __('hr.fields.job_title') }}</th>
                            <th>{{ __('hr.next_annual_leave_date') }}</th>
                            <th>{{ __('hr.replacement_employee') }}</th>
                            <th>{{ __('hr.fields.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($approachingLeaves as $employee)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="font-weight-bold">{{ $employee->worker_number ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('customer.hr.employees.show', ['locale' => $currentLocale, 'employee' => $employee->id]) }}" class="font-weight-bold text-primary">
                                        {{ $employee->full_name }}
                                    </a>
                                </td>
                                <td>{{ $employee->department->name ?? '-' }}</td>
                                <td>{{ $employee->jobTitle->name ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-warning px-2 py-1 font-weight-bold" style="font-size: 0.9rem;">
                                        {{ $employee->next_annual_leave_date ? $employee->next_annual_leave_date->format('Y-m-d') : '-' }}
                                    </span>
                                </td>
                                <td>
                                    @if($employee->replacementEmployee)
                                        <span class="badge badge-success">{{ $employee->replacementEmployee->full_name }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ __('hr.no_replacement_set') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('customer.hr.employees.show', ['locale' => $currentLocale, 'employee' => $employee->id]) }}" class="btn btn-sm btn-outline-primary" title="{{ __('hr.actions.view') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('customer.hr.employees.edit', ['locale' => $currentLocale, 'employee' => $employee->id]) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('hr.actions.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-4 text-muted">
                                    {{ __('hr.no_upcoming_leaves', ['days' => $thresholdDays]) }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection