@extends('layouts.customer.dashboard')

@section('title', __('poultry.actions.edit'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
@php
    $currentLocale = request()->route('locale') ?? app()->getLocale();
@endphp
<div class="container py-4 livestock-page">
    <div class="page-head">
        <h2 class="page-title">{{ __('poultry.actions.edit') }}: {{ $rental->vehicle?->plate_number }} ({{ $rental->started_at?->format('Y-m-d') }})</h2>
    </div>
    @include('dashboard.customer.poultry.partials.flash')
    <div class="card-block">
        <form method="POST" action="{{ route('customer.poultry.vehicle-rentals.update', ['locale' => $currentLocale, 'rental' => $rental->id]) }}">
            @method('PUT')
            @include('dashboard.customer.poultry.vehicle_rentals._form')
            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-primary-green" type="submit">
                    <i class="fas fa-save mr-1"></i> {{ __('poultry.actions.save') }}
                </button>
                <a class="btn btn-outline-white" href="{{ route('customer.poultry.vehicle-rentals.index', ['locale' => $currentLocale]) }}">
                    {{ __('poultry.actions.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection