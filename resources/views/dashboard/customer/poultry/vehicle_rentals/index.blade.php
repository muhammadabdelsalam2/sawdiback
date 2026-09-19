@extends('layouts.customer.dashboard')

@section('title', __('poultry.titles.vehicle_rentals'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
@php
    $currentLocale = request()->route('locale') ?? app()->getLocale();
@endphp
<div class="container py-4 livestock-page">
    <div class="page-head">
        <h2 class="page-title">{{ __('poultry.titles.vehicle_rentals') }}</h2>
        <div class="d-flex gap-2">
            <a class="btn btn-primary-green" href="{{ route('customer.poultry.vehicle-rentals.create', ['locale' => $currentLocale]) }}">
                <i class="fas fa-plus mr-1"></i> {{ __('poultry.actions.add_rental') }}
            </a>
            <a class="btn btn-outline-white" href="{{ route('customer.poultry.vehicle-rentals.financial-summary', ['locale' => $currentLocale]) }}">
                <i class="fas fa-chart-line mr-1"></i> {{ __('poultry.fields.net_profit') }}
            </a>
        </div>
    </div>
    @include('dashboard.customer.poultry.partials.flash')
    <div class="table-container">
        <table class="table registry-table mb-0">
            <thead>
                <tr>
                    <th>{{ __('poultry.titles.vehicles') }}</th>
                    <th>{{ __('poultry.fields.started_at') }}</th>
                    <th>{{ __('poultry.fields.rental_type') }}</th>
                    <th>{{ __('poultry.fields.customer_name') }}</th>
                    <th>{{ __('poultry.fields.rental_fee') }}</th>
                    <th>{{ __('poultry.fields.total_costs') }}</th>
                    <th>{{ __('poultry.fields.net_profit') }}</th>
                    <th>{{ __('poultry.fields.status') }}</th>
                    <th>{{ __('poultry.fields.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rentals as $rental)
                    <tr>
                        <td><strong>{{ $rental->vehicle?->plate_number ?? '-' }}</strong></td>
                        <td>{{ $rental->started_at?->format('Y-m-d H:i') }}</td>
                        <td>
                            @if($rental->rental_type === 'external')
                                <span class="badge bg-info text-dark">{{ __('poultry.options.external') }}</span>
                            @else
                                <span class="badge bg-light text-dark border">{{ __('poultry.options.internal') }}</span>
                            @endif
                        </td>
                        <td>{{ $rental->customer_name ?? '-' }}</td>
                        <td class="text-success font-weight-bold">{{ number_format((float)$rental->rental_fee, 2) }}</td>
                        <td class="text-danger">{{ number_format((float)$rental->total_trip_expenses, 2) }}</td>
                        <td>
                            <span class="badge {{ (float)$rental->net_trip_profit >= 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ number_format((float)$rental->net_trip_profit, 2) }}
                            </span>
                        </td>
                        <td>{{ __('poultry.options.' . $rental->payment_status) }}</td>
                        <td class="d-flex gap-2">
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('customer.poultry.vehicle-rentals.edit', ['locale' => $currentLocale, 'rental' => $rental->id]) }}">
                                <i class="fas fa-edit"></i> {{ __('poultry.actions.edit') }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">{{ __('poultry.empty.no_rentals') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $rentals->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection