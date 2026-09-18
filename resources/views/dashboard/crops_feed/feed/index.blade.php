@extends('layouts.customer.dashboard')

@section('title', __('crops_feed.titles.feed_management') !== 'crops_feed.titles.feed_management' ? __('crops_feed.titles.feed_management') : 'إدارة ومخزون الأعلاف')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    @php
        $currentLocale = request()->route('locale') ?? app()->getLocale();
        $isArabic = str_starts_with(strtolower($currentLocale), 'ar');
    @endphp

    <div class="container py-4 livestock-page">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="page-title mb-0">{{ __('crops_feed.titles.feed_management') !== 'crops_feed.titles.feed_management' ? __('crops_feed.titles.feed_management') : ($isArabic ? 'إدارة ومخزون الأعلاف' : 'Feed Management') }}</h2>
            <a href="{{ route('customer.crops-feed.feed.reports', ['locale' => $currentLocale]) }}" class="btn btn-outline-white">
                <i class="fas fa-chart-pie mr-1"></i> {{ $isArabic ? 'تقارير استهلاك وتكاليف الأعلاف' : 'Feed Reports' }}
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close btn-close" data-dismiss="alert" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close btn-close" data-dismiss="alert" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                <button type="button" class="close btn-close" data-dismiss="alert" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- 1. جدول أرصدة الأعلاف الحالية وتنبيهات المخزون --}}
        <div class="card-block mb-4">
            <h5 class="section-title mb-3">
                <i class="fas fa-warehouse text-primary mr-2"></i> {{ $isArabic ? 'أرصدة مخزون الأعلاف الحالية' : 'Current Feed Stocks' }}
            </h5>
            <div class="table-container mb-0">
                <table class="table registry-table mb-0 text-center align-middle">
                    <thead class="thead-light">
                    <tr>
                        <th>{{ __('crops_feed.fields.feed_type') !== 'crops_feed.fields.feed_type' ? __('crops_feed.fields.feed_type') : ($isArabic ? 'نوع العلف' : 'Feed Type') }}</th>
                        <th>{{ __('crops_feed.fields.stock_on_hand') !== 'crops_feed.fields.stock_on_hand' ? __('crops_feed.fields.stock_on_hand') : ($isArabic ? 'الرصيد المتاح (كجم)' : 'Stock on Hand (kg)') }}</th>
                        <th>{{ __('crops_feed.fields.low_stock_threshold') !== 'crops_feed.fields.low_stock_threshold' ? __('crops_feed.fields.low_stock_threshold') : ($isArabic ? 'حد إعادة الطلب' : 'Low Stock Threshold') }}</th>
                        <th>{{ __('crops_feed.fields.status') !== 'crops_feed.fields.status' ? __('crops_feed.fields.status') : ($isArabic ? 'الحالة' : 'Status') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($stocks as $stock)
                        <tr>
                            <td class="font-weight-bold text-dark">{{ $stock['feedType']->name }}</td>
                            <td class="font-weight-bold {{ $stock['is_low_stock'] ? 'text-danger' : 'text-success' }}">
                                {{ number_format((float)$stock['stock_on_hand'], 2) }}
                            </td>
                            <td>{{ number_format((float)$stock['feedType']->low_stock_threshold, 2) }}</td>
                            <td>
                                    <span class="badge badge-{{ $stock['is_low_stock'] ? 'danger' : 'success' }}">
                                        {{ $stock['is_low_stock'] ? ($isArabic ? 'مخزون منخفض' : 'Low Stock') : ($isArabic ? 'متاح ومغطى' : 'In Stock') }}
                                    </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted py-3">{{ $isArabic ? 'لا توجد أنواع أعلاف مسجلة' : 'No feed types found' }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 2. تسجيل حركة مخزون مع تحديد المصدر (مشترى بالكيلو/الطن مقابل مزروع داخلياً) --}}
        <div class="card-block mb-4">
            <h5 class="section-title mb-3">
                <i class="fas fa-dolly text-success mr-2"></i> {{ __('crops_feed.actions.record_stock_movement') !== 'crops_feed.actions.record_stock_movement' ? __('crops_feed.actions.record_stock_movement') : ($isArabic ? 'تسجيل توريد / حركة مخزون علف' : 'Record Stock Movement') }}
            </h5>
            <form method="POST" action="{{ route('customer.crops-feed.feed.stock-movements.store', ['locale' => $currentLocale]) }}" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">{{ __('crops_feed.fields.feed_type') }} <span class="text-danger">*</span></label>
                    <select name="feed_type_id" class="form-select" required>
                        @foreach($feedTypes as $feedType)
                            <option value="{{ $feedType->id }}">{{ $feedType->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label font-weight-bold">{{ $isArabic ? 'مصدر العلف' : 'Feed Source' }} <span class="text-danger">*</span></label>
                    <select name="source_type" class="form-select" required>
                        <option value="purchased">{{ $isArabic ? 'تم شراؤه (مورد خارجي)' : 'Purchased' }}</option>
                        <option value="farm_crop">{{ $isArabic ? 'تم زراعته داخل المزرعة' : 'Farm Harvested' }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label font-weight-bold">{{ __('crops_feed.fields.movement_type') }} <span class="text-danger">*</span></label>
                    <select name="movement_type" class="form-select" required>
                        <option value="in">{{ $isArabic ? 'وارد / إضافة رصيد' : 'In (Stock Add)' }}</option>
                        <option value="out">{{ $isArabic ? 'صادر / تسوية خصم' : 'Out (Reduction)' }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label font-weight-bold">{{ $isArabic ? 'وحدة الشراء/التوريد' : 'Unit' }} <span class="text-danger">*</span></label>
                    <select name="unit" class="form-select" required>
                        <option value="kg">{{ $isArabic ? 'كيلوجرام (كجم)' : 'KG' }}</option>
                        <option value="ton">{{ $isArabic ? 'طن (1000 كجم)' : 'Ton' }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">{{ __('crops_feed.fields.quantity') }} <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="quantity" class="form-control" placeholder="0.00" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">{{ __('crops_feed.fields.unit_cost') }} ({{ $isArabic ? 'سعر الوحدة' : 'Unit Cost' }})</label>
                    <input type="number" step="0.01" min="0" name="unit_cost" class="form-control" placeholder="0.00">
                </div>
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">{{ __('crops_feed.fields.movement_date') }} <span class="text-danger">*</span></label>
                    <input type="date" name="movement_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label font-weight-bold">{{ __('crops_feed.fields.notes') }}</label>
                    <input type="text" name="notes" class="form-control" placeholder="{{ $isArabic ? 'اسم المورد، الفاتورة أو المزرعة المنتجة...' : 'Supplier name, invoice or source farm...' }}">
                </div>
                <div class="col-12">
                    <button class="btn btn-primary-green" type="submit">{{ __('crops_feed.actions.save') }}</button>
                </div>
            </form>
        </div>

        {{-- 3. تسجيل استهلاك وتوزيع التكاليف على الأقسام الإنتاجية --}}
        <div class="card-block mb-4">
            <h5 class="section-title mb-3">
                <i class="fas fa-utensils text-warning mr-2"></i> {{ __('crops_feed.actions.record_consumption') !== 'crops_feed.actions.record_consumption' ? __('crops_feed.actions.record_consumption') : ($isArabic ? 'تسجيل استهلاك العلف وتوزيعه على القطاعات' : 'Record Feed Consumption & Section Cost') }}
            </h5>
            <form method="POST" action="{{ route('customer.crops-feed.feed.consumptions.store', ['locale' => $currentLocale]) }}" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">{{ __('crops_feed.fields.feed_type') }} <span class="text-danger">*</span></label>
                    <select name="feed_type_id" class="form-select" required>
                        @foreach($feedTypes as $feedType)
                            <option value="{{ $feedType->id }}">{{ $feedType->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- اختيار القطاع الإنتاجي المستهدف --}}
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">{{ $isArabic ? 'القطاع / النشاط المستهدف' : 'Target Section' }} <span class="text-danger">*</span></label>
                    <select name="target_section" class="form-select" required>
                        <option value="">{{ $isArabic ? '-- اختر القطاع المستفيد --' : '-- Select Section --' }}</option>
                        @foreach($targetSections ?? [
                            'poultry_broiler' => 'دواجن - لاحم',
                            'poultry_layer'   => 'دواجن - بياض',
                            'breeding'        => 'سلالات وأمهات',
                            'goats'           => 'الماعز',
                            'rabbits'         => 'الأرانب',
                            'fish'            => 'الأسماك',
                            'other'           => 'أخرى'
                        ] as $secKey => $secLabel)
                            <option value="{{ $secKey }}">{{ $secLabel }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- الحظيرة التابعة --}}
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">{{ $isArabic ? 'الحظيرة (اختياري)' : 'Pen (Optional)' }}</label>
                    <select name="pen_id" class="form-select">
                        <option value="">{{ $isArabic ? '-- بدون حظيرة محددة --' : '-- No Specific Pen --' }}</option>
                        @foreach($pens as $pen)
                            <option value="{{ $pen->id }}">{{ $pen->farm?->name ?? 'مزرعة' }} - {{ $pen->pen_number }} ({{ $pen->type }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- الحيوان الفردي --}}
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">{{ __('crops_feed.fields.animal') }} ({{ $isArabic ? 'اختياري' : 'Optional' }})</label>
                    <select name="animal_id" class="form-select">
                        <option value="">{{ $isArabic ? '-- بدون حيوان فردي --' : '-- None --' }}</option>
                        @foreach($animals as $animal)
                            <option value="{{ $animal->id }}">{{ $animal->tag_number }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label font-weight-bold">{{ __('crops_feed.fields.group_name') }}</label>
                    <input type="text" name="group_name" class="form-control" placeholder="{{ $isArabic ? 'مثال: عنبر 3 / دورة لاحم 5' : 'e.g., Batch #4' }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label font-weight-bold">{{ __('crops_feed.fields.quantity') }} ({{ $isArabic ? 'كجم' : 'kg' }}) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="quantity" class="form-control" placeholder="0.00" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label font-weight-bold">{{ __('crops_feed.fields.consumption_date') }} <span class="text-danger">*</span></label>
                    <input type="date" name="consumption_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label font-weight-bold">{{ $isArabic ? 'تكلفة الكيلو (اختياري)' : 'Cost/kg (Optional)' }}</label>
                    <input type="number" step="0.01" min="0" name="unit_cost" class="form-control" placeholder="0.00">
                </div>

                <div class="col-12">
                    <label class="form-label font-weight-bold">{{ __('crops_feed.fields.notes') }}</label>
                    <input type="text" name="notes" class="form-control" placeholder="{{ $isArabic ? 'ملاحظات إضافية حول التغذية...' : 'Additional feeding notes...' }}">
                </div>

                <div class="col-12">
                    <button class="btn btn-primary-green" type="submit">{{ __('crops_feed.actions.save') }}</button>
                </div>
            </form>
        </div>

        {{-- 4. تحويل محصول زراعي إلى علف --}}
        <div class="card-block mb-4">
            <h5 class="section-title mb-3">
                <i class="fas fa-recycle text-info mr-2"></i> {{ __('crops_feed.actions.allocate_crop_to_feed') !== 'crops_feed.actions.allocate_crop_to_feed' ? __('crops_feed.actions.allocate_crop_to_feed') : ($isArabic ? 'تحويل محصول مزروع إلى مخزون الأعلاف' : 'Allocate Farm Crop to Feed') }}
            </h5>
            <form method="POST" action="{{ route('customer.crops-feed.feed.crop-allocations.store', ['locale' => $currentLocale]) }}" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">{{ __('crops_feed.titles.crops') }} <span class="text-danger">*</span></label>
                    <select name="crop_id" class="form-select" required>
                        @foreach($crops as $crop)
                            <option value="{{ $crop->id }}">{{ $crop->name }} ({{ $isArabic ? 'المتاح للعلف' : 'Available' }}: {{ $crop->available_for_feed_tons }} {{ $crop->yield_unit_label }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">{{ __('crops_feed.fields.feed_type') }} <span class="text-danger">*</span></label>
                    <select name="feed_type_id" class="form-select" required>
                        @foreach($feedTypes as $feedType)
                            <option value="{{ $feedType->id }}">{{ $feedType->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label font-weight-bold">{{ $isArabic ? 'الكمية المحولة' : 'Quantity' }} <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="quantity_tons" class="form-control" placeholder="0.00" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label font-weight-bold">{{ __('crops_feed.fields.allocation_date') }} <span class="text-danger">*</span></label>
                    <input type="date" name="allocation_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label font-weight-bold">{{ __('crops_feed.fields.notes') }}</label>
                    <input type="text" name="notes" class="form-control">
                </div>
                <div class="col-12">
                    <button class="btn btn-primary-green" type="submit">{{ __('crops_feed.actions.save') }}</button>
                </div>
            </form>
        </div>

        {{-- 5. جدول آخر عمليات استهلاك الأعلاف الموزعة --}}
        <div class="card-block">
            <h5 class="section-title mb-3">
                <i class="fas fa-history text-secondary mr-2"></i> {{ $isArabic ? 'سجل استهلاك وتكاليف الأعلاف الأخير' : 'Recent Feed Consumptions' }}
            </h5>
            <div class="table-container">
                <table class="table registry-table mb-0 text-center align-middle">
                    <thead class="thead-light">
                    <tr>
                        <th>{{ __('crops_feed.fields.feed_type') }}</th>
                        <th>{{ $isArabic ? 'القطاع المستفيد' : 'Target Section' }}</th>
                        <th>{{ __('crops_feed.fields.group_name') }}</th>
                        <th>{{ __('crops_feed.fields.animal') }}</th>
                        <th>{{ __('crops_feed.fields.quantity') }} ({{ $isArabic ? 'كجم' : 'kg' }})</th>
                        <th>{{ __('crops_feed.fields.unit_cost') }}</th>
                        <th>{{ $isArabic ? 'إجمالي التكلفة' : 'Total Cost' }}</th>
                        <th>{{ __('crops_feed.fields.consumption_date') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($recentConsumptions as $row)
                        <tr>
                            <td class="font-weight-bold text-dark">{{ $row->feedType?->name }}</td>
                            <td>
                                @if($row->target_section)
                                    <span class="badge badge-info">{{ $targetSections[$row->target_section] ?? $row->target_section }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $row->group_name ?? ($row->pen ? ($row->pen->farm?->name . ' - ' . $row->pen->pen_number) : '-') }}</td>
                            <td>{{ $row->animal?->tag_number ?? '-' }}</td>
                            <td class="font-weight-bold">{{ number_format((float)$row->quantity, 2) }}</td>
                            <td>{{ number_format((float)$row->unit_cost, 2) }}</td>
                            <td class="font-weight-bold text-primary">{{ number_format((float)$row->total_cost, 2) }}</td>
                            <td>{{ $row->consumption_date?->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-muted py-4">{{ __('crops_feed.empty.no_consumptions') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
