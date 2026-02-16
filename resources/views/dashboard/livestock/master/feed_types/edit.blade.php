@extends('layouts.customer.dashboard')

@section('title', __('livestock.titles.edit_feed_type'))
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <h2 class="page-title mb-3">{{ __('livestock.titles.edit_feed_type') }}</h2>
        @if ($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        <div class="card-block">
            <form method="POST" action="{{ route('superadmin.livestock.feed-types.update', ['locale' => $currentLocale, 'feed_type' => $feedType->id]) }}">
                @method('PUT')
                @include('dashboard.livestock.master.feed_types._form', ['feedType' => $feedType])
                <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-primary-green" type="submit">{{ __('livestock.actions.update') }}</button>
                    <a class="btn btn-outline-white" href="{{ route('superadmin.livestock.feed-types.index', ['locale' => $currentLocale]) }}">{{ __('livestock.actions.back') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
