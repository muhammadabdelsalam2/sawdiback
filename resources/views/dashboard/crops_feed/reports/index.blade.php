@extends('layouts.customer.dashboard')

@section('title', __('crops_feed.titles.reports') !== 'crops_feed.titles.reports' ? __('crops_feed.titles.reports') : 'تقارير المحاصيل والأعلاف')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    @php
        $currentLocale = request()->route('locale') ?? app()->getLocale();
        $isArabic = str_starts_with(strtolower($currentLocale), 'ar');

        $sectionLabels = [
            'poultry_broiler' => $isArabic ? 'دواجن - لاحم' : 'Poultry - Broiler',
            'poultry_layer'   => $isArabic ? 'دواجن - بياض' : 'Poultry - Layer',
            'breeding'        => $isArabic ? 'سلالات وأمهات' : 'Breeding & Parents',
            'goats'           => $isArabic ? 'الماعز' : 'Goats',
            'rabbits'         => $isArabic ? 'الأرانب' : 'Rabbits',
            'fish'            => $isArabic ? 'الأسماك' : 'Fish',
            'other'           => $isArabic ? 'أخرى' : 'Other',
        ];
    @endphp

    <div class="container py-4 livestock-page">
        <div class="page-head d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
            <h2 class="page-title mb-0">{{ __('crops_feed.titles.reports') !== 'crops_feed.titles.reports' ? __('crops_feed.titles.reports') : ($isArabic ? 'تقارير وتحليلات الأعلاف والمحاصيل' : 'Crops & Feed Reports') }}</h2>
            <form method="GET" action="{{ route('customer.crops-feed.reports.index', ['locale' => $currentLocale]) }}" class="d-flex gap-2">
                <input type="month" name="month" class="form-control" value="{{ $month }}">
                <button class="btn btn-primary-green" type="submit">
                    <i class="fas fa-filter mr-1"></i> {{ __('crops_feed.actions.filter') !== 'crops_feed.actions.filter' ? __('crops_feed.actions.filter') : ($isArabic ? 'تصفية الشهر' : 'Filter') }}
                </button>
            </form>
        </div>

        {{-- بطاقات الملخص والمؤشرات الشهرية --}}
        <div class="card-block mb-4">
            <div class="row g-3 text-center text-md-start">
                <div class="col-md-3">
                    <div class="p-3 border rounded bg-light h-100">
                        <span class="text-muted d-block small font-weight-bold">{{ __('crops_feed.fields.monthly_feed_cost') !== 'crops_feed.fields.monthly_feed_cost' ? __('crops_feed.fields.monthly_feed_cost') : ($isArabic ? 'إجمالي تكلفة العلف الشهرية' : 'Monthly Feed Cost') }}</span>
                        <h4 class="font-weight-bold text-danger mt-2 mb-0">{{ number_format((float)$monthlyFeedCost, 2) }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 border rounded bg-light h-100">
                        <span class="text-muted d-block small font-weight-bold">{{ __('crops_feed.fields.feed_production') !== 'crops_feed.fields.feed_production' ? __('crops_feed.fields.feed_production') : ($isArabic ? 'إنتاج المزرعة المحول لأعلاف' : 'Farm Feed Production') }}</span>
                        <h4 class="font-weight-bold text-success mt-2 mb-0">{{ number_format((float)$feedProduction, 2) }} <small class="text-muted fs-6">{{ $isArabic ? 'طن' : 'ton' }}</small></h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 border rounded bg-light h-100">
                        <span class="text-muted d-block small font-weight-bold">{{ __('crops_feed.fields.feed_need') !== 'crops_feed.fields.feed_need' ? __('crops_feed.fields.feed_need') : ($isArabic ? 'إجمالي الكمية المستهلكة' : 'Consumed Feed') }}</span>
                        <h4 class="font-weight-bold text-primary mt-2 mb-0">{{ number_format((float)$feedNeed, 2) }} <small class="text-muted fs-6">{{ $isArabic ? 'كجم' : 'kg' }}</small></h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 border rounded bg-light h-100">
                        <span class="text-muted d-block small font-weight-bold">{{ __('crops_feed.fields.coverage_status') !== 'crops_feed.fields.coverage_status' ? __('crops_feed.fields.coverage_status') : ($isArabic ? 'نسبة تغطية الإنتاج الذاتي' : 'Self-Coverage') }}</span>
                        <div class="mt-2">
                            @if(($feedProduction * 1000) >= $feedNeed && $feedNeed > 0)
                                <span class="badge badge-success p-2 fs-6">{{ $isArabic ? 'مغطى ذاتياً بالكامل' : 'Fully Covered' }}</span>
                            @else
                                <span class="badge badge-warning text-dark p-2 fs-6">{{ $isArabic ? 'بحاجة لتوريد خارجي' : 'External Supply Needed' }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- جدول توزيع استهلاك وتكاليف الأعلاف على الأنشطة والقطاعات --}}
        <div class="card-block mb-4">
            <h5 class="section-title mb-3">
                <i class="fas fa-layer-group text-primary mr-2"></i> {{ $isArabic ? 'توزيع استهلاك وتكاليف الأعلاف حسب القطاعات والأنشطة' : 'Feed Consumption & Cost by Target Section' }}
            </h5>
            <div class="table-container">
                <table class="table registry-table mb-0 text-center align-middle">
                    <thead class="thead-light">
                    <tr>
                        <th>{{ $isArabic ? 'القطاع / النشاط المستفيد' : 'Target Section' }}</th>
                        <th>{{ $isArabic ? 'إجمالي الكمية المستهلكة (كجم)' : 'Total Quantity (kg)' }}</th>
                        <th>{{ __('crops_feed.fields.total_cost') !== 'crops_feed.fields.total_cost' ? __('crops_feed.fields.total_cost') : ($isArabic ? 'إجمالي التكلفة' : 'Total Cost') }}</th>
                        <th>{{ $isArabic ? 'نسبة التكلفة من الإجمالي' : 'Cost Share %' }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($costByDepartment ?? [] as $dept)
                        @php
                            $share = $monthlyFeedCost > 0 ? (($dept->total_cost / $monthlyFeedCost) * 100) : 0;
                        @endphp
                        <tr>
                            <td class="font-weight-bold text-dark">
                                {{ $sectionLabels[$dept->target_section] ?? ($dept->target_section ?: ($isArabic ? 'عام / غير محدد' : 'General / Unassigned')) }}
                            </td>
                            <td>{{ number_format((float)$dept->total_quantity, 2) }}</td>
                            <td class="font-weight-bold text-primary">{{ number_format((float)$dept->total_cost, 2) }}</td>
                            <td>
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px; max-width: 100px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ min(100, $share) }}%"></div>
                                    </div>
                                    <span class="small font-weight-bold">{{ number_format($share, 1) }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted py-3">{{ $isArabic ? 'لا توجد بيانات استهلاك موزعة لهذا الشهر.' : 'No section consumption data recorded for this month.' }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- جدول استهلاك العلف بحسب الحيوان الفردي --}}
        <div class="card-block mb-4">
            <h5 class="section-title mb-3">
                <i class="fas fa-paw text-success mr-2"></i> {{ __('crops_feed.fields.monthly_feed_cost') }} - {{ __('crops_feed.fields.animal') }}
            </h5>
            <div class="table-container">
                <table class="table registry-table mb-0 text-center align-middle">
                    <thead class="thead-light">
                    <tr>
                        <th>{{ __('crops_feed.fields.animal') }}</th>
                        <th>{{ $isArabic ? 'الكمية المستهلكة (كجم)' : 'Quantity (kg)' }}</th>
                        <th>{{ __('crops_feed.fields.total_cost') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($costPerAnimal as $row)
                        <tr>
                            <td class="font-weight-bold text-dark">{{ $row->animal?->tag_number ?? '-' }}</td>
                            <td>{{ number_format((float) ($row->total_quantity ?? 0), 2) }}</td>
                            <td class="font-weight-bold text-primary">{{ number_format((float) $row->total_cost, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-muted py-3">{{ __('crops_feed.empty.no_cost_per_animal') !== 'crops_feed.empty.no_cost_per_animal' ? __('crops_feed.empty.no_cost_per_animal') : ($isArabic ? 'لا توجد بيانات استهلاك فردي مسجلة.' : 'No animal-specific consumption found.') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- جدول تنبيهات انخفاض مخزون الأعلاف --}}
        <div class="card-block">
            <h5 class="section-title mb-3">
                <i class="fas fa-exclamation-triangle text-danger mr-2"></i> {{ __('dashboard.stats.low_stock_alert') !== 'dashboard.stats.low_stock_alert' ? __('dashboard.stats.low_stock_alert') : ($isArabic ? 'تنبيهات نقص وانخفاض مخزون الأعلاف' : 'Low Stock Alerts') }}
            </h5>
            <div class="table-container">
                <table class="table registry-table mb-0 text-center align-middle">
                    <thead class="thead-light">
                    <tr>
                        <th>{{ __('crops_feed.fields.feed_type') }}</th>
                        <th>{{ __('crops_feed.fields.stock_on_hand') }} ({{ $isArabic ? 'كجم' : 'kg' }})</th>
                        <th>{{ __('crops_feed.fields.low_stock_threshold') }} ({{ $isArabic ? 'كجم' : 'kg' }})</th>
                        <th>{{ __('crops_feed.fields.status') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($lowStockRows as $row)
                        <tr>
                            <td class="font-weight-bold text-dark">{{ $row['feedType']->name }}</td>
                            <td class="font-weight-bold text-danger">{{ number_format((float) $row['stock_on_hand'], 2) }}</td>
                            <td>{{ number_format((float) $row['feedType']->low_stock_threshold, 2) }}</td>
                            <td>
                                    <span class="badge badge-danger p-2">
                                        <i class="fas fa-exclamation-circle mr-1"></i> {{ $isArabic ? 'أوشك على النفاد' : 'Critical Low' }}
                                    </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted py-3 text-success font-weight-bold"><i class="fas fa-check-circle mr-1"></i> {{ __('crops_feed.empty.no_low_stock') !== 'crops_feed.empty.no_low_stock' ? __('crops_feed.empty.no_low_stock') : ($isArabic ? 'جميع أرصدة الأعلاف في المستويات الآمنة.' : 'All feed stocks are at safe levels.') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
