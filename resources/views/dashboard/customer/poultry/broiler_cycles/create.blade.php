@extends('layouts.customer.dashboard')

@section('title', __('poultry.titles.add_broiler_cycle'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <div class="page-head"><h2 class="page-title">{{ __('poultry.titles.add_broiler_cycle') }}</h2></div>
        @include('dashboard.customer.poultry.partials.flash')
        <div class="card-block">
            <form method="POST" action="{{ route('customer.poultry.broiler-cycles.store', ['locale' => $currentLocale]) }}">
                @include('dashboard.customer.poultry.broiler_cycles._form')
                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-primary-green">{{ __('poultry.actions.save') }}</button>
                    <a class="btn btn-outline-white" href="{{ route('customer.poultry.broiler-cycles.index', ['locale' => $currentLocale]) }}">{{ __('poultry.actions.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
