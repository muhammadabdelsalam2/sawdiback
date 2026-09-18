@extends('layouts.customer.dashboard')

@section('title', __('crops_feed.titles.crops') !== 'crops_feed.titles.crops' ? __('crops_feed.titles.crops') : 'المحاصيل الزراعية')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    @php
        $currentLocale = request()->route('locale') ?? app()->getLocale();
        $isArabic = str_starts_with(strtolower($currentLocale), 'ar');
    @endphp

    <div class="container py-4 livestock-page">
        <div class="page-head d-flex justify-content-between align-items-center mb-4">
            <h2 class="page-title mb-0">{{ __('crops_feed.titles.crops') !== 'crops_feed.titles.crops' ? __('crops_feed.titles.crops') : ($isArabic ? 'المحاصيل الزراعية' : 'Crops') }}</h2>
            <a class="btn btn-primary-green" href="{{ route('customer.crops-feed.crops.create', ['locale' => $currentLocale]) }}">
                <i class="fas fa-plus mr-1"></i> {{ __('crops_feed.actions.add_crop') !== 'crops_feed.actions.add_crop' ? __('crops_feed.actions.add_crop') : ($isArabic ? 'إضافة محصول جديد' : 'Add Crop') }}
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

        <div class="table-container shadow-sm bg-white rounded">
            <table class="table registry-table mb-0 js-livestock-table align-middle text-center">
                <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th>{{ __('crops_feed.fields.name') !== 'crops_feed.fields.name' ? __('crops_feed.fields.name') : ($isArabic ? 'المحصول' : 'Crop Name') }}</th>
                    <th>{{ __('crops_feed.fields.farm') !== 'crops_feed.fields.farm' ? __('crops_feed.fields.farm') : ($isArabic ? 'المزرعة' : 'Farm') }}</th>
                    <th>{{ __('crops_feed.fields.land_area') !== 'crops_feed.fields.land_area' ? __('crops_feed.fields.land_area') : ($isArabic ? 'المساحة' : 'Area') }}</th>
                    <th>{{ __('crops_feed.fields.planting_date') !== 'crops_feed.fields.planting_date' ? __('crops_feed.fields.planting_date') : ($isArabic ? 'تاريخ الزراعة' : 'Planting Date') }}</th>
                    <th>{{ __('crops_feed.fields.expected_harvest_date') !== 'crops_feed.fields.expected_harvest_date' ? __('crops_feed.fields.expected_harvest_date') : ($isArabic ? 'الحصاد المتوقع' : 'Harvest Date') }}</th>
                    <th>{{ __('crops_feed.fields.yield_tons') !== 'crops_feed.fields.yield_tons' ? __('crops_feed.fields.yield_tons') : ($isArabic ? 'الإنتاجية' : 'Yield') }}</th>
                    <th>{{ __('crops_feed.fields.loss_rate') !== 'crops_feed.fields.loss_rate' ? __('crops_feed.fields.loss_rate') : ($isArabic ? 'نسبة الهالك' : 'Loss Rate') }}</th>
                    <th>{{ __('crops_feed.fields.total_cost') !== 'crops_feed.fields.total_cost' ? __('crops_feed.fields.total_cost') : ($isArabic ? 'إجمالي التكلفة' : 'Total Cost') }}</th>
                    <th>{{ __('crops_feed.fields.profit_or_loss') !== 'crops_feed.fields.profit_or_loss' ? __('crops_feed.fields.profit_or_loss') : ($isArabic ? 'الربح / الخسارة' : 'Profit/Loss') }}</th>
                    <th class="no-sort">{{ __('crops_feed.fields.actions') !== 'crops_feed.fields.actions' ? __('crops_feed.fields.actions') : ($isArabic ? 'الإجراءات' : 'Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($rows as $row)
                    <tr>
                        <td>{{ $row->id }}</td>
                        <td class="font-weight-bold text-dark">{{ $row->name }}</td>
                        <td>{{ $row->farm?->name ?? '-' }}</td>
                        <td>{{ $row->land_area }}</td>
                        <td>{{ $row->planting_date?->format('Y-m-d') }}</td>
                        <td>{{ $row->expected_harvest_date?->format('Y-m-d') ?? '-' }}</td>
                        <td class="font-weight-bold">
                            @if($row->yield_tons !== null)
                                <span>{{ $row->yield_tons }}</span>
                                <span class="badge badge-light border text-muted">{{ $row->yield_unit_label }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                                <span class="badge badge-{{ (float)$row->loss_rate > 10 ? 'danger' : 'success' }}">
                                    {{ $row->loss_rate }}%
                                </span>
                        </td>
                        <td>{{ number_format((float)$row->total_cost, 2) }}</td>
                        <td class="font-weight-bold {{ (float)$row->profit_or_loss < 0 ? 'text-danger' : 'text-success' }}">
                            {{ $row->profit_or_loss !== null ? number_format((float)$row->profit_or_loss, 2) : '-' }}
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('customer.crops-feed.crops.show', ['locale' => $currentLocale, 'crop' => $row->id]) }}" title="{{ __('livestock.actions.view') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('customer.crops-feed.crops.edit', ['locale' => $currentLocale, 'crop' => $row->id]) }}" title="{{ __('livestock.actions.edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('customer.crops-feed.crops.destroy', ['locale' => $currentLocale, 'crop' => $row->id]) }}" onsubmit="return confirm('{{ $isArabic ? 'هل أنت متأكد من حذف هذا المحصول؟' : 'Are you sure you want to delete this crop?' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit" title="{{ __('crops_feed.actions.delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="py-4 text-muted">
                            {{ __('crops_feed.empty.no_crops') !== 'crops_feed.empty.no_crops' ? __('crops_feed.empty.no_crops') : ($isArabic ? 'لا توجد محاصيل مسجلة حالياً.' : 'No crops found.') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $rows->links('pagination::bootstrap-5') }}</div>
    </div>
@endsection
