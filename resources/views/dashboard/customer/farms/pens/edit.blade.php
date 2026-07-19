@extends('layouts.customer.dashboard')
@section('title', __('farms.titles.edit_pen'))
@push('styles')<link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">@endpush
@section('content')
<div class="container py-4 livestock-page"><div class="page-head"><h2 class="page-title">{{ __('farms.titles.edit_pen') }}</h2></div>@include('dashboard.customer.farms.partials.flash')<div class="card-block"><form method="POST" action="{{ route('customer.farm-pens.update', ['locale' => $currentLocale, 'farm_pen' => $pen->id]) }}">@method('PUT')@include('dashboard.customer.farms.pens._form')<div class="mt-3 d-flex gap-2"><button class="btn btn-primary-green">{{ __('farms.actions.save') }}</button><a class="btn btn-outline-white" href="{{ route('customer.farm-pens.show', ['locale' => $currentLocale, 'farm_pen' => $pen->id]) }}">{{ __('farms.actions.cancel') }}</a></div></form></div></div>
@endsection
