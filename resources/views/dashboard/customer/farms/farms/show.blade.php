@extends('layouts.customer.dashboard')

@section('title', __('farms.titles.farm_details') ?? 'تفاصيل المزرعة')

@push('styles')
    <style>
        .farm-show-page .card-block {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            border: 1px solid #eef2f5;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .farm-show-page .info-pill {
            background: #f8fafc;
            border-radius: 8px;
            padding: 10px 14px;
            border: 1px solid #f1f5f9;
            margin-bottom: 8px;
        }
        .farm-show-page .badge-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
@php
    $currentLocale = request()->route('locale') ?? app()->getLocale();
@endphp

<div class="container-fluid my-4 farm-show-page">
    {{-- الهيدر الرئيسي --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h2 class="h3 font-weight-bold text-gray-800 mb-1">
                {{ $farm->name }}
                @if($farm->code)
                    <span class="text-muted small">({{ $farm->code }})</span>
                @endif
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('customer.farms.index', ['locale' => $currentLocale]) }}">{{ __('farms.titles.farms') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $farm->name }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('customer.farms.edit', ['locale' => $currentLocale, 'farm' => $farm->id]) }}" class="btn btn-outline-primary font-weight-bold">
                <i class="fas fa-edit mr-1"></i> {{ __('farms.actions.edit') }}
            </a>
            <a href="{{ route('customer.farms.index', ['locale' => $currentLocale]) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> {{ __('farms.actions.back_to_list') ?? 'رجوع للقائمة' }}
            </a>
        </div>
    </div>

    {{-- بيانات المزرعة الأساسية --}}
    <div class="card-block">
        <h6 class="font-weight-bold text-muted mb-3 border-bottom pb-2">
            <i class="fas fa-info-circle mr-1 text-primary"></i> بيانات المزرعة
        </h6>
        <div class="row g-2">
            <div class="col-md-3 col-sm-6">
                <div class="info-pill">
                    <span class="text-muted d-block small">{{ __('farms.fields.name') }}</span>
                    <strong>{{ $farm->name }}</strong>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-pill">
                    <span class="text-muted d-block small">{{ __('farms.fields.code') }}</span>
                    <strong>{{ $farm->code ?? '-' }}</strong>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-pill">
                    <span class="text-muted d-block small">{{ __('farms.fields.ownership_type') ?? 'نوع الملكية' }}</span>
                    <strong>{{ $farm->ownership_type ? ($farm->ownership_type === 'owned' ? 'ملك' : 'إيجار') : '-' }}</strong>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-pill">
                    <span class="text-muted d-block small">{{ __('farms.fields.location') }}</span>
                    <strong>{{ $farm->location ?? '-' }}</strong>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-pill">
                    <span class="text-muted d-block small">{{ __('farms.fields.area_sqm') }}</span>
                    <strong>{{ $farm->area_sqm ? number_format($farm->area_sqm, 2) . ' م²' : '-' }}</strong>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-pill">
                    <span class="text-muted d-block small">{{ __('farms.fields.is_active') }}</span>
                    @if($farm->is_active)
                        <span class="badge bg-success-subtle text-success border border-success-subtle badge-status">نشطة</span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle badge-status">غير نشطة</span>
                    @endif
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="info-pill">
                    <span class="text-muted d-block small">{{ __('farms.fields.notes') }}</span>
                    <span>{{ $farm->notes ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- جدول الحظائر التابعة للمزرعة --}}
    <div class="card-block">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="font-weight-bold text-muted mb-0">
                <i class="fas fa-warehouse mr-1 text-success"></i> الحظائر التابعة ({{ $farm->pens->count() }})
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered text-center mb-0">
                <thead class="table-light">
                    <tr>
                        <th>رقم الحظيرة</th>
                        <th>اسم الحظيرة</th>
                        <th>النوع</th>
                        <th>السعة الاستيعابية</th>
                        <th>العدد الحالي للحيوانات</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($farm->pens as $pen)
                        <tr>
                            <td class="font-weight-bold">{{ $pen->pen_number }}</td>
                            <td>{{ $pen->name ?? '-' }}</td>
                            <td>{{ $pen->type ?? '-' }}</td>
                            <td>{{ $pen->capacity }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $pen->animals_count ?? $pen->current_count ?? 0 }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $pen->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $pen->is_active ? 'نشطة' : 'غير نشطة' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted py-4">لا توجد حظائر مسجلة تابعة لهذه المزرعة</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection