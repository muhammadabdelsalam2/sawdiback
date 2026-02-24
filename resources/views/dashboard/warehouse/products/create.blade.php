@extends('layouts.customer.dashboard')

@section('title', __('warehouse.titles.add_product'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <h2 class="page-title mb-3">{{ __('warehouse.titles.add_product') }}</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="card-block">
            <form method="POST" action="{{ route('customer.inventory.products.store', ['locale' => $currentLocale]) }}">
                @csrf
                @include('dashboard.warehouse.products._form')
                <div class="mt-3">
                    <button class="btn btn-primary-green" type="submit">{{ __('warehouse.actions.save') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

