@extends('layouts.customer.dashboard')

@php
    $locale = $currentLocale ?? request()->route('locale') ?? app()->getLocale();
    $isAr = str_starts_with($locale, 'ar');
@endphp

@section('title', $isAr ? 'التحليلات والمؤشرات العامة' : 'Analytics & Performance')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        {{-- Header --}}
        <div class="page-head mb-4">
            <div>
                <h2 class="page-title">
                    <i class="fas fa-chart-line text-success mr-2"></i>
                    {{ $isAr ? 'لوحة التحليلات والمؤشرات السريعة' : 'Analytics & Quick Indicators' }}
                </h2>
                <p class="text-muted small mb-0">
                    {{ $isAr ? 'نظرة شاملة على التنبيهات التشغيلية وحالة المزارع والقطاعات.' : 'Overview of operational alerts, farms, and sectors.' }}
                </p>
            </div>
        </div>

        {{-- بطاقات التنبيهات العلوية --}}
        <div class="row g-3 mb-4">
            {{-- تنبيه وثائق الموظفين --}}
            <div class="col-md-4 col-sm-6">
                <a href="{{ route('customer.hr.employees.document-alerts', ['locale' => $locale]) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm p-3 h-100 {{ ($expiringDocsCount ?? 0) > 0 ? 'border-start border-danger border-4' : '' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted d-block small">{{ $isAr ? 'وثائق موظفين تنتهي قريباً' : 'Expiring Documents' }}</span>
                                <h3 class="mb-0 {{ ($expiringDocsCount ?? 0) > 0 ? 'text-danger' : 'text-success' }} fw-bold">
                                    {{ $expiringDocsCount ?? 0 }}
                                </h3>
                            </div>
                            <div class="p-3 bg-light rounded-circle text-danger">
                                <i class="fas fa-id-card fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- تنبيه نواقص المخزون --}}
            <div class="col-md-4 col-sm-6">
                <a href="{{ route('customer.inventory.alerts.index', ['locale' => $locale]) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm p-3 h-100 {{ ($lowStockCount ?? 0) > 0 ? 'border-start border-warning border-4' : '' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted d-block small">{{ $isAr ? 'أصناف بلغت حد الطلب' : 'Low Stock Items' }}</span>
                                <h3 class="mb-0 {{ ($lowStockCount ?? 0) > 0 ? 'text-warning' : 'text-success' }} fw-bold">
                                    {{ $lowStockCount ?? 0 }}
                                </h3>
                            </div>
                            <div class="p-3 bg-light rounded-circle text-warning">
                                <i class="fas fa-boxes fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            {{-- بطاقة الاستزراع السمكي --}}
            <div class="col-md-4 col-sm-12">
                <a href="{{ route('customer.fisheries.index', ['locale' => $locale]) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm p-3 h-100">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted d-block small">{{ $isAr ? 'أحواض الأسماك النشطة' : 'Active Fish Ponds' }}</span>
                                <h3 class="mb-0 text-primary fw-bold">
                                    {{ $fishSummary['active_ponds'] ?? 0 }}
                                </h3>
                                <small class="text-muted">{{ number_format($fishSummary['live_fish'] ?? 0) }} {{ $isAr ? 'سمكة حية' : 'live fish' }}</small>
                            </div>
                            <div class="p-3 bg-light rounded-circle text-primary">
                                <i class="fas fa-fish fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- محتوى تقرير التحليلات السابق (إن وجد) --}}
        @if(isset($report))
            <div class="card-block">
                {{-- يعرض باقي جداول ورسومات التحليلات إن كانت موجودة مسبقاً --}}
            </div>
        @endif
    </div>
@endsection
