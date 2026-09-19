@extends('layouts.customer.dashboard')

@section('title', __('livestock.titles.animal_profile'))

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
    <style>
        .livestock-page .card-block {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            border: 1px solid #eef2f5;
            padding: 1.5rem;
            transition: box-shadow 0.2s ease;
        }
        .livestock-page .card-block:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
        }
        .toggle-table-btn {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none !important;
        }
        .toggle-table-btn:hover {
            background-color: #f1f5f9;
            color: #1e293b;
        }
        .toggle-table-btn .badge-counter {
            background: #e2e8f0;
            color: #475569;
            font-size: 0.75rem;
            padding: 3px 8px;
            border-radius: 6px;
        }
        .mini-history-table thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 0.78rem;
            font-weight: 700;
            border-bottom: 2px solid #e2e8f0;
            padding: 8px 6px;
        }
        .mini-history-table tbody td {
            font-size: 0.8rem;
            vertical-align: middle;
            padding: 8px 6px;
            color: #334155;
        }
        .info-pill {
            background: #f8fafc;
            border-radius: 8px;
            padding: 8px 12px;
            border: 1px solid #f1f5f9;
            margin-bottom: 8px;
            font-size: 0.88rem;
        }

        .livestock-page .table-responsive {
            overflow-x: auto !important;
            overflow-y: hidden !important;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .livestock-page .table-responsive::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
        }
        .livestock-page .dataTables_wrapper {
            overflow: visible !important;
        }
    </style>
@endpush

@section('content')
@php
    $currentLocale = request()->route('locale') ?? app()->getLocale();
    $isArabic = str_starts_with(strtolower($currentLocale), 'ar');
@endphp

<div class="container py-4 livestock-page">
    {{-- الهيدر السريع --}}
    <div class="page-head mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="page-title mb-1 font-weight-bold">
                {{ __('livestock.titles.animal_profile') }}: <span class="text-success">#{{ $animal->tag_number }}</span>
            </h2>
            <span class="badge bg-light text-secondary border">ID: {{ $animal->id }}</span>
        </div>
        <div class="quick-actions d-flex gap-2">
            <button type="button" class="btn btn-warning shadow-sm font-weight-bold" data-bs-toggle="modal" data-bs-target="#transferAnimalModal">
                <i class="fas fa-exchange-alt mr-1"></i> {{ __('livestock.actions.transfer_pen') }}
            </button>
            <a class="btn btn-outline-secondary"
                href="{{ route('customer.livestock.animals.edit', ['locale' => $currentLocale, 'animal' => $animal->id]) }}">
                <i class="fas fa-edit mr-1"></i> {{ __('livestock.actions.edit') }}
            </a>
            <a class="btn btn-outline-secondary"
                href="{{ route('customer.livestock.animals.index', ['locale' => $currentLocale]) }}">
                <i class="fas fa-arrow-left mr-1"></i> {{ __('livestock.actions.back_to_list') }}
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-3">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- بطاقة بيانات الحيوان الأساسية --}}
    <div class="card-block mb-3">
        <h6 class="font-weight-bold text-muted mb-3 border-bottom pb-2">
            <i class="fas fa-id-card mr-1 text-primary"></i> {{ $isArabic ? 'بيانات الحيوان الأساسية' : 'Primary Animal Details' }}
        </h6>
        <div class="row g-2">
            <div class="col-md-3 col-sm-6">
                <div class="info-pill">
                    <span class="text-muted d-block small">{{ __('livestock.fields.species') }}</span>
                    <strong>{{ $animal->species->name ?? __('livestock.options.no_data') }}</strong>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-pill">
                    <span class="text-muted d-block small">{{ __('livestock.fields.breed') }}</span>
                    <strong>{{ $animal->breed->name ?? __('livestock.options.no_data') }}</strong>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-pill">
                    <span class="text-muted d-block small">{{ __('farms.fields.farm') }}</span>
                    <strong>{{ $animal->pen?->farm?->name ?? __('livestock.options.no_data') }}</strong>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-pill">
                    <span class="text-muted d-block small">{{ __('farms.fields.pen') }}</span>
                    <strong>
                        @if($animal->pen)
                            {{ $animal->pen->pen_number }} 
                            <span class="text-muted small">({{ __('farms.pen_types.' . $animal->pen->type) ?? $animal->pen->type }})</span>
                        @else
                            {{ __('livestock.options.no_data') }}
                        @endif
                    </strong>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="info-pill">
                    <span class="text-muted d-block small">{{ __('livestock.fields.gender') }}</span>
                    <strong>{{ __('livestock.options.' . $animal->gender) }}</strong>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-pill">
                    <span class="text-muted d-block small">{{ __('livestock.fields.status') }}</span>
                    <span class="badge bg-success">{{ __('livestock.options.' . $animal->status) }}</span>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-pill">
                    <span class="text-muted d-block small">{{ __('livestock.fields.health') }}</span>
                    <span class="badge {{ $animal->health_status === 'under_treatment' ? 'bg-danger' : 'bg-info' }}">
                        {{ __('livestock.options.' . $animal->health_status) }}
                    </span>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="info-pill">
                    <span class="text-muted d-block small">{{ __('livestock.fields.intended_purpose') }}</span>
                    <strong>{{ $animal->intended_purpose ? __('livestock.options.' . $animal->intended_purpose) : __('livestock.options.no_data') }}</strong>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="info-pill">
                    <span class="text-muted d-block small">{{ __('livestock.fields.birth_date') }}</span>
                    <strong>{{ optional($animal->birth_date)->toDateString() ?? __('livestock.options.no_data') }}</strong>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="info-pill">
                    <span class="text-muted d-block small">{{ __('livestock.fields.mother') }}</span>
                    <strong>{{ $animal->mother->tag_number ?? __('livestock.options.no_data') }}</strong>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="info-pill">
                    <span class="text-muted d-block small">{{ __('livestock.fields.father') }}</span>
                    <strong>{{ $animal->father->tag_number ?? __('livestock.options.no_data') }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- كارت تغيير الحالة --}}
    <div class="card-block mb-4">
        <h6 class="font-weight-bold text-muted mb-3">
            <i class="fas fa-sync-alt mr-1 text-warning"></i> {{ __('livestock.sections.change_status') }}
        </h6>
        <form method="POST"
            action="{{ route('customer.livestock.animals.status.change', ['locale' => $currentLocale, 'animal' => $animal->id]) }}"
            class="row g-2 align-items-center">
            @csrf
            <div class="col-md-3">
                <select name="status" class="form-select" required>
                    @foreach (['active', 'sold', 'dead', 'slaughtered'] as $status)
                        <option value="{{ $status }}" @selected($animal->status === $status)>{{ __('livestock.options.' . $status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <input class="form-control" name="reason" placeholder="{{ __('livestock.fields.reason') }}">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary-green w-100" type="submit">{{ __('livestock.actions.change_status') }}</button>
            </div>
        </form>
    </div>

    {{-- كروت العمليات اليومية --}}
    <div class="row g-3">
        {{-- 1. التغذية --}}
        <div class="col-md-6">
            <div class="card-block h-100 d-flex flex-column justify-content-between">
                <div>
                    <h6 class="font-weight-bold text-success mb-3">
                        <i class="fas fa-utensils mr-1"></i> {{ __('livestock.sections.record_feeding') }}
                    </h6>
                    <form method="POST" action="{{ route('customer.livestock.feeding-logs.store', ['locale' => $currentLocale]) }}"
                        class="row g-2 mb-3">
                        @csrf
                        <input type="hidden" name="animal_id" value="{{ $animal->id }}">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">{{ __('livestock.fields.feed_type') }}</label>
                            <select name="feed_type_id" class="form-select" required>
                                @foreach ($feedTypes as $feedType)
                                    <option value="{{ $feedType->id }}">{{ $feedType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">{{ __('livestock.fields.feeding_date') }}</label>
                            <input type="date" name="feeding_date" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">{{ __('livestock.fields.quantity') }}</label>
                            <input type="number" step="0.01" min="0.01" name="quantity" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">{{ __('livestock.fields.unit_cost_optional') }}</label>
                            <input type="number" step="0.01" min="0" name="unit_cost" class="form-control">
                        </div>
                        <div class="col-12 mt-2">
                            <button class="btn btn-primary-green w-100" type="submit">{{ __('livestock.actions.save_feeding') }}</button>
                        </div>
                    </form>
                </div>

                <div class="border-top pt-3">
                    <button class="toggle-table-btn" type="button" data-bs-toggle="collapse" data-bs-target="#feedingLogsCollapse" aria-expanded="false">
                        <span><i class="fas fa-history mr-2 text-muted"></i> {{ __('livestock.sections.recent_feeding_logs') }}</span>
                        <span class="badge-counter">{{ $animal->feedingLogs->count() }}</span>
                    </button>
                    <div class="collapse mt-2" id="feedingLogsCollapse">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered text-center mini-history-table mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('livestock.fields.date') }}</th>
                                        <th>{{ __('livestock.fields.feed_type') }}</th>
                                        <th>{{ __('livestock.fields.quantity') }}</th>
                                        <th>{{ __('livestock.fields.cost') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($animal->feedingLogs->take(5) as $fLog)
                                        <tr>
                                            <td>{{ optional($fLog->feeding_date)->format('Y-m-d') ?? \Carbon\Carbon::parse($fLog->feeding_date)->format('Y-m-d') }}</td>
                                            <td>{{ $fLog->feedType->name ?? '-' }}</td>
                                            <td>{{ $fLog->quantity }}</td>
                                            <td>{{ $fLog->total_cost ? number_format($fLog->total_cost, 2) : '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-muted">{{ __('livestock.empty.no_feeding_logs') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. إنتاج الحليب --}}
        <div class="col-md-6">
            <div class="card-block h-100 d-flex flex-column justify-content-between">
                <div>
                    <h6 class="font-weight-bold text-primary mb-3">
                        <i class="fas fa-wine-bottle mr-1"></i> {{ __('livestock.sections.record_milk') }}
                    </h6>
                    <form method="POST"
                        action="{{ route('customer.livestock.milk-production-logs.store', ['locale' => $currentLocale]) }}"
                        class="row g-2 mb-3">
                        @csrf
                        <input type="hidden" name="animal_id" value="{{ $animal->id }}">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">{{ __('livestock.fields.date') }}</label>
                            <input type="date" name="production_date" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">{{ __('livestock.fields.quantity_liters') }}</label>
                            <input type="number" step="0.01" min="0.01" name="quantity_liters" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">{{ __('livestock.fields.fat_percentage') }}</label>
                            <input type="number" step="0.01" min="0" max="100" name="fat_percentage" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">{{ __('livestock.fields.quality_grade') }}</label>
                            <input type="text" name="quality_grade" class="form-control">
                        </div>
                        <div class="col-12 mt-2">
                            <button class="btn btn-primary-green w-100" type="submit">{{ __('livestock.actions.save_milk') }}</button>
                        </div>
                    </form>
                </div>

                <div class="border-top pt-3">
                    <button class="toggle-table-btn" type="button" data-bs-toggle="collapse" data-bs-target="#milkLogsCollapse" aria-expanded="false">
                        <span><i class="fas fa-history mr-2 text-muted"></i> {{ __('livestock.sections.recent_milk_logs') }}</span>
                        <span class="badge-counter">{{ $animal->milkProductionLogs->count() }}</span>
                    </button>
                    <div class="collapse mt-2" id="milkLogsCollapse">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered text-center mini-history-table mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('livestock.fields.date') }}</th>
                                        <th>{{ __('livestock.fields.quantity_liters') }}</th>
                                        <th>{{ __('livestock.fields.fat_percentage') }}</th>
                                        <th>{{ __('livestock.fields.quality_grade') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($animal->milkProductionLogs->sortByDesc('production_date')->take(5) as $mLog)
                                        <tr>
                                            <td>{{ optional($mLog->production_date)->format('Y-m-d') ?? \Carbon\Carbon::parse($mLog->production_date)->format('Y-m-d') }}</td>
                                            <td>{{ number_format($mLog->quantity_liters, 2) }}</td>
                                            <td>{{ $mLog->fat_percentage !== null ? $mLog->fat_percentage . '%' : '-' }}</td>
                                            <td>{{ $mLog->quality_grade ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-muted">{{ __('livestock.empty.no_milk_logs') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. الفحوصات والأحداث الصحية --}}
        <div class="col-md-6">
            <div class="card-block h-100 d-flex flex-column justify-content-between">
                <div>
                    <h6 class="font-weight-bold text-danger mb-3">
                        <i class="fas fa-notes-medical mr-1"></i> {{ __('livestock.sections.record_health') }}
                    </h6>
                    <form method="POST"
                        action="{{ route('customer.livestock.health-records.store', ['locale' => $currentLocale]) }}"
                        class="row g-2 mb-3">
                        @csrf
                        <input type="hidden" name="animal_id" value="{{ $animal->id }}">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">{{ __('livestock.fields.type') }}</label>
                            <select name="record_type" class="form-select" required>
                                <option value="checkup">{{ __('livestock.options.checkup') }}</option>
                                <option value="illness">{{ __('livestock.options.illness') }}</option>
                                <option value="injury">{{ __('livestock.options.injury') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">{{ __('livestock.fields.cost') }}</label>
                            <input type="number" step="0.01" min="0" name="cost" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-muted">{{ __('livestock.fields.diagnosis') }}</label>
                            <textarea name="diagnosis" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-muted">{{ __('livestock.fields.treatment') }}</label>
                            <textarea name="treatment" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label small text-muted">{{ __('livestock.fields.next_followup_date') }}</label>
                            <input type="date" name="next_followup_date" class="form-control">
                        </div>
                        <div class="col-md-5 d-flex align-items-center">
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="set_animal_under_treatment" value="1" id="underTreatment">
                                <label class="form-check-label small" for="underTreatment">
                                    {{ __('livestock.fields.mark_under_treatment') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <button class="btn btn-primary-green w-100" type="submit">{{ __('livestock.actions.save_health') }}</button>
                        </div>
                    </form>
                </div>

                <div class="border-top pt-3">
                    <button class="toggle-table-btn" type="button" data-bs-toggle="collapse" data-bs-target="#healthRecordsCollapse" aria-expanded="false">
                        <span><i class="fas fa-history mr-2 text-muted"></i> {{ __('livestock.sections.recent_health_records') }}</span>
                        <span class="badge-counter">{{ $animal->healthRecords->count() }}</span>
                    </button>
                    <div class="collapse mt-2" id="healthRecordsCollapse">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered text-center mini-history-table mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('livestock.fields.type') }}</th>
                                        <th>{{ __('livestock.fields.diagnosis') }}</th>
                                        <th>{{ __('livestock.fields.cost') }}</th>
                                        <th>{{ __('livestock.fields.date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($animal->healthRecords->take(5) as $hRecord)
                                        <tr>
                                            <td>{{ __('livestock.options.' . $hRecord->record_type) ?? $hRecord->record_type }}</td>
                                            <td>{{ Str::limit($hRecord->diagnosis, 25) }}</td>
                                            <td>{{ $hRecord->cost ? number_format($hRecord->cost, 2) : '-' }}</td>
                                            <td>{{ $hRecord->created_at ? $hRecord->created_at->format('Y-m-d') : '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-muted">{{ __('livestock.empty.no_health_records') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. التطعيمات واللقاحات --}}
        <div class="col-md-6">
            <div class="card-block h-100 d-flex flex-column justify-content-between">
                <div>
                    <h6 class="font-weight-bold text-info mb-3">
                        <i class="fas fa-syringe mr-1"></i> {{ __('livestock.sections.record_vaccination') }}
                    </h6>
                    <form method="POST"
                        action="{{ route('customer.livestock.vaccinations.store', ['locale' => $currentLocale]) }}"
                        class="row g-2 mb-3">
                        @csrf
                        <input type="hidden" name="animal_id" value="{{ $animal->id }}">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">{{ __('livestock.fields.vaccine') }}</label>
                            <select name="vaccine_id" class="form-select" required>
                                @foreach ($vaccines as $vaccine)
                                    <option value="{{ $vaccine->id }}">{{ $vaccine->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">{{ __('livestock.fields.dose_number') }}</label>
                            <input type="number" min="1" name="dose_number" class="form-control" value="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">{{ __('livestock.fields.vaccination_date') }}</label>
                            <input type="date" name="vaccination_date" class="form-control" value="{{ now()->toDateString() }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">{{ __('livestock.fields.next_due_date_optional') }}</label>
                            <input type="date" name="next_due_date" class="form-control">
                        </div>
                        <div class="col-12 mt-2">
                            <button class="btn btn-primary-green w-100" type="submit">{{ __('livestock.actions.save_vaccination') }}</button>
                        </div>
                    </form>
                </div>

                <div class="border-top pt-3">
                    <button class="toggle-table-btn" type="button" data-bs-toggle="collapse" data-bs-target="#vaccinesCollapse" aria-expanded="false">
                        <span><i class="fas fa-history mr-2 text-muted"></i> {{ __('livestock.sections.recent_vaccinations') }}</span>
                        <span class="badge-counter">{{ $animal->vaccinations->count() }}</span>
                    </button>
                    <div class="collapse mt-2" id="vaccinesCollapse">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered text-center mini-history-table mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('livestock.fields.vaccine') }}</th>
                                        <th>{{ __('livestock.fields.dose_number') }}</th>
                                        <th>{{ __('livestock.fields.vaccination_date') }}</th>
                                        <th>{{ __('livestock.fields.next_due_date_optional') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($animal->vaccinations->take(5) as $vac)
                                        <tr>
                                            <td>{{ $vac->vaccine->name ?? '-' }}</td>
                                            <td>{{ $vac->dose_number }}</td>
                                            <td>{{ optional($vac->vaccination_date)->format('Y-m-d') ?? \Carbon\Carbon::parse($vac->vaccination_date)->format('Y-m-d') }}</td>
                                            <td>
                                                @if($vac->next_due_date)
                                                    <span class="badge bg-light text-dark border">
                                                        {{ \Carbon\Carbon::parse($vac->next_due_date)->format('Y-m-d') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-muted">{{ __('livestock.empty.no_vaccines') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. تتبع الوزن --}}
        <div class="col-md-6">
            <div class="card-block h-100 d-flex flex-column justify-content-between">
                <div>
                    <h6 class="font-weight-bold text-dark mb-3">
                        <i class="fas fa-weight mr-1 text-secondary"></i> {{ __('livestock.sections.record_weight') }}
                    </h6>
                    <form method="POST" action="{{ route('customer.livestock.weight-logs.store', ['locale' => $currentLocale]) }}"
                        class="row g-2 mb-3">
                        @csrf
                        <input type="hidden" name="animal_id" value="{{ $animal->id }}">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">{{ __('livestock.fields.recorded_at') }}</label>
                            <input type="datetime-local" name="recorded_at" class="form-control"
                                value="{{ now()->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">{{ __('livestock.fields.weight') }} ({{ $isArabic ? 'كجم' : 'kg' }})</label>
                            <input type="number" step="0.01" min="0.01" name="weight" class="form-control" required>
                        </div>
                        <div class="col-12 mt-2">
                            <button class="btn btn-primary-green w-100" type="submit">{{ __('livestock.actions.save_weight') }}</button>
                        </div>
                    </form>
                </div>

                <div class="border-top pt-3">
                    <button class="toggle-table-btn" type="button" data-bs-toggle="collapse" data-bs-target="#weightLogsCollapse" aria-expanded="false">
                        <span><i class="fas fa-history mr-2 text-muted"></i> {{ __('livestock.sections.recent_weight_logs') }}</span>
                        <span class="badge-counter">{{ $animal->weightLogs->count() }}</span>
                    </button>
                    <div class="collapse mt-2" id="weightLogsCollapse">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered text-center mini-history-table mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('livestock.fields.recorded_at') }}</th>
                                        <th>{{ __('livestock.fields.weight') }} ({{ $isArabic ? 'كجم' : 'kg' }})</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($animal->weightLogs->sortByDesc('recorded_at')->take(5) as $wLog)
                                        <tr>
                                            <td>{{ optional($wLog->recorded_at)->format('Y-m-d H:i') ?? \Carbon\Carbon::parse($wLog->recorded_at)->format('Y-m-d H:i') }}</td>
                                            <td class="font-weight-bold">{{ number_format($wLog->weight, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-muted">{{ __('livestock.empty.no_weight_logs') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- سجل الحالات --}}
    <div class="card-block mt-4">
        <h6 class="font-weight-bold text-muted mb-3">
            <i class="fas fa-clipboard-list mr-1"></i> {{ __('livestock.sections.status_history') }}
        </h6>
        <div class="table-responsive">
            <table class="table js-livestock-table table-hover">
                <thead>
                    <tr>
                        <th>{{ __('livestock.fields.old') }}</th>
                        <th>{{ __('livestock.fields.new') }}</th>
                        <th>{{ __('livestock.fields.reason') }}</th>
                        <th>{{ __('livestock.fields.changed_at') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($animal->statusHistory as $history)
                        <tr>
                            <td>{{ __('livestock.options.' . $history->old_status) }}</td>
                            <td><span class="badge bg-light text-dark border">{{ __('livestock.options.' . $history->new_status) }}</span></td>
                            <td>{{ $history->change_reason ?? __('livestock.options.no_data') }}</td>
                            <td>{{ optional($history->changed_at)->toDateTimeString() ?? __('livestock.options.no_data') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">{{ __('livestock.empty.no_status_changes') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- نافذة نقل الحيوان (Modal) --}}
<div class="modal fade" id="transferAnimalModal" tabindex="-1" role="dialog" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <form id="transferAnimalForm" action="{{ route('customer.livestock.animals.transfer', ['locale' => $currentLocale, 'animal' => $animal->id]) }}" method="POST">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title font-weight-bold" id="transferModalLabel">
                        <i class="fas fa-exchange-alt mr-1 text-warning"></i> {{ __('livestock.actions.transfer_pen') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2 small mb-3">
                        {{ __('livestock.fields.current_pen') }}: 
                        <strong>
                            @if($animal->pen)
                                {{ $animal->pen->pen_number }} ({{ $animal->pen->farm?->name ?? '-' }})
                            @else
                                {{ $isArabic ? 'غير محدد' : 'Not assigned' }}
                            @endif
                        </strong>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold small mb-1">{{ __('livestock.fields.destination_pen') }} <span class="text-danger">*</span></label>
                        <select name="to_pen_id" id="toPenSelect" class="form-select" required>
                            <option value="">-- {{ __('livestock.placeholders.select_pen') }} --</option>
                            @php
                                $availablePens = \App\Models\FarmPen::with('farm')
                                    ->where('tenant_id', auth()->user()->tenant_id)
                                    ->where('id', '!=', $animal->pen_id)
                                    ->orderBy('pen_number')
                                    ->get();
                            @endphp
                            @foreach($availablePens as $targetPen)
                                <option value="{{ $targetPen->id }}" data-pen="{{ $targetPen->pen_number }}">
                                    {{ $targetPen->farm?->name ?? 'مزرعة' }} - {{ $targetPen->pen_number }} 
                                    ({{ __('farms.pen_types.' . $targetPen->type) ?? $targetPen->type }}) 
                                    [{{ $targetPen->current_count }}/{{ $targetPen->capacity }}]
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold small mb-1">{{ __('livestock.fields.notes') }}</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="{{ __('livestock.placeholders.notes') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('livestock.actions.cancel') }}</button>
                    <button type="button" id="btnSubmitTransfer" class="btn btn-warning font-weight-bold">{{ __('livestock.actions.confirm_transfer') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const currentLang = '{{ strtolower($currentLocale) }}';
            const isArabic = currentLang.startsWith('ar');

            const btnSubmit = document.getElementById('btnSubmitTransfer');
            const formTransfer = document.getElementById('transferAnimalForm');
            const selectPen = document.getElementById('toPenSelect');

            if (btnSubmit && formTransfer) {
                btnSubmit.addEventListener('click', function () {
                    if (!selectPen.value) {
                        Swal.fire({
                            icon: 'warning',
                            title: isArabic ? 'تنبيه' : 'Attention',
                            text: isArabic ? 'يرجى اختيار الحظيرة الجديدة أولاً' : 'Please select destination pen first',
                            confirmButtonColor: '#e11d48',
                            confirmButtonText: isArabic ? 'حسناً' : 'OK'
                        });
                        return;
                    }

                    const selectedOption = selectPen.options[selectPen.selectedIndex];
                    const penText = selectedOption ? selectedOption.getAttribute('data-pen') : '';

                    Swal.fire({
                        title: isArabic ? 'تأكيد نقل الحيوان' : 'Confirm Transfer',
                        text: isArabic 
                            ? `هل أنت متأكد من نقل الحيوان إلى الحظيرة رقم (${penText})؟` 
                            : `Are you sure you want to transfer this animal to pen (${penText})?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#f59e0b',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: isArabic ? 'نعم، انقل الحيوان' : 'Yes, Transfer',
                        cancelButtonText: isArabic ? 'إلغاء' : 'Cancel',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            formTransfer.submit();
                        }
                    });
                });
            }
        });
    </script>
@endpush