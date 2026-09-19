@extends('layouts.customer.dashboard')

@section('title', __('poultry.titles.vehicles'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
@php
    $currentLocale = request()->route('locale') ?? app()->getLocale();
@endphp
<div class="container py-4 livestock-page">
    <div class="page-head">
        <h2 class="page-title">{{ __('poultry.titles.vehicles') }}</h2>
        <a class="btn btn-primary-green" href="{{ route('customer.poultry.vehicles.create', ['locale' => $currentLocale]) }}">
            <i class="fas fa-plus mr-1"></i> {{ __('poultry.actions.add_vehicle') }}
        </a>
    </div>
    @include('dashboard.customer.poultry.partials.flash')
    <div class="table-container">
        <table class="table registry-table mb-0">
            <thead>
                <tr>
                    <th>{{ __('poultry.fields.plate_number') }}</th>
                    <th>{{ __('farms.fields.farm') }}</th>
                    <th>{{ __('poultry.fields.ownership_type') }}</th>
                    <th>{{ __('poultry.fields.driver_name') }}</th>
                    <th>{{ __('poultry.fields.capacity_birds') }}</th>
                    <th>{{ __('poultry.fields.status') }}</th>
                    <th>{{ __('poultry.fields.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vehicles as $vehicle)
                    <tr>
                        <td><strong>{{ $vehicle->plate_number }}</strong></td>
                        <td>{{ $vehicle->farm?->name ?? '-' }}</td>
                        <td>
                            @if($vehicle->ownership_type === 'leased')
                                <span class="badge bg-warning text-dark">{{ __('poultry.options.leased') }} ({{ number_format((float)$vehicle->lease_cost, 2) }})</span>
                            @else
                                <span class="badge bg-light text-dark border">{{ __('poultry.options.owned') }}</span>
                            @endif
                        </td>
                        <td>{{ $vehicle->driver_name ?? '-' }}</td>
                        <td>{{ number_format($vehicle->capacity_birds) }}</td>
                        <td>{{ __('poultry.options.' . $vehicle->status) }}</td>
                        <td class="d-flex gap-2">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('customer.poultry.vehicles.show', ['locale' => $currentLocale, 'vehicle' => $vehicle->id]) }}">
                                <i class="fas fa-eye"></i> {{ __('poultry.actions.view') }}
                            </a>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('customer.poultry.vehicles.edit', ['locale' => $currentLocale, 'vehicle' => $vehicle->id]) }}">
                                <i class="fas fa-edit"></i> {{ __('poultry.actions.edit') }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">{{ __('poultry.empty.no_vehicles') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $vehicles->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection