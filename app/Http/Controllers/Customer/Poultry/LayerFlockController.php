@extends('layouts.customer.dashboard')

@section('title', __('poultry.titles.layer_flock_details'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
@php
    $currentLocale = request()->route('locale') ?? app()->getLocale();
@endphp
<div class="container py-4 livestock-page">
    <div class="page-head">
        <h2 class="page-title">{{ __('poultry.titles.layer_flock_details') }}: {{ $flock->flock_number }}</h2>
        <div class="d-flex gap-2">
            <a class="btn btn-sm btn-outline-white" href="{{ route('customer.poultry.layer-flocks.edit', ['locale' => $currentLocale, 'layer_flock' => $flock->id]) }}">
                <i class="fas fa-edit mr-1"></i> {{ __('poultry.actions.edit') }}
            </a>
            <a class="btn btn-sm btn-outline-white" href="{{ route('customer.poultry.layer-flocks.index', ['locale' => $currentLocale]) }}">
                {{ __('poultry.actions.back') }}
            </a>
        </div>
    </div>

    @include('dashboard.customer.poultry.partials.flash')

    {{-- بطاقات الملخص المالي والإنتاجي --}}
    <div class="card-block mb-3">
        <div class="row g-3">
            <div class="col-md-2">
                <strong>{{ __('poultry.fields.total_egg_revenue') }}:</strong>
                <span class="text-success font-weight-bold">{{ number_format((float)($flock->total_egg_revenue ?? 0), 2) }}</span>
            </div>
            <div class="col-md-2">
                <strong>{{ __('poultry.fields.total_feed_cost') }}:</strong>
                <span class="text-danger">{{ number_format((float)($flock->total_feed_cost ?? 0), 2) }}</span>
            </div>
            <div class="col-md-2">
                <strong>{{ __('poultry.fields.net_profit') }}:</strong>
                <span class="font-weight-bold {{ (float)($flock->net_profit ?? 0) >= 0 ? 'text-primary' : 'text-danger' }}">
                    {{ number_format((float)($flock->net_profit ?? 0), 2) }}
                </span>
            </div>
            <div class="col-md-2">
                <strong>الطيور الحية:</strong>
                <span class="text-primary font-weight-bold">{{ number_format($metrics['current_live_hens'] ?? 0) }}</span>
            </div>
            <div class="col-md-2">
                <strong>نسبة النفوق:</strong>
                <span class="{{ ($metrics['mortality_rate'] ?? 0) > 5 ? 'text-danger' : 'text-success' }}">
                    {{ $metrics['mortality_rate'] ?? 0 }}% ({{ $metrics['total_mortality'] ?? 0 }})
                </span>
            </div>
            <div class="col-md-2">
                <strong>معدل البياض اليومي:</strong>
                <span class="badge bg-success">{{ $metrics['hen_day_production_rate'] ?? 0 }}%</span>
            </div>
        </div>
    </div>

    {{-- نموذج تسجيل إنتاج البيض اليومي --}}
    <div class="card-block mb-3">
        <h5 class="section-title mb-3 font-weight-bold">
            <i class="fas fa-egg text-warning mr-1"></i> {{ __('poultry.actions.record_egg_log') }}
        </h5>
        <form method="POST" action="{{ route('customer.poultry.layer-flocks.egg-logs.store', ['locale' => $currentLocale, 'layer_flock' => $flock->id]) }}" class="row g-3">
            @csrf
            <div class="col-md-2">
                <input type="date" name="production_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-2">
                <input type="number" min="0" name="eggs_count" class="form-control" placeholder="{{ __('poultry.fields.eggs_count') }}" required>
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" min="0" name="sale_price" class="form-control" placeholder="{{ __('poultry.fields.sale_price') }}" required>
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" min="0" name="daily_feed_cost" class="form-control" placeholder="{{ __('poultry.fields.daily_feed_cost') }}" required>
            </div>
            <div class="col-md-2">
                <input type="number" min="0" name="damaged_count" class="form-control" placeholder="{{ __('poultry.fields.damaged_count') }}" value="0">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary-green w-100">{{ __('poultry.actions.save') }}</button>
            </div>
        </form>
    </div>

    {{-- نموذج تسجيل النفوق اليومي --}}
    <div class="card-block mb-4">
        <h5 class="section-title mb-3 font-weight-bold">
            <i class="fas fa-heart-broken text-danger mr-1"></i> {{ __('poultry.actions.record_mortality') }}
        </h5>
        <form method="POST" action="{{ route('customer.poultry.layer-flocks.mortalities.store', ['locale' => $currentLocale, 'layer_flock' => $flock->id]) }}" class="row g-3">
            @csrf
            <div class="col-md-3">
                <input type="date" name="mortality_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-3">
                <input type="number" min="1" name="quantity" class="form-control" placeholder="{{ __('poultry.fields.quantity') }}" required>
            </div>
            <div class="col-md-4">
                <input type="text" name="notes" class="form-control" placeholder="{{ __('poultry.fields.notes') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary-green w-100">{{ __('poultry.actions.save') }}</button>
            </div>
        </form>
    </div>

    {{-- جدول حركة وسجل إنتاج البيض اليومي --}}
    <div class="card-block">
        <h5 class="border-bottom pb-2 mb-3 font-weight-bold">
            <i class="fas fa-history mr-1"></i> سجل إنتاج وتكاليف البيض
        </h5>
        <div class="table-container">
            <table class="table registry-table mb-0">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>عدد البيض</th>
                        <th>سعر البيع المقدر</th>
                        <th>تكلفة العلف اليومي</th>
                        <th>البيض التالف/الكسر</th>
                        <th>الأطباق التقريبية (30)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($flock->eggProductionLogs as $log)
                        @php
                            $eggs = (int) ($log->eggs_count ?? $log->good_eggs_count ?? 0);
                            $damaged = (int) ($log->damaged_count ?? $log->damaged_eggs_count ?? 0);
                        @endphp
                        <tr>
                            <td>{{ $log->production_date ? \Carbon\Carbon::parse($log->production_date)->format('Y-m-d') : '-' }}</td>
                            <td class="text-success font-weight-bold">{{ number_format($eggs) }}</td>
                            <td>{{ number_format((float)($log->sale_price ?? 0), 2) }}</td>
                            <td class="text-danger">{{ number_format((float)($log->daily_feed_cost ?? 0), 2) }}</td>
                            <td class="text-muted">{{ number_format($damaged) }}</td>
                            <td><span class="badge bg-light text-dark border">{{ round($eggs / 30, 1) }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">{{ __('poultry.empty.no_egg_logs') ?? 'لا توجد سجلات إنتاج بيض مسجلة حتى الآن.' }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection