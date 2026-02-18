@extends('layouts.customer.dashboard')

@section('title', __('livestock.titles.create_vaccine'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <h2 class="page-title mb-3">{{ __('livestock.titles.create_vaccine') }}</h2>
        @if ($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        <div class="card-block">
            <form method="POST" action="{{ route('customer.livestock.vaccines.store', ['locale' => $currentLocale]) }}">
                @include('dashboard.livestock.master.vaccines._form')
                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-primary-green" type="submit">{{ __('livestock.actions.save') }}</button>
                    <a class="btn btn-outline-white" href="{{ route('customer.livestock.vaccines.index', ['locale' => $currentLocale]) }}">{{ __('livestock.actions.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
