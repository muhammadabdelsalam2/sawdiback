@extends('layouts.customer.dashboard')

@section('title', __('poultry.titles.hatchery_batch_details'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
    <style>
        .batch-details-page .card-block {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            border: 1px solid #eef2f5;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .stat-badge {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
        }
        .stat-badge .label {
            font-size: 0.8rem;
            color: #64748b;
            margin-bottom: 4px;
            font-weight: 600;
        }
        .stat-badge .value {
            font-size: 1.15rem;
            color: #0f172a;
            font-weight: 700;
        }
        .badge-incident {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
        }
        .badge-normal {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
@php
    $currentLocale = request()->route('locale') ?? app()->getLocale();
    $isArabic = str_starts_with(strtolower($currentLocale), 'ar');
    $breedsList = \App\Models\Poultry\PoultryHatcheryBatch::BREEDS ?? [];
@endphp

<div class="container py-4 livestock-page batch-details-page">
    {{-- رأس الصفحة --}}
    <div class="page-head mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="page-title mb-1 font-weight-bold text-dark">
                {{ __('poultry.titles.hatchery_batch_details') }}: {{ $batch->batch_number }}
            </h2>
            <div class="text-muted small">
                <i class="fas fa-calendar-alt mr-1"></i> {{ $isArabic ? 'تاريخ التحميل:' : 'Loaded at:' }} 
                {{ $batch->loaded_at?->format('Y-m-d') }} | 
                {{ $isArabic ? 'المتوقع للتفقيس:' : 'Expected Hatch:' }} 
                {{ $batch->expected_hatch_at?->format('Y-m-d') ?? '-' }}
                @if($batch->actual_hatch_at)
                    | <span class="text-success font-weight-bold"><i class="fas fa-check-circle mr-1"></i> {{ $isArabic ? 'تم الفقس في:' : 'Hatched at:' }} {{ $batch->actual_hatch_at->format('Y-m-d') }}</span>
                @endif
            </div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-sm btn-outline-white" href="{{ route('customer.poultry.hatchery-batches.edit', ['locale' => $currentLocale, 'hatchery_batch' => $batch->id]) }}">
                <i class="fas fa-edit mr-1"></i> {{ __('poultry.actions.edit') }}
            </a>
            <a class="btn btn-outline-secondary px-3" href="{{ route('customer.poultry.hatchery-batches.index', ['locale' => $currentLocale]) }}">
                <i class="fas fa-arrow-left mr-1"></i> {{ __('poultry.actions.back') }}
            </a>
        </div>
    </div>

    @include('dashboard.customer.poultry.partials.flash')

    {{-- 1. كروت الإحصائيات الأساسية --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-badge">
                <div class="label">{{ __('poultry.fields.machine') }}</div>
                <div class="value text-primary">{{ $batch->machine?->machine_number ?? '-' }}</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-badge">
                <div class="label">{{ __('poultry.fields.eggs_loaded') }}</div>
                <div class="value">{{ number_format($batch->eggs_loaded) }}</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-badge">
                <div class="label">{{ __('poultry.fields.chicks_produced') }}</div>
                <div class="value text-success">{{ number_format($batch->chicks_produced) }}</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-badge">
                <div class="label">{{ __('poultry.fields.success_rate') }}</div>
                <div class="value text-info">{{ $batch->success_rate ?? ($batch->eggs_loaded > 0 ? round(($batch->chicks_produced / $batch->eggs_loaded) * 100, 2) : 0) }}%</div>
            </div>
        </div>
    </div>

    {{-- 2. بطاقة الأداء المالي وربحية الدفعة --}}
    <div class="card-block mb-4">
        <h5 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
            <i class="fas fa-coins text-warning mr-1"></i> {{ $isArabic ? 'التحليل المالي وتكلفة الدفعة' : 'Financial & Profitability Breakdown' }}
        </h5>
        <div class="row g-3">
            <div class="col-md-3 col-sm-6">
                <div class="stat-badge border-start border-danger border-4">
                    <div class="label">{{ $isArabic ? 'إجمالي التكاليف (شراء + تشغيل)' : 'Total Costs' }}</div>
                    <div class="value text-danger">{{ number_format((float)($financials['total_cost'] ?? $batch->purchase_amount), 2) }}</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-badge border-start border-success border-4">
                    <div class="label">{{ $isArabic ? 'الإيراد المحقق / المتوقع' : 'Revenue' }}</div>
                    <div class="value text-success">{{ number_format((float)($financials['total_revenue'] ?? 0), 2) }}</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-badge border-start border-primary border-4">
                    <div class="label">{{ __('poultry.fields.net_profit') }}</div>
                    <div class="value {{ (float)($financials['net_profit'] ?? 0) >= 0 ? 'text-primary' : 'text-danger' }}">
                        {{ number_format((float)($financials['net_profit'] ?? 0), 2) }}
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-badge border-start border-info border-4">
                    <div class="label">{{ $isArabic ? 'تكلفة الكتكوت الواحد' : 'Cost Per Chick' }}</div>
                    <div class="value text-info">{{ number_format((float)($financials['cost_per_chick'] ?? 0), 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. تسجيل إتمام الفقس الفعلي وترحيل الحسابات --}}
    @if(!$batch->actual_hatch_at)
        <div class="card-block mb-4 bg-light border-primary">
            <h5 class="font-weight-bold text-dark mb-2">
                <i class="fas fa-check-double text-primary mr-1"></i> {{ $isArabic ? 'تسجيل إتمام الفقس الفعلي للدفعة' : 'Record Actual Hatch Completion' }}
            </h5>
            <p class="text-muted small mb-3">
                {{ $isArabic ? 'عند تسجيل عدد الكتاكيت الفاقسة وتاريخ الفقس، سيتم تثبيت مؤشرات النجاح وترحيل القيد اليومي للمدفوعات والمبيعات تلقائياً.' : 'Recording actual hatch will compute final success rate and trigger automatic journal entries.' }}
            </p>
            <form method="POST" action="{{ route('customer.poultry.hatchery-batches.update', ['locale' => $currentLocale, 'hatchery_batch' => $batch->id]) }}" class="row g-3 align-items-end">
                @csrf
                @method('PUT')
                <div class="col-md-4">
                    <label class="form-label font-weight-bold small text-muted">{{ $isArabic ? 'تاريخ الفقس الفعلي' : 'Actual Hatch Date' }} <span class="text-danger">*</span></label>
                    <input type="date" name="actual_hatch_at" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label font-weight-bold small text-muted">{{ __('poultry.fields.chicks_produced') }} <span class="text-danger">*</span></label>
                    <input type="number" min="1" max="{{ $batch->eggs_loaded }}" name="chicks_produced" class="form-control" placeholder="مثال: 4500" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary-green w-100 font-weight-bold" onclick="return confirm('{{ $isArabic ? 'هل أنت متأكد من تسجيل نتيجة الفقس وإتمام الدفعة؟' : 'Confirm hatch completion?' }}')">
                        <i class="fas fa-lock mr-1"></i> {{ $isArabic ? 'إتمام الدفعة وترحيل الحسابات' : 'Complete & Post Journal' }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- 4. تفاصيل التكلفة ومصدر البيض وتوزيع السلالات --}}
    <div class="row g-3 mb-4">
        {{-- بيانات المصدر والتكلفة --}}
        <div class="col-lg-5">
            <div class="card-block h-100">
                <h5 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                    <i class="fas fa-info-circle text-primary mr-1"></i> {{ $isArabic ? 'بيانات التكلفة والتوريد' : 'Cost & Source Info' }}
                </h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2 d-flex justify-content-between border-bottom pb-2">
                        <span class="text-muted">{{ $isArabic ? 'مبلغ الشراء / التكلفة:' : 'Purchase Cost:' }}</span>
                        <strong class="text-dark">{{ number_format((float)$batch->purchase_amount, 2) }}</strong>
                    </li>
                    <li class="mb-2 d-flex justify-content-between border-bottom pb-2">
                        <span class="text-muted">{{ $isArabic ? 'مصدر البيض:' : 'Egg Source:' }}</span>
                        <strong>
                            @if($batch->egg_source === 'purchased')
                                <span class="badge bg-warning text-dark">{{ $isArabic ? 'شراء خارجي' : 'Purchased' }}</span>
                            @else
                                <span class="badge bg-info text-white">{{ $isArabic ? 'من المزرعة' : 'Internal Farm' }}</span>
                            @endif
                        </strong>
                    </li>
                    @if($batch->egg_source === 'purchased')
                        <li class="mb-2 d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted">{{ $isArabic ? 'اسم البائع:' : 'Seller Name:' }}</span>
                            <strong>{{ $batch->seller_name ?? '-' }}</strong>
                        </li>
                        <li class="mb-2 d-flex justify-content-between border-bottom pb-2">
                            <span class="text-muted">{{ $isArabic ? 'رقم هاتف البائع:' : 'Seller Phone:' }}</span>
                            <strong>{{ $batch->seller_phone ?? '-' }}</strong>
                        </li>
                        <li class="mb-2 d-flex justify-content-between pb-1">
                            <span class="text-muted">{{ $isArabic ? 'موقع البائع:' : 'Seller Location:' }}</span>
                            <strong>{{ $batch->seller_location ?? '-' }}</strong>
                        </li>
                    @else
                        <li class="mb-2 d-flex justify-content-between pb-1">
                            <span class="text-muted">{{ $isArabic ? 'رقم الحظيرة المصدر:' : 'Source Pen:' }}</span>
                            <strong>{{ $batch->pen?->farm?->name }} - {{ $batch->pen?->pen_number ?? '-' }}</strong>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        {{-- توزيع السلالات --}}
        <div class="col-lg-7">
            <div class="card-block h-100">
                <h5 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
                    <i class="fas fa-dna text-success mr-1"></i> {{ $isArabic ? 'توزيع السلالات وكميات البيض' : 'Breeds & Egg Breakdown' }}
                </h5>
                <div class="table-responsive">
                    <table class="table table-sm table-hover text-center align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start">{{ $isArabic ? 'السلالة' : 'Breed' }}</th>
                                <th>{{ $isArabic ? 'عدد البيض' : 'Eggs Loaded' }}</th>
                                <th>{{ $isArabic ? 'النسبة من الدفعة' : 'Share %' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $batchBreeds = $batch->relationLoaded('breeds') ? $batch->breeds : $batch->breeds()->get();
                            @endphp
                            @forelse($batchBreeds as $b)
                                @php
                                    $count = (int) ($b->pivot->egg_count ?? 0);
                                    $percent = $batch->eggs_loaded > 0 ? round(($count / $batch->eggs_loaded) * 100, 1) : 0;
                                @endphp
                                <tr>
                                    <td class="text-start font-weight-bold">{{ $b->name ?? ($breedsList[$b->slug] ?? $b->slug) }}</td>
                                    <td>{{ number_format($count) }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $percent }}%</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-muted py-3">
                                        {{ $isArabic ? 'لم يتم ربط سلالات تفصيلية أو مسجلة كسلالة واحدة عامة.' : 'No detailed breeds recorded.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- 5. تسجيل متابعة يومية جديدة --}}
    <div class="card-block">
        <h5 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
            <i class="fas fa-plus-circle text-primary mr-1"></i> {{ $isArabic ? 'تسجيل قراءة متابعة يومية للفقاسة' : 'Add Daily Monitoring Log' }}
        </h5>
        
        <form method="POST" action="{{ route('customer.poultry.hatchery-batches.daily-logs.store', ['locale' => $currentLocale, 'hatchery_batch' => $batch->id]) }}">
            @csrf
            <div class="row g-3 align-items-end">
                <div class="col-md-2 col-sm-6">
                    <label class="form-label font-weight-bold small text-muted">{{ $isArabic ? 'التاريخ' : 'Date' }} <span class="text-danger">*</span></label>
                    <input type="date" name="log_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-2 col-sm-6">
                    <label class="form-label font-weight-bold small text-muted">{{ $isArabic ? 'درجة الحرارة (°م)' : 'Temp (°C)' }} <span class="text-danger">*</span></label>
                    <input type="number" step="0.1" name="temperature" class="form-control" placeholder="37.5" required>
                </div>
                <div class="col-md-2 col-sm-6">
                    <label class="form-label font-weight-bold small text-muted">{{ $isArabic ? 'درجة الرطوبة (%)' : 'Humidity (%)' }} <span class="text-danger">*</span></label>
                    <input type="number" step="0.1" name="humidity" class="form-control" placeholder="60.0" required>
                </div>
                <div class="col-md-2 col-sm-6">
                    <label class="form-label font-weight-bold small text-muted">{{ $isArabic ? 'حالة التشغيل' : 'Incident Status' }}</label>
                    <select name="has_incident" id="hasIncidentSelect" class="form-select">
                        <option value="0">{{ $isArabic ? 'طبيعي / منتظم' : 'Normal' }}</option>
                        <option value="1">{{ $isArabic ? 'عطل / توقف' : 'Incident / Stoppage' }}</option>
                    </select>
                </div>
                <div class="col-md-2 col-sm-6 incident-input" style="display:none;">
                    <label class="form-label font-weight-bold small text-danger">{{ $isArabic ? 'مدة التوقف (ساعة)' : 'Duration (Hours)' }}</label>
                    <input type="number" step="0.1" min="0" name="stoppage_duration_hours" class="form-control border-danger" placeholder="مثال: 2">
                </div>
                <div class="col-md-4 incident-input" style="display:none;">
                    <label class="form-label font-weight-bold small text-danger">{{ $isArabic ? 'سبب العطل / التوقف' : 'Reason of Incident' }}</label>
                    <input type="text" name="incident_reason" class="form-control border-danger" placeholder="{{ $isArabic ? 'مثال: انقطاع الكهرباء / صيانة دورية' : 'e.g. Power outage' }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label font-weight-bold small text-muted">{{ __('poultry.fields.notes') }}</label>
                    <input type="text" name="notes" class="form-control" placeholder="{{ $isArabic ? 'ملاحظات أخرى...' : 'Notes...' }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary-green w-100 font-weight-bold">
                        <i class="fas fa-save mr-1"></i> {{ $isArabic ? 'تسجيل اليومية' : 'Save Log' }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- 6. جدول المتابعة اليومي --}}
    <div class="card-block">
        <h5 class="font-weight-bold text-dark border-bottom pb-2 mb-3">
            <i class="fas fa-history text-secondary mr-1"></i> {{ $isArabic ? 'جدول المتابعة اليومي للدفعة' : 'Daily Monitoring Log History' }}
        </h5>
        
        <div class="table-container">
            <table class="table registry-table mb-0 text-center">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>{{ $isArabic ? 'اليوم / التاريخ' : 'Date' }}</th>
                        <th>{{ $isArabic ? 'درجة الحرارة' : 'Temperature' }}</th>
                        <th>{{ $isArabic ? 'درجة الرطوبة' : 'Humidity' }}</th>
                        <th>{{ $isArabic ? 'حالة الماكينة' : 'Status' }}</th>
                        <th>{{ $isArabic ? 'مدة التوقف' : 'Stoppage' }}</th>
                        <th>{{ $isArabic ? 'السبب / العطل' : 'Reason / Notes' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $dailyLogs = $batch->dailyLogs()->orderBy('log_date', 'asc')->get();
                    @endphp
                    @forelse($dailyLogs as $log)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $log->log_date?->format('Y-m-d') }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $log->temperature }} °م</span></td>
                            <td><span class="badge bg-light text-info border">{{ $log->humidity }} %</span></td>
                            <td>
                                @if($log->has_incident)
                                    <span class="badge-incident"><i class="fas fa-exclamation-triangle mr-1"></i> {{ $isArabic ? 'عطل / إيقاف' : 'Incident' }}</span>
                                @else
                                    <span class="badge-normal"><i class="fas fa-check-circle mr-1"></i> {{ $isArabic ? 'منتظم' : 'Normal' }}</span>
                                @endif
                            </td>
                            <td>
                                @if($log->stoppage_duration_hours > 0)
                                    <span class="font-weight-bold text-danger">{{ $log->stoppage_duration_hours }} {{ $isArabic ? 'ساعة' : 'hrs' }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-start">
                                @if($log->has_incident && $log->incident_reason)
                                    <strong class="text-danger">{{ $log->incident_reason }}</strong>
                                    @if($log->notes) - <small class="text-muted">{{ $log->notes }}</small> @endif
                                @else
                                    <span class="text-muted">{{ $log->notes ?: '-' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-4 text-muted">
                                <i class="fas fa-clipboard-list fa-2x mb-2 d-block text-secondary"></i>
                                {{ $isArabic ? 'لا توجد قراءات متابعة يومية مسجلة لهذه الدفعة حتى الآن.' : 'No daily monitoring logs recorded yet.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const incidentSelect = document.getElementById('hasIncidentSelect');
    const incidentInputs = document.querySelectorAll('.incident-input');

    function toggleIncidentInputs() {
        if (incidentSelect && incidentSelect.value === '1') {
            incidentInputs.forEach(el => el.style.display = 'block');
        } else {
            incidentInputs.forEach(el => el.style.display = 'none');
        }
    }

    if (incidentSelect) {
        incidentSelect.addEventListener('change', toggleIncidentInputs);
        toggleIncidentInputs();
    }
});
</script>
@endsection