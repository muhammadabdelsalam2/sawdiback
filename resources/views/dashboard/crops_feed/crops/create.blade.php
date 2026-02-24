@extends('layouts.customer.dashboard')

@section('title', __('crops_feed.titles.create_crop'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <h2 class="page-title mb-3">{{ __('crops_feed.titles.create_crop') }}</h2>
        @if ($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        <div class="card-block">
            <form method="POST" action="{{ route('customer.crops-feed.crops.store', ['locale' => $currentLocale]) }}">
                @include('dashboard.crops_feed.crops._form')
                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-primary-green" type="submit">{{ __('crops_feed.actions.save') }}</button>
                    <a class="btn btn-outline-white" href="{{ route('customer.crops-feed.crops.index', ['locale' => $currentLocale]) }}">{{ __('crops_feed.actions.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
