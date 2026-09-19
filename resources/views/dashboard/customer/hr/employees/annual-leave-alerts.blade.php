@extends('layouts.customer.dashboard')

@php
    $currentLocale = request()->route('locale') ?? app()->getLocale();
    $isAr = str_starts_with($currentLocale, 'ar');
@endphp

@section('title', $isAr ? 'تنبيهات الإجازات السنوية' : 'Annual Leave Alerts')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <div class="page-head mb-4">
            <div>
                <h2 class="page-title">
                    <i class="fas fa-calendar-alt text-success mr-2"></i>
                    {{ $isAr ? 'تنبيهات استحقاق الإجازات السنوية' : 'Annual Leave Due Alerts' }}
                </h2>
                <p class="text-muted small mb-0">
                    {{ $isAr ? 'قائمة الموظفين المستحقين لإجازتهم السنوية خلال الـ 60 يوماً القادمة.' : 'Employees due for annual leave within the next 60 days.' }}
                </p>
            </div>
            <div class="quick-actions">
                <a class="btn btn-outline-white" href="{{ route('customer.hr.employees.index', ['locale' => $currentLocale]) }}">
                    <i class="fas fa-arrow-left mr-1"></i> {{ $isAr ? 'سجل الموظفين' : 'Employees' }}
                </a>
                <a class="btn btn-primary-green" href="{{ route('customer.hr.leaves.create', ['locale' => $currentLocale]) }}">
                    <i class="fas fa-plus mr-1"></i> {{ $isAr ? 'تقديم طلب إجازة' : 'Request Leave' }}
                </a>
            </div>
        </div>

        <div class="table-container">
            <table class="table registry-table mb-0 align-middle">
                <thead>
                <tr>
                    <th>{{ $isAr ? 'الموظف' : 'Employee' }}</th>
                    <th>{{ $isAr ? 'القسم / المسمى' : 'Dept / Title' }}</th>
                    <th>{{ $isAr ? 'تاريخ التعيين' : 'Hire Date' }}</th>
                    <th>{{ $isAr ? 'موعد الاستحقاق القادم' : 'Due Date' }}</th>
                    <th>{{ $isAr ? 'الأيام المتبقية' : 'Days Remaining' }}</th>
                    <th>{{ $isAr ? 'الحالة' : 'Status' }}</th>
                    <th>{{ $isAr ? 'الإجراءات' : 'Actions' }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($employees as $emp)
                    <tr>
                        <td>
                            <strong>{{ $emp->name ?? ($emp->first_name . ' ' . $emp->last_name) }}</strong>
                            <small class="text-muted d-block">{{ $emp->employee_code ?? ('#' . $emp->id) }}</small>
                        </td>
                        <td>
                            <span>{{ $emp->department?->name ?? '-' }}</span>
                            <small class="text-muted d-block">{{ $emp->jobTitle?->name ?? '-' }}</small>
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($emp->hire_date)->format('Y-m-d') }}
                        </td>
                        <td>
                            <strong>{{ $emp->next_annual_leave_date->format('Y-m-d') }}</strong>
                        </td>
                        <td>
                        <span class="badge {{ $emp->days_until_leave <= 15 ? 'bg-danger' : ($emp->days_until_leave <= 30 ? 'bg-warning text-dark' : 'bg-success') }}">
                            {{ $emp->days_until_leave }} {{ $isAr ? 'يوم' : 'days' }}
                        </span>
                        </td>
                        <td>
                            @if($emp->days_until_leave <= 15)
                                <span class="text-danger font-weight-bold">
                                <i class="fas fa-exclamation-triangle mr-1"></i> {{ $isAr ? 'استحقاق وشيك' : 'Urgent' }}
                            </span>
                            @else
                                <span class="text-muted">{{ $isAr ? 'قادم' : 'Upcoming' }}</span>
                            @endif
                        </td>
                        <td class="d-flex gap-2">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('customer.hr.employees.show', ['locale' => $currentLocale, 'employee' => $emp->id]) }}">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a class="btn btn-sm btn-primary-green" href="{{ route('customer.hr.leaves.create', ['locale' => $currentLocale, 'employee_id' => $emp->id]) }}">
                                <i class="fas fa-calendar-plus"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle text-success fa-2x d-block mb-2"></i>
                            {{ $isAr ? 'لا توجد إجازات سنوية مستحقة خلال الـ 60 يوماً القادمة.' : 'No annual leaves are due within the next 60 days.' }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
