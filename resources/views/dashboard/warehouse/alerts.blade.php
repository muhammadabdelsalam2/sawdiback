@extends('layouts.customer.dashboard')

@php
    $currentLocale = request()->route('locale') ?? app()->getLocale();
    $isAr = str_starts_with($currentLocale, 'ar');
    $lowStockProducts = $lowStockProducts ?? [];
    $expiringBatches = $expiringBatches ?? [];
@endphp

@section('title', $isAr ? 'تنبيهات المخزون وحدود الطلب' : 'Inventory Alerts & Thresholds')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        {{-- Header --}}
        <div class="page-head mb-4">
            <div>
                <h2 class="page-title">
                    <i class="fas fa-exclamation-triangle text-danger mr-2"></i>
                    {{ $isAr ? 'تنبيهات المخزون وحد إعادة الطلب' : 'Inventory & Stock Alerts' }}
                </h2>
                <p class="text-muted small mb-0">
                    {{ $isAr ? 'متابعة الأصناف التي اقتربت من النفاد والتشغيلات المنتهية أو القريبة من الانتهاء.' : 'Monitor items reaching reorder thresholds and expiring product batches.' }}
                </p>
            </div>
            <div class="quick-actions">
                <a class="btn btn-outline-white" href="{{ route('customer.inventory.index', ['locale' => $currentLocale]) }}">
                    <i class="fas fa-arrow-left mr-1"></i> {{ $isAr ? 'سجل المخزن' : 'Warehouse' }}
                </a>
                <a class="btn btn-primary-green" href="{{ route('customer.inventory.products.create', ['locale' => $currentLocale]) }}">
                    <i class="fas fa-plus mr-1"></i> {{ $isAr ? 'إضافة صنف' : 'Add Product' }}
                </a>
            </div>
        </div>

        {{-- 1. جدول الأصناف التي بلغت حد الطلب --}}
        <div class="card-block mb-4">
            <h5 class="section-title mb-3 font-weight-bold text-danger">
                <i class="fas fa-boxes text-danger mr-2"></i>
                {{ $isAr ? 'أصناف بلغت الحد الأدنى للمخزون' : 'Items Reaching Minimum Threshold' }}
                <span class="badge bg-danger ms-2">{{ count($lowStockProducts) }}</span>
            </h5>

            <div class="table-container">
                <table class="table registry-table mb-0 align-middle">
                    <thead>
                    <tr>
                        <th>{{ $isAr ? 'الصنف / كود SKU' : 'Product / SKU' }}</th>
                        <th>{{ $isAr ? 'التصنيف' : 'Category' }}</th>
                        <th>{{ $isAr ? 'الرصيد الحالي' : 'Current Stock' }}</th>
                        <th>{{ $isAr ? 'الحد الأدنى (إعادة الطلب)' : 'Min Threshold' }}</th>
                        <th>{{ $isAr ? 'العجز المطلوب' : 'Shortage' }}</th>
                        <th>{{ $isAr ? 'الحالة' : 'Status' }}</th>
                        <th>{{ $isAr ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($lowStockProducts as $prod)
                        @php
                            $minStock = $prod->min_stock ?? 0;
                            $currentStock = $prod->current_stock ?? 0;
                            $shortage = max(0, $minStock - $currentStock);
                            $unit = $prod->unit ?? ($isAr ? 'وحدة' : 'Unit');
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $prod->name }}</strong>
                                <small class="text-muted d-block">{{ $prod->sku ?? '-' }}</small>
                            </td>
                            <td>{{ $prod->category?->name ?? '-' }}</td>
                            <td>
                                <strong class="text-danger fs-6">{{ number_format($currentStock, 2) }} {{ $unit }}</strong>
                            </td>
                            <td>
                                {{ number_format($minStock, 2) }} {{ $unit }}
                            </td>
                            <td>
                            <span class="badge bg-danger">
                                -{{ number_format($shortage, 2) }} {{ $unit }}
                            </span>
                            </td>
                            <td>
                                @if($currentStock <= 0)
                                    <span class="badge bg-dark">{{ $isAr ? 'نفد تماماً' : 'Out of Stock' }}</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ $isAr ? 'منخفض وحرج' : 'Low Stock' }}</span>
                                @endif
                            </td>
                            <td class="d-flex gap-2">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('customer.inventory.products.edit', ['locale' => $currentLocale, 'product' => $prod->id]) }}">
                                    <i class="fas fa-edit"></i> {{ $isAr ? 'تعديل' : 'Edit' }}
                                </a>
                                <a class="btn btn-sm btn-primary-green" href="{{ route('customer.procurement.purchase-orders.create', ['locale' => $currentLocale, 'product_id' => $prod->id]) }}">
                                    <i class="fas fa-shopping-cart"></i> {{ $isAr ? 'أمر شراء' : 'Order' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-check-circle text-success fa-2x d-block mb-2"></i>
                                {{ $isAr ? 'المخزون في الحدود الآمنة ولا توجد أصناف منخفضة حالياً.' : 'Stock is within safe levels.' }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 2. جدول التشغيلات المنتهية أو القريبة من الانتهاء --}}
        @if(count($expiringBatches) > 0)
            <div class="card-block">
                <h5 class="section-title mb-3 font-weight-bold text-warning">
                    <i class="fas fa-calendar-times text-warning mr-2"></i>
                    {{ $isAr ? 'تشغيلات قريبة من تاريخ الانتهاء (أقل من 30 يوماً)' : 'Batches Expiring Soon (Within 30 Days)' }}
                </h5>

                <div class="table-container">
                    <table class="table registry-table mb-0 align-middle">
                        <thead>
                        <tr>
                            <th>{{ $isAr ? 'رقم التشغيلة' : 'Batch No.' }}</th>
                            <th>{{ $isAr ? 'الصنف' : 'Product' }}</th>
                            <th>{{ $isAr ? 'الكمية المتبقية' : 'Remaining Qty' }}</th>
                            <th>{{ $isAr ? 'تاريخ الانتهاء' : 'Expiry Date' }}</th>
                            <th>{{ $isAr ? 'المتبقي' : 'Days Left' }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($expiringBatches as $batch)
                            @php
                                $daysLeft = (int) \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($batch->expiry_date), false);
                            @endphp
                            <tr>
                                <td><strong>{{ $batch->batch_number }}</strong></td>
                                <td>{{ $batch->product?->name ?? '-' }}</td>
                                <td><strong>{{ number_format($batch->quantity, 2) }}</strong></td>
                                <td>{{ \Carbon\Carbon::parse($batch->expiry_date)->format('Y-m-d') }}</td>
                                <td>
                                    @if($daysLeft <= 0)
                                        <span class="badge bg-danger">{{ $isAr ? 'منتهية الصلاحية' : 'Expired' }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ $daysLeft }} {{ $isAr ? 'يوم' : 'days' }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
