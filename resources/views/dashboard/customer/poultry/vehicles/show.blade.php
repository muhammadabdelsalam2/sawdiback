@extends('layouts.customer.dashboard')

@section('title', __('poultry.titles.vehicle_details'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
@php
    $currentLocale = request()->route('locale') ?? app()->getLocale();
@endphp
<div class="container py-4 livestock-page">
    <div class="page-head">
        <h2 class="page-title">{{ __('poultry.titles.vehicle_details') }}: {{ $vehicle->plate_number }}</h2>
        <div class="d-flex gap-2">
            <a class="btn btn-sm btn-primary-green" href="{{ route('customer.poultry.vehicle-rentals.create', ['locale' => $currentLocale, 'vehicle_id' => $vehicle->id]) }}">
                <i class="fas fa-plus mr-1"></i> {{ __('poultry.actions.add_rental') }}
            </a>
            <a class="btn btn-sm btn-outline-white" href="{{ route('customer.poultry.vehicle-rentals.financial-summary', ['locale' => $currentLocale, 'vehicle_id' => $vehicle->id]) }}">
                <i class="fas fa-chart-line mr-1"></i> {{ __('poultry.fields.net_profit') }}
            </a>
            <a class="btn btn-sm btn-outline-white" href="{{ route('customer.poultry.vehicles.edit', ['locale' => $currentLocale, 'vehicle' => $vehicle->id]) }}">
                <i class="fas fa-edit mr-1"></i> {{ __('poultry.actions.edit') }}
            </a>
            <a class="btn btn-sm btn-outline-white" href="{{ route('customer.poultry.vehicles.index', ['locale' => $currentLocale]) }}">
                {{ __('poultry.actions.back') }}
            </a>
        </div>
    </div>

    @include('dashboard.customer.poultry.partials.flash')

    {{-- البيانات الأساسية --}}
    <div class="card-block mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <strong>{{ __('poultry.fields.plate_number') }}:</strong>
                <span class="text-primary font-weight-bold">{{ $vehicle->plate_number }}</span>
            </div>
            <div class="col-md-3">
                <strong>{{ __('farms.fields.farm') }}:</strong>
                <span>{{ $vehicle->farm?->name ?? '-' }}</span>
            </div>
            <div class="col-md-3">
                <strong>{{ __('poultry.fields.ownership_type') }}:</strong>
                <span>{{ __('poultry.options.' . $vehicle->ownership_type) }}</span>
            </div>
            <div class="col-md-3">
                <strong>{{ __('poultry.fields.status') }}:</strong>
                <span>{{ __('poultry.options.' . $vehicle->status) }}</span>
            </div>
            <div class="col-md-3">
                <strong>{{ __('poultry.fields.driver_name') }}:</strong>
                <span>{{ $vehicle->driver_name ?? '-' }}</span>
            </div>
            <div class="col-md-3">
                <strong>{{ __('poultry.fields.driver_phone') }}:</strong>
                <span>{{ $vehicle->driver_phone ?? '-' }}</span>
            </div>
            <div class="col-md-3">
                <strong>{{ __('poultry.fields.capacity_birds') }}:</strong>
                <span>{{ number_format($vehicle->capacity_birds) }}</span>
            </div>
            <div class="col-md-3">
                <strong>{{ __('poultry.fields.capacity_crates') }}:</strong>
                <span>{{ number_format($vehicle->capacity_crates) }}</span>
            </div>
            @if($vehicle->notes)
                <div class="col-md-12">
                    <strong>{{ __('poultry.fields.notes') }}:</strong>
                    <span>{{ $vehicle->notes }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- عقد الاستئجار إن وجد --}}
    @if($vehicle->ownership_type === 'leased')
        <div class="card-block mb-4">
            <h5 class="border-bottom pb-2 mb-3 font-weight-bold">
                <i class="fas fa-file-contract mr-1"></i> {{ __('poultry.titles.vehicle_details') }} ({{ __('poultry.options.leased') }})
            </h5>
            <div class="row g-3">
                <div class="col-md-3">
                    <strong>{{ __('poultry.fields.lessor_name') }}:</strong>
                    <span>{{ $vehicle->lessor_name ?? '-' }}</span>
                </div>
                <div class="col-md-3">
                    <strong>{{ __('poultry.fields.lessor_phone') }}:</strong>
                    <span>{{ $vehicle->lessor_phone ?? '-' }}</span>
                </div>
                <div class="col-md-3">
                    <strong>{{ __('poultry.fields.lease_cost') }}:</strong>
                    <span class="text-danger font-weight-bold">{{ number_format((float)$vehicle->lease_cost, 2) }}</span>
                </div>
                <div class="col-md-3">
                    <strong>{{ __('poultry.fields.lease_period') }}:</strong>
                    <span>{{ $vehicle->lease_period ? __('poultry.options.' . $vehicle->lease_period) : '-' }}</span>
                </div>
                <div class="col-md-3">
                    <strong>{{ __('poultry.fields.lease_start_date') }}:</strong>
                    <span>{{ $vehicle->lease_start_date?->format('Y-m-d') ?? '-' }}</span>
                </div>
                <div class="col-md-3">
                    <strong>{{ __('poultry.fields.lease_end_date') }}:</strong>
                    <span>{{ $vehicle->lease_end_date?->format('Y-m-d') ?? '-' }}</span>
                </div>
            </div>
        </div>
    @endif

    {{-- جدول حركة الرحلات والتأجير --}}
    <div class="page-head mt-4">
        <h3 class="page-title">{{ __('poultry.titles.vehicle_rentals') }}</h3>
    </div>
    <div class="table-container">
        <table class="table registry-table mb-0">
            <thead>
                <tr>
                    <th>{{ __('poultry.fields.started_at') }}</th>
                    <th>{{ __('poultry.fields.rental_type') }}</th>
                    <th>{{ __('poultry.fields.customer_name') }}</th>
                    <th>{{ __('poultry.fields.origin') }} / {{ __('poultry.fields.destination') }}</th>
                    <th>{{ __('poultry.fields.rental_fee') }}</th>
                    <th>{{ __('poultry.fields.total_costs') }}</th>
                    <th>{{ __('poultry.fields.net_profit') }}</th>
                    <th>{{ __('poultry.fields.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicle->rentals as $rental)
                    <tr>
                        <td>{{ $rental->started_at?->format('Y-m-d H:i') }}</td>
                        <td>{{ __('poultry.options.' . $rental->rental_type) }}</td>
                        <td>{{ $rental->customer_name ?? '-' }}</td>
                        <td>{{ $rental->origin ?? '-' }} <i class="fas fa-arrow-left mx-1 text-muted"></i> {{ $rental->destination ?? '-' }}</td>
                        <td class="text-success font-weight-bold">{{ number_format((float)$rental->rental_fee, 2) }}</td>
                        <td class="text-danger">{{ number_format((float)$rental->total_trip_expenses, 2) }}</td>
                        <td>
                            <span class="badge {{ (float)$rental->net_trip_profit >= 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ number_format((float)$rental->net_trip_profit, 2) }}
                            </span>
                        </td>
                        <td>{{ __('poultry.options.' . $rental->payment_status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">{{ __('poultry.empty.no_rentals') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection