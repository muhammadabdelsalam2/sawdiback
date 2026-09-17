@extends('layouts.customer.dashboard')

@section('title', __('poultry.titles.chicken_breed_details'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
<div class="container py-4 livestock-page">
    <div class="page-head d-flex justify-content-between align-items-center mb-3">
        <h2 class="page-title">{{ __('poultry.titles.chicken_breed_details') }}: {{ $breed->code }}</h2>
        <div class="d-flex gap-2">
            <a class="btn btn-sm btn-outline-white" href="{{ route('customer.poultry.chicken-breeds.edit', ['locale' => $currentLocale, 'chicken_breed' => $breed->id]) }}">
                <i class="fas fa-edit mr-1"></i> {{ __('poultry.actions.edit') }}
            </a>
            <a class="btn btn-sm btn-outline-white" href="{{ route('customer.poultry.chicken-breeds.index', ['locale' => $currentLocale]) }}">
                {{ __('poultry.actions.back') }}
            </a>
        </div>
    </div>

    @include('dashboard.customer.poultry.partials.flash')

    {{-- بيانات السلالة والحظيرة --}}
    <div class="card-block mb-3">
        <div class="row g-3">
            <div class="col-md-3">
                <strong>{{ __('poultry.fields.breed_type') }}:</strong>
                <span class="badge bg-light text-dark border">{{ __('poultry.options.' . $breed->breed_type) ?? $breed->breed_type }}</span>
            </div>
            <div class="col-md-3">
                <strong>{{ __('poultry.fields.purchase_amount') }}:</strong>
                <span class="text-danger font-weight-bold">{{ number_format((float)$breed->purchase_amount, 2) }}</span>
            </div>
            <div class="col-md-3">
                <strong>{{ __('poultry.fields.pen') ?? 'الحظيرة' }}:</strong>
                <span>{{ $breed->pen?->farm?->name }} - {{ $breed->pen?->pen_number ?? '-' }}</span>
            </div>
            <div class="col-md-3">
                <strong>تاريخ البدء:</strong>
                <span>{{ $breed->started_at ? \Carbon\Carbon::parse($breed->started_at)->format('Y-m-d') : '-' }}</span>
            </div>
        </div>
    </div>

    {{-- بطاقات مؤشرات الإنتاج والقطيع --}}
    <div class="card-block mb-3">
        <div class="row g-3">
            <div class="col-md-2">
                <strong>{{ __('poultry.fields.female_count') }}:</strong>
                <span class="text-primary font-weight-bold">{{ number_format($metrics['female_count']) }}</span>
            </div>
            <div class="col-md-2">
                <strong>{{ __('poultry.fields.male_count') }}:</strong>
                <span class="text-info font-weight-bold">{{ number_format($metrics['male_count']) }}</span>
            </div>
            <div class="col-md-2">
                <strong>إجمالي القطيع:</strong>
                <span class="font-weight-bold">{{ number_format($metrics['total_birds']) }}</span>
            </div>
            <div class="col-md-2">
                <strong>إجمالي البيض المجموع:</strong>
                <span class="text-success font-weight-bold">{{ number_format($metrics['total_eggs']) }}</span>
            </div>
            <div class="col-md-2">
                <strong>عدد الأطباق (30 بيضة):</strong>
                <span class="badge bg-light text-dark border">{{ number_format($metrics['total_trays'], 1) }}</span>
            </div>
            <div class="col-md-2">
                <strong>متوسط إنتاج الدجاجة:</strong>
                <span class="badge bg-success">{{ $metrics['avg_eggs_per_hen'] }}</span>
            </div>
        </div>
    </div>

    {{-- نموذج تسجيل جمع البيض اليومي للسلالة --}}
    <div class="card-block mb-3">
        <h5 class="section-title mb-3 font-weight-bold">
            <i class="fas fa-egg text-warning mr-1"></i> {{ __('poultry.actions.record_breed_egg_log') }}
        </h5>
        <form method="POST" action="{{ route('customer.poultry.chicken-breeds.egg-logs.store', ['locale' => $currentLocale, 'chicken_breed' => $breed->id]) }}" class="row g-3">
            @csrf
            <div class="col-md-2">
                <input type="date" name="production_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-2">
                <input type="number" min="0" name="eggs_count" class="form-control" placeholder="{{ __('poultry.fields.eggs_count') }}" required>
            </div>
            <div class="col-md-2">
                <input type="number" min="0" name="fertilized_count" class="form-control" placeholder="{{ __('poultry.fields.fertilized_count') }}">
            </div>
            <div class="col-md-2">
                <input type="number" min="0" name="unfertilized_count" class="form-control" placeholder="{{ __('poultry.fields.unfertilized_count') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="notes" class="form-control" placeholder="{{ __('poultry.fields.notes') }}">
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary-green w-100">{{ __('poultry.actions.save') }}</button>
            </div>
        </form>
    </div>

    {{-- جدول سجل حركة إنتاج البيض --}}
    <div class="card-block">
        <h5 class="border-bottom pb-2 mb-3 font-weight-bold">
            <i class="fas fa-history mr-1"></i> سجل إنتاج بيض السلالة
        </h5>
        <div class="table-container">
            <table class="table registry-table mb-0">
                <thead>
                    <tr>
                        <th>{{ __('poultry.fields.production_date') }}</th>
                        <th>{{ __('poultry.fields.eggs_count') }}</th>
                        <th>{{ __('poultry.fields.fertilized_count') }}</th>
                        <th>{{ __('poultry.fields.unfertilized_count') }}</th>
                        <th>نسبة الخصوبة المقدرة</th>
                        <th>{{ __('poultry.fields.notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($breed->eggLogs as $log)
                        @php
                            $total = (int) $log->eggs_count;
                            $fert = (int) ($log->fertilized_count ?? 0);
                            $fertRate = $total > 0 && $fert > 0 ? round(($fert / $total) * 100, 1) : null;
                        @endphp
                        <tr>
                            <td>{{ $log->production_date ? \Carbon\Carbon::parse($log->production_date)->format('Y-m-d') : '-' }}</td>
                            <td class="text-success font-weight-bold">{{ number_format($total) }}</td>
                            <td>{{ number_format($fert) }}</td>
                            <td class="text-muted">{{ number_format((int)($log->unfertilized_count ?? 0)) }}</td>
                            <td>
                                @if($fertRate !== null)
                                    <span class="badge bg-info text-white">{{ $fertRate }}%</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $log->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-3 text-muted">{{ __('poultry.empty.no_egg_logs') ?? 'لا توجد سجلات بيض مسجلة لهذه السلالة.' }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection