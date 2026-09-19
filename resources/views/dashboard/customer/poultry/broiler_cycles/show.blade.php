@extends('layouts.customer.dashboard')

@section('title', __('poultry.titles.broiler_cycle_details'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <div class="page-head">
            <h2 class="page-title">{{ __('poultry.titles.broiler_cycle_details') }}: {{ $cycle->cycle_number }}</h2>
            <div class="d-flex gap-2">
                <a class="btn btn-sm btn-outline-white" href="{{ route('customer.poultry.broiler-cycles.edit', ['locale' => $currentLocale, 'broiler_cycle' => $cycle->id]) }}">
                    <i class="fas fa-edit mr-1"></i> {{ __('poultry.actions.edit') }}
                </a>
                <a class="btn btn-sm btn-outline-white" href="{{ route('customer.poultry.broiler-cycles.index', ['locale' => $currentLocale]) }}">
                    {{ __('poultry.actions.back') }}
                </a>
            </div>
        </div>

        @include('dashboard.customer.poultry.partials.flash')

        {{-- الملخص العام ومؤشرات الأداء (FCR) --}}
        <div class="card-block mb-3">
            <div class="row g-3">
                <div class="col-md-2">
                    <strong>{{ __('poultry.fields.age_days') }}:</strong>
                    <span>{{ $cycle->age_days }}</span>
                </div>
                <div class="col-md-2">
                    <strong>{{ __('poultry.fields.current_birds') }}:</strong>
                    <span class="text-primary font-weight-bold">{{ number_format($metrics['current_live_birds'] ?? 0) }}</span>
                </div>
                <div class="col-md-2">
                    <strong>{{ __('poultry.fields.mortality_rate') }}:</strong>
                    <span class="{{ ($metrics['mortality_rate'] ?? 0) > 5 ? 'text-danger' : 'text-success' }}">
                        {{ $metrics['mortality_rate'] ?? 0 }}% ({{ $metrics['total_mortality'] ?? 0 }})
                    </span>
                </div>
                <div class="col-md-2">
                    <strong>{{ __('poultry.fields.fcr') }}:</strong>
                    <span class="badge bg-info text-dark">{{ $metrics['fcr'] ?? 0 }}</span>
                </div>
                <div class="col-md-2">
                    <strong>{{ __('poultry.fields.feed_consumption') }}:</strong>
                    <span>{{ number_format($metrics['total_feed_kg'] ?? 0, 2) }} كجم</span>
                </div>
                <div class="col-md-2">
                    <strong>{{ __('poultry.fields.net_profit') }}:</strong>
                    <span class="font-weight-bold {{ ($metrics['net_profit'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($metrics['net_profit'] ?? 0, 2) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- نموذج تسجيل التكاليف والأعلاف والتشغيل --}}
        <div class="card-block mb-3">
            <h5 class="section-title mb-3 font-weight-bold">
                <i class="fas fa-coins text-warning mr-1"></i> {{ __('poultry.actions.record_cost') }}
            </h5>
            <form method="POST" action="{{ route('customer.poultry.broiler-cycles.costs.store', ['locale' => $currentLocale, 'broiler_cycle' => $cycle->id]) }}" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <select name="cost_type" id="cost_type_select" class="form-select" required>
                        <option value="feed">{{ __('poultry.options.feed') ?? 'أعلاف وتغذية' }}</option>
                        <option value="electricity">كهرباء وطاقة</option>
                        <option value="water">مياه وري</option>
                        <option value="fuel">وقود وتدفئة (ديزل / غاز)</option>
                        <option value="transport">نقل وتوصيل</option>
                        <option value="chicks_purchase">{{ __('poultry.options.chicks_purchase') ?? 'شراء كتاكيت' }}</option>
                        <option value="slaughter_packaging">{{ __('poultry.options.slaughter_packaging') ?? 'تجهيز وتغليف' }}</option>
                        <option value="other">{{ __('poultry.options.other') ?? 'مصاريف أخرى' }}</option>
                    </select>
                </div>
                <div class="col-md-2" id="feed_quantity_wrapper">
                    <input type="number" step="0.01" min="0" name="quantity_kg" class="form-control" placeholder="الكمية (كجم)">
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" min="0.01" name="amount" class="form-control" placeholder="{{ __('poultry.fields.amount') }}" required>
                </div>
                <div class="col-md-2">
                    <input type="date" name="cost_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="notes" class="form-control" placeholder="{{ __('poultry.fields.notes') }}">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary-green w-100">{{ __('poultry.actions.save') }}</button>
                </div>
            </form>
        </div>

        {{-- نموذج تسجيل المبيعات --}}
        <div class="card-block mb-3">
            <h5 class="section-title mb-3 font-weight-bold">
                <i class="fas fa-cash-register text-success mr-1"></i> {{ __('poultry.actions.record_sale') }}
            </h5>
            <form method="POST" action="{{ route('customer.poultry.broiler-cycles.sales.store', ['locale' => $currentLocale, 'broiler_cycle' => $cycle->id]) }}" class="row g-3">
                @csrf
                <div class="col-md-2">
                    <input type="date" name="sale_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-2">
                    <input type="number" step="1" min="1" name="quantity" class="form-control" placeholder="عدد الطيور" required>
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" min="0" name="weight_kg" class="form-control" placeholder="الوزن الإجمالي (كجم)">
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" min="0.01" name="unit_price" class="form-control" placeholder="{{ __('poultry.fields.unit_price') }}" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="customer_name" class="form-control" placeholder="{{ __('poultry.fields.customer_name') }}">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary-green w-100">{{ __('poultry.actions.save') }}</button>
                </div>
            </form>
        </div>

        {{-- نموذج تسجيل النفوق اليومي --}}
        <div class="card-block mb-4">
            <h5 class="section-title mb-3 font-weight-bold">
                <i class="fas fa-heart-broken text-danger mr-1"></i> {{ __('poultry.actions.record_mortality') }}
            </h5>
            <form method="POST" action="{{ route('customer.poultry.broiler-cycles.mortalities.store', ['locale' => $currentLocale, 'broiler_cycle' => $cycle->id]) }}" class="row g-3">
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

        {{-- جداول السجلات المرتبطة بالدورة --}}
        <div class="card-block">
            <h5 class="border-bottom pb-2 mb-3 font-weight-bold">
                <i class="fas fa-history mr-1"></i> سجل تكاليف وأعلاف الدورة
            </h5>
            <div class="table-container mb-4">
                <table class="table registry-table mb-0">
                    <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>نوع التكلفة</th>
                        <th>الكمية (كجم)</th>
                        <th>المبلغ</th>
                        <th>ملاحظات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($cycle->costs as $cost)
                        <tr>
                            <td>{{ $cost->cost_date ? \Carbon\Carbon::parse($cost->cost_date)->format('Y-m-d') : '-' }}</td>
                            <td>
                                @php
                                    $costLabel = match($cost->cost_type) {
                                        'feed' => 'أعلاف وتغذية',
                                        'electricity' => 'كهرباء وطاقة',
                                        'water' => 'مياه وري',
                                        'fuel' => 'وقود وتدفئة',
                                        'transport' => 'نقل وتوصيل',
                                        'chicks_purchase' => 'شراء كتاكيت',
                                        'slaughter_packaging' => 'تجهيز وتغليف',
                                        default => $cost->cost_type
                                    };
                                    $badgeBg = match($cost->cost_type) {
                                        'feed' => 'bg-warning text-dark',
                                        'electricity' => 'bg-primary text-white',
                                        'water' => 'bg-info text-dark',
                                        'fuel' => 'bg-secondary text-white',
                                        'transport' => 'bg-dark text-white',
                                        default => 'bg-light text-dark border'
                                    };
                                @endphp
                                <span class="badge {{ $badgeBg }}">
                                        {{ $costLabel }}
                                    </span>
                            </td>
                            <td>{{ $cost->quantity_kg ? number_format($cost->quantity_kg, 2) : '-' }}</td>
                            <td class="text-danger font-weight-bold">{{ number_format((float)$cost->amount, 2) }}</td>
                            <td>{{ $cost->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">{{ __('poultry.empty.no_costs') ?? 'لا توجد تكاليف مسجلة حتى الآن.' }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const costType = document.getElementById('cost_type_select');
            const qtyWrapper = document.getElementById('feed_quantity_wrapper');
            if (costType && qtyWrapper) {
                function toggleQty() {
                    qtyWrapper.style.display = costType.value === 'feed' ? 'block' : 'none';
                }
                costType.addEventListener('change', toggleQty);
                toggleQty();
            }
        });
    </script>
@endsection
