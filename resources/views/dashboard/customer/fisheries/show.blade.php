@extends('layouts.customer.dashboard')

@php
    $currentLocale = request()->route('locale') ?? app()->getLocale();
    $isAr = str_starts_with($currentLocale, 'ar');
@endphp

@section('title', ($isAr ? 'تفاصيل دورة الحوض السمكي: ' : 'Fish Pond Details: ') . $batch->pond_name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        {{-- Header --}}
        <div class="page-head">
            <h2 class="page-title">
                {{ $isAr ? 'تفاصيل الحوض' : 'Pond Details' }}: {{ $batch->pond_name }}
                <small class="text-muted">({{ $batch->batch_code }})</small>
            </h2>
            <div class="d-flex gap-2">
                <a class="btn btn-sm btn-outline-white" href="{{ route('customer.fisheries.edit', ['locale' => $currentLocale, 'fish_batch' => $batch->id]) }}">
                    <i class="fas fa-edit mr-1"></i> {{ $isAr ? 'تعديل' : 'Edit' }}
                </a>
                <a class="btn btn-sm btn-outline-white" href="{{ route('customer.fisheries.index', ['locale' => $currentLocale]) }}">
                    {{ $isAr ? 'رجوع للأحواض' : 'Back' }}
                </a>
            </div>
        </div>

        {{-- تنبيهات النجاح --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- بطاقات الملخص والمؤشرات العلوية --}}
        <div class="card-block mb-3">
            <div class="row g-3 text-center align-items-center">
                <div class="col-md-2 col-6">
                    <span class="text-muted d-block small">{{ $isAr ? 'المزرعة' : 'Farm' }}</span>
                    <strong>{{ $batch->farm?->name ?? '-' }}</strong>
                </div>
                <div class="col-md-2 col-6">
                    <span class="text-muted d-block small">{{ $isAr ? 'العدد الحي الحالي' : 'Live Count' }}</span>
                    <strong class="text-primary fs-5">{{ number_format($batch->current_count) }}</strong>
                </div>
                <div class="col-md-2 col-6">
                    <span class="text-muted d-block small">{{ $isAr ? 'نسبة النفوق' : 'Mortality Rate' }}</span>
                    <strong class="{{ $mortalityRate > 5 ? 'text-danger' : 'text-success' }} fs-5">
                        %{{ $mortalityRate }} <small class="text-muted">({{ number_format($batch->mortality_count) }})</small>
                    </strong>
                </div>
                <div class="col-md-2 col-6">
                    <span class="text-muted d-block small">{{ $isAr ? 'استهلاك العلف' : 'Feed Consumed' }}</span>
                    <strong>
                        {{ number_format($batch->feed_consumed_kg, 2) }} {{ $isAr ? 'كجم' : 'kg' }}
                        @if($batch->feed_consumed_kg >= 1000)
                            <small class="text-muted d-block">({{ $feedTon }} {{ $isAr ? 'طن' : 'ton' }})</small>
                        @endif
                    </strong>
                </div>
                <div class="col-md-2 col-6">
                    <span class="text-muted d-block small">{{ $isAr ? 'إجمالي التكلفة' : 'Total Cost' }}</span>
                    <strong class="text-danger fs-5">{{ number_format($batch->total_cost, 2) }}</strong>
                </div>
                <div class="col-md-2 col-6">
                    <span class="text-muted d-block small">{{ $isAr ? 'الحالة' : 'Status' }}</span>
                    @php
                        $statusClass = match($batch->status) {
                            'active' => 'bg-success',
                            'harvested' => 'bg-info text-dark',
                            default => 'bg-secondary'
                        };
                        $statusName = match($batch->status) {
                            'active' => ($isAr ? 'نشط' : 'Active'),
                            'harvested' => ($isAr ? 'تم الحصاد' : 'Harvested'),
                            default => ($isAr ? 'متوقف' : 'Paused')
                        };
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ $statusName }}</span>
                </div>
            </div>
        </div>

        {{-- 1. نموذج تسجيل وجبة علف مع ربط المخزن والخصم المباشر --}}
        <div class="card-block mb-3">
            <h5 class="section-title mb-3 font-weight-bold">
                <i class="fas fa-utensils text-warning mr-1"></i> {{ $isAr ? 'تسجيل استهلاك العلف (خصم من المخزن)' : 'Record Feed & Deduct Stock' }}
            </h5>
            <form method="POST" action="{{ route('customer.fisheries.feeding', ['locale' => $currentLocale, 'fish_batch' => $batch->id]) }}" class="row g-2 align-items-center">
                @csrf
                <div class="col-md-3">
                    <select name="product_id" class="form-select">
                        <option value="">{{ $isAr ? '-- اختر صنف العلف من المخزن (اختياري) --' : '-- Select Feed Item (Optional) --' }}</option>
                        @foreach($feedProducts ?? [] as $feedProd)
                            <option value="{{ $feedProd->id }}">
                                {{ $feedProd->name }} ({{ $isAr ? 'المتاح' : 'Stock' }}: {{ number_format($feedProd->current_stock, 1) }} {{ $feedProd->unit }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" min="0.01" name="quantity" class="form-control" placeholder="{{ $isAr ? 'الكمية المستهلكة' : 'Quantity' }}" required>
                </div>
                <div class="col-md-2">
                    <select name="unit" class="form-select" required>
                        <option value="kg">{{ $isAr ? 'كيلو (كجم)' : 'Kilogram (kg)' }}</option>
                        <option value="ton">{{ $isAr ? 'طن' : 'Ton' }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" min="0" name="cost" class="form-control" placeholder="{{ $isAr ? 'التكلفة (تلقائية إن لم تُحدد)' : 'Cost (Auto calculated)' }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="feed_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary-green w-100">{{ $isAr ? 'حفظ' : 'Save' }}</button>
                </div>
                <div class="col-12 mt-1">
                    <input type="text" name="notes" class="form-control form-control-sm" placeholder="{{ $isAr ? 'ملاحظات إضافية حول التغذية (اختياري)...' : 'Feeding notes (Optional)...' }}">
                </div>
            </form>
        </div>

        {{-- 2. نموذج تسجيل النفوق --}}
        <div class="card-block mb-3">
            <h5 class="section-title mb-3 font-weight-bold">
                <i class="fas fa-heart-broken text-danger mr-1"></i> {{ $isAr ? 'تسجيل النفوق' : 'Record Fish Mortality' }}
            </h5>
            <form method="POST" action="{{ route('customer.fisheries.mortality', ['locale' => $currentLocale, 'fish_batch' => $batch->id]) }}" class="row g-2 align-items-center">
                @csrf
                <div class="col-md-3">
                    <input type="date" name="mortality_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-3">
                    <input type="number" min="1" step="1" name="count" class="form-control" placeholder="{{ $isAr ? 'عدد الأسماك النافقة' : 'Dead Count' }}" required>
                </div>
                <div class="col-md-4">
                    <input type="text" name="notes" class="form-control" placeholder="{{ $isAr ? 'ملاحظات أو سبب النفوق' : 'Notes / Cause of mortality' }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary-green w-100">{{ $isAr ? 'حفظ' : 'Save' }}</button>
                </div>
            </form>
        </div>

        {{-- 3. نموذج تسجيل الحصاد والمبيعات --}}
        <div class="card-block mb-3">
            <h5 class="section-title mb-3 font-weight-bold">
                <i class="fas fa-cash-register text-success mr-1"></i> {{ $isAr ? 'تسجيل حصاد وتسويق الأسماك' : 'Record Harvest & Sales' }}
            </h5>
            <form method="POST" action="{{ route('customer.fisheries.harvest', ['locale' => $currentLocale, 'fish_batch' => $batch->id]) }}" class="row g-2 align-items-center">
                @csrf
                <div class="col-md-2">
                    <input type="date" name="harvest_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-2">
                    <select name="harvest_type" class="form-select" required>
                        <option value="partial">{{ $isAr ? 'حصاد جزئي (تخفيف)' : 'Partial Harvest' }}</option>
                        <option value="total">{{ $isAr ? 'حصاد كلي (إنهاء الدورة)' : 'Total Harvest' }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" min="0.01" name="weight" class="form-control" placeholder="{{ $isAr ? 'الوزن المحصود' : 'Weight' }}" required>
                </div>
                <div class="col-md-2">
                    <select name="unit" class="form-select" required>
                        <option value="kg">{{ $isAr ? 'كيلو (كجم)' : 'Kilogram (kg)' }}</option>
                        <option value="ton">{{ $isAr ? 'طن' : 'Ton' }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" min="0" name="total_sales" class="form-control" placeholder="{{ $isAr ? 'إجمالي المبيعات' : 'Total Sales' }}" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary-green w-100">{{ $isAr ? 'حفظ' : 'Save' }}</button>
                </div>
                <div class="col-12 mt-1">
                    <input type="text" name="notes" class="form-control form-control-sm" placeholder="{{ $isAr ? 'ملاحظات الحصاد والتسويق (اختياري)...' : 'Harvest notes (Optional)...' }}">
                </div>
            </form>
        </div>
    </div>
@endsection
