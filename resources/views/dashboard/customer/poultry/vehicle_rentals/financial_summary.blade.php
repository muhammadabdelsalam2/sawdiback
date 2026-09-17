@extends('layouts.customer.dashboard')

@section('title', __('poultry.fields.net_profit') . ' - ' . __('poultry.titles.vehicle_rentals'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
@php
    $currentLocale = request()->route('locale') ?? app()->getLocale();
@endphp
<div class="container py-4 livestock-page">
    <div class="page-head">
        <h2 class="page-title">
            <i class="fas fa-chart-line text-success me-2"></i>
            {{ __('poultry.titles.vehicle_rentals') }} - {{ __('poultry.fields.net_profit') }}
        </h2>
        <div class="d-flex gap-2">
            <a class="btn btn-primary-green" href="{{ route('customer.poultry.vehicle-rentals.create', ['locale' => $currentLocale]) }}">
                <i class="fas fa-plus mr-1"></i> {{ __('poultry.actions.add_rental') }}
            </a>
            <a class="btn btn-outline-white" href="{{ route('customer.poultry.vehicle-rentals.index', ['locale' => $currentLocale]) }}">
                {{ __('poultry.actions.back') }}
            </a>
        </div>
    </div>

    @include('dashboard.customer.poultry.partials.flash')

    {{-- بطاقات الملخص المالي الرئيسي --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card-block h-100 border-start border-success border-4">
                <div class="text-muted small mb-1">{{ __('poultry.fields.total_sales') }} ({{ __('poultry.fields.rental_fee') }})</div>
                <h3 class="font-weight-bold text-success mb-0">
                    {{ number_format((float)$totalRevenue, 2) }}
                </h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-block h-100 border-start border-danger border-4">
                <div class="text-muted small mb-1">{{ __('poultry.fields.total_costs') }} ({{ __('poultry.options.fuel') }} + {{ __('poultry.fields.driver_commission') }} + {{ __('poultry.options.other') }})</div>
                <h3 class="font-weight-bold text-danger mb-0">
                    {{ number_format((float)$totalExpenses, 2) }}
                </h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-block h-100 border-start border-primary border-4">
                <div class="text-muted small mb-1">{{ __('poultry.fields.net_profit') }}</div>
                <h3 class="font-weight-bold {{ (float)$netProfit >= 0 ? 'text-primary' : 'text-danger' }} mb-0">
                    {{ number_format((float)$netProfit, 2) }}
                </h3>
            </div>
        </div>
    </div>

    {{-- فلترة التواريخ والمركبات --}}
    <div class="card-block mb-4">
        <form method="GET" action="{{ route('customer.poultry.vehicle-rentals.financial-summary', ['locale' => $currentLocale]) }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label font-weight-bold">{{ __('poultry.fields.started_at') }} (من)</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label font-weight-bold">{{ __('poultry.fields.started_at') }} (إلى)</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-green w-100">
                        <i class="fas fa-filter mr-1"></i> تصفية
                    </button>
                    <a href="{{ route('customer.poultry.vehicle-rentals.financial-summary', ['locale' => $currentLocale]) }}" class="btn btn-outline-white">
                        إلغاء
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- جدول تفصيل الرحلات المحاسبية --}}
    <div class="table-container">
        <table class="table registry-table mb-0">
            <thead>
                <tr>
                    <th>{{ __('poultry.titles.vehicles') }}</th>
                    <th>{{ __('poultry.fields.started_at') }}</th>
                    <th>{{ __('poultry.fields.rental_type') }}</th>
                    <th>{{ __('poultry.fields.customer_name') }}</th>
                    <th>{{ __('poultry.fields.rental_fee') }}</th>
                    <th>{{ __('poultry.fields.fuel_cost') }}</th>
                    <th>{{ __('poultry.fields.driver_commission') }}</th>
                    <th>{{ __('poultry.options.other') }}</th>
                    <th>{{ __('poultry.fields.total_costs') }}</th>
                    <th>{{ __('poultry.fields.net_profit') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rentals as $rental)
                    <tr>
                        <td><strong>{{ $rental->vehicle?->plate_number ?? '-' }}</strong></td>
                        <td>{{ $rental->started_at?->format('Y-m-d') }}</td>
                        <td>
                            @if($rental->rental_type === 'external')
                                <span class="badge bg-info text-dark">{{ __('poultry.options.external') }}</span>
                            @else
                                <span class="badge bg-light text-dark border">{{ __('poultry.options.internal') }}</span>
                            @endif
                        </td>
                        <td>{{ $rental->customer_name ?? '-' }}</td>
                        <td class="text-success font-weight-bold">{{ number_format((float)$rental->rental_fee, 2) }}</td>
                        <td>{{ number_format((float)$rental->fuel_cost, 2) }}</td>
                        <td>{{ number_format((float)$rental->driver_commission, 2) }}</td>
                        <td>{{ number_format((float)$rental->other_expenses, 2) }}</td>
                        <td class="text-danger font-weight-bold">{{ number_format((float)$rental->total_trip_expenses, 2) }}</td>
                        <td>
                            <span class="badge {{ (float)$rental->net_trip_profit >= 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ number_format((float)$rental->net_trip_profit, 2) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10">{{ __('poultry.empty.no_rentals') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection