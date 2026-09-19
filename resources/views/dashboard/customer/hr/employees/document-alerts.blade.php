@extends('layouts.customer.dashboard')

@php
    $currentLocale = request()->route('locale') ?? app()->getLocale();
    $isAr = str_starts_with($currentLocale, 'ar');
@endphp

@section('title', $isAr ? 'تنبيهات انتهاء وثائق الموظفين' : 'Employee Document Expiry Alerts')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <div class="page-head mb-4">
            <div>
                <h2 class="page-title">
                    <i class="fas fa-id-card text-warning mr-2"></i>
                    {{ $isAr ? 'تنبيهات انتهاء الوثائق والعقود' : 'Document & Contract Expiry Alerts' }}
                </h2>
                <p class="text-muted small mb-0">
                    {{ $isAr ? 'متابعة الوثائق والهويات والعقود المنتهية أو التي توشك على الانتهاء خلال 60 يوماً.' : 'Track expired or expiring IDs, passports, and contracts within 60 days.' }}
                </p>
            </div>
            <div class="quick-actions">
                <a class="btn btn-outline-white" href="{{ route('customer.hr.employees.index', ['locale' => $currentLocale]) }}">
                    <i class="fas fa-arrow-left mr-1"></i> {{ $isAr ? 'سجل الموظفين' : 'Employees' }}
                </a>
                <a class="btn btn-outline-white" href="{{ route('customer.hr.employees.annual-leave-alerts', ['locale' => $currentLocale]) }}">
                    <i class="fas fa-calendar-alt mr-1"></i> {{ $isAr ? 'تنبيهات الإجازات' : 'Leave Alerts' }}
                </a>
            </div>
        </div>

        <div class="table-container">
            <table class="table registry-table mb-0 align-middle">
                <thead>
                <tr>
                    <th>{{ $isAr ? 'الموظف' : 'Employee' }}</th>
                    <th>{{ $isAr ? 'القسم / المسمى الوظيفي' : 'Dept / Title' }}</th>
                    <th>{{ $isAr ? 'نوع الوثيقة' : 'Document Type' }}</th>
                    <th>{{ $isAr ? 'رقم الوثيقة' : 'Document No.' }}</th>
                    <th>{{ $isAr ? 'تاريخ الانتهاء' : 'Expiry Date' }}</th>
                    <th>{{ $isAr ? 'الحالة والمتبقي' : 'Status & Remaining' }}</th>
                    <th>{{ $isAr ? 'الإجراءات' : 'Actions' }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($employees as $emp)
                    @foreach($emp->expiring_documents as $doc)
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
                            <span class="badge bg-light text-dark border">
                                {{ $isAr ? $doc['doc_name'] : $doc['doc_name_en'] }}
                            </span>
                            </td>
                            <td>
                                <code>{{ $doc['doc_number'] }}</code>
                            </td>
                            <td>
                                <strong>{{ $doc['expiry_date']->format('Y-m-d') }}</strong>
                            </td>
                            <td>
                                @if($doc['is_expired'])
                                    <span class="badge bg-danger">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    {{ $isAr ? 'منتهية منذ ' . abs($doc['days_remaining']) . ' يوم' : 'Expired ' . abs($doc['days_remaining']) . ' days ago' }}
                                </span>
                                @elseif($doc['days_remaining'] <= 15)
                                    <span class="badge bg-danger">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ $isAr ? 'متبقي ' . $doc['days_remaining'] . ' يوم (عاجل)' : $doc['days_remaining'] . ' days left (Urgent)' }}
                                </span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                    {{ $isAr ? 'متبقي ' . $doc['days_remaining'] . ' يوم' : $doc['days_remaining'] . ' days left' }}
                                </span>
                                @endif
                            </td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('customer.hr.employees.edit', ['locale' => $currentLocale, 'employee' => $emp->id]) }}">
                                    <i class="fas fa-edit"></i> {{ $isAr ? 'تحديث الوثيقة' : 'Update Doc' }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle text-success fa-2x d-block mb-2"></i>
                            {{ $isAr ? 'جميع وثائق وعقود الموظفين سارية ولا توجد وثائق منتهية قريباً.' : 'All employee documents and contracts are valid.' }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
