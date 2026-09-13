@extends('layouts.customer.dashboard')

@section('title', __('livestock.titles.under_treatment'))

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
    <style>
        .alerts-page .alert-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
            border: 1px solid #eef2f6;
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .alerts-page .alert-card-header {
            background: #ffffff;
            border-bottom: 1px solid #eef2f6;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .alerts-page .table thead th {
            background-color: #f8fafc;
            color: #334155;
            font-size: 0.84rem;
            font-weight: 700;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 10px;
            vertical-align: middle;
            white-space: nowrap;
        }

        .alerts-page .table tbody td {
            font-size: 0.88rem;
            color: #1e293b;
            vertical-align: middle;
            padding: 12px 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .alerts-page .table tbody tr:hover {
            background-color: #f8fafc;
        }

        .alerts-page .table-responsive {
            overflow-x: auto !important;
            overflow-y: hidden !important;
            scrollbar-width: thin;
        }

        .tag-pill {
            background-color: #f1f5f9;
            color: #0f172a;
            border: 1px solid #cbd5e1;
            padding: 3px 8px;
            border-radius: 6px;
            font-family: monospace;
            font-weight: 700;
            text-decoration: none;
        }
        .tag-pill:hover {
            color: #15803d;
            border-color: #86efac;
        }

        .badge-treatment {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.76rem;
            font-weight: 600;
        }

        .badge-due-date {
            background-color: #fef9c3;
            color: #854d0e;
            border: 1px solid #fef08a;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 600;
            font-family: monospace;
        }

        .badge-expired {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 600;
            font-family: monospace;
        }

        .btn-action-view {
            color: #0284c7;
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 6px;
            padding: 4px 8px;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .btn-action-view:hover {
            background-color: #0284c7;
            color: #ffffff;
        }
    </style>
@endpush

@section('content')
@php
    $currentLocale = request()->route('locale') ?? app()->getLocale();
    $isArabic = str_starts_with(strtolower($currentLocale), 'ar');
@endphp

<div class="container py-4 alerts-page">
    {{-- رأس الصفحة --}}
    <div class="page-head mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="h3 font-weight-bold text-dark mb-1">
                <i class="fas fa-heartbeat text-danger mr-2"></i> {{ __('livestock.titles.under_treatment') }}
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('customer.livestock.animals.index', ['locale' => $currentLocale]) }}">{{ __('livestock.titles.animals') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('livestock.actions.alerts') }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a class="btn btn-outline-secondary btn-sm px-3"
                href="{{ route('customer.livestock.animals.index', ['locale' => $currentLocale]) }}">
                <i class="fas fa-arrow-left mr-1"></i> {{ __('livestock.actions.back_to_list') }}
            </a>
        </div>
    </div>

    {{-- 1. كارت الحالات تحت العلاج / الملاحظة --}}
    <div class="alert-card">
        <div class="alert-card-header">
            <h5 class="mb-0 font-weight-bold text-danger">
                <i class="fas fa-first-aid mr-2"></i> {{ __('livestock.titles.under_treatment') }}
            </h5>
            <span class="badge bg-danger text-white rounded-pill px-3">{{ count($rows) }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 text-center">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>{{ __('livestock.fields.tag') }}</th>
                        <th>{{ __('livestock.fields.species') }}</th>
                        <th>{{ __('livestock.fields.breed') }}</th>
                        <th>{{ __('farms.fields.farm') }}</th>
                        <th>{{ __('farms.fields.pen') }}</th>
                        <th>{{ __('livestock.fields.status') }}</th>
                        <th>{{ __('livestock.fields.health') }}</th>
                        <th style="width: 80px;">{{ __('livestock.fields.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $animal)
                        <tr>
                            <td class="text-muted font-weight-bold">{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('customer.livestock.animals.show', ['locale' => $currentLocale, 'animal' => $animal->id]) }}" class="tag-pill">
                                    {{ $animal->tag_number }}
                                </a>
                            </td>
                            <td class="font-weight-bold">{{ $animal->species->name ?? '-' }}</td>
                            <td>{{ $animal->breed->name ?? '-' }}</td>
                            <td>{{ $animal->pen?->farm?->name ?? '-' }}</td>
                            <td>
                                @if($animal->pen)
                                    <span class="badge bg-light text-dark border">
                                        {{ $animal->pen->pen_number }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border">
                                    {{ __('livestock.options.' . $animal->status) != 'livestock.options.' . $animal->status ? __('livestock.options.' . $animal->status) : $animal->status }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-treatment">
                                    {{ __('livestock.options.' . $animal->health_status) != 'livestock.options.' . $animal->health_status ? __('livestock.options.' . $animal->health_status) : $animal->health_status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('customer.livestock.animals.show', ['locale' => $currentLocale, 'animal' => $animal->id]) }}" 
                                   class="btn-action-view" title="{{ __('livestock.actions.view') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-4 text-center text-muted">
                                <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                                {{ __('livestock.empty.no_under_treatment') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 2. كارت التطعيمات القادمة والمستحقة --}}
    <div class="alert-card">
        <div class="alert-card-header">
            <h5 class="mb-0 font-weight-bold text-warning text-dark">
                <i class="fas fa-calendar-check mr-2 text-warning"></i> {{ __('livestock.sections.upcoming_vaccinations') }}
            </h5>
            <span class="badge bg-warning text-dark rounded-pill px-3">{{ count($vaccinationsDue) }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 text-center">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>{{ __('livestock.fields.tag') }}</th>
                        <th>{{ __('livestock.fields.vaccine') }}</th>
                        <th>{{ __('livestock.fields.dose_number') ?? 'الجرعة' }}</th>
                        <th>{{ __('livestock.fields.next_due_date_optional') }}</th>
                        <th style="width: 80px;">{{ __('livestock.fields.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vaccinationsDue as $row)
                        <tr>
                            <td class="text-muted font-weight-bold">{{ $loop->iteration }}</td>
                            <td>
                                @if($row->animal)
                                    <a href="{{ route('customer.livestock.animals.show', ['locale' => $currentLocale, 'animal' => $row->animal->id]) }}" class="tag-pill">
                                        {{ $row->animal->tag_number }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="font-weight-bold">{{ $row->vaccine->name ?? '-' }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $row->dose_number ?? 1 }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-due-date">
                                    {{ optional($row->next_due_date)->toDateString() ?? '-' }}
                                </span>
                            </td>
                            <td>
                                @if($row->animal)
                                    <a href="{{ route('customer.livestock.animals.show', ['locale' => $currentLocale, 'animal' => $row->animal->id]) }}" 
                                       class="btn-action-view" title="{{ __('livestock.actions.view') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-muted">
                                <i class="fas fa-syringe fa-2x mb-2 d-block text-secondary"></i>
                                {{ __('livestock.empty.no_upcoming_vaccinations') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- 3. كارت تشغيلات اللقاحات المنتهية الصلاحية --}}
    <div class="alert-card">
        <div class="alert-card-header">
            <h5 class="mb-0 font-weight-bold text-danger">
                <i class="fas fa-exclamation-triangle mr-2 text-danger"></i> {{ __('livestock.sections.expiring_vaccine_batches') }}
            </h5>
            <span class="badge bg-danger text-white rounded-pill px-3">{{ count($expiringVaccineBatches) }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 text-center">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>{{ __('livestock.fields.vaccine') }}</th>
                        <th>{{ __('livestock.fields.batch_number') }}</th>
                        <th>{{ __('livestock.fields.quantity') }}</th>
                        <th>{{ __('livestock.fields.expiry_date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($expiringVaccineBatches as $batch)
                        <tr>
                            <td class="text-muted font-weight-bold">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $batch->vaccine?->name ?? '-' }}</td>
                            <td><span class="badge bg-light text-dark border font-monospace">{{ $batch->batch_number ?? '-' }}</span></td>
                            <td class="font-weight-bold">{{ $batch->quantity }}</td>
                            <td>
                                <span class="badge-expired">
                                    {{ $batch->expiry_date?->format('Y-m-d') ?? '-' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-muted">
                                <i class="fas fa-shield-alt fa-2x mb-2 d-block text-success"></i>
                                {{ __('livestock.empty.no_expiring_vaccine_batches') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection