@extends('layouts.customer.dashboard')
@section('title', __('poultry.titles.edit_layer_flock'))
@push('styles')<link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">@endpush
@section('content')
<div class="container py-4 livestock-page"><div class="page-head"><h2 class="page-title">{{ __('poultry.titles.edit_layer_flock') }}</h2></div>@include('dashboard.customer.poultry.partials.flash')<div class="card-block"><form method="POST" action="{{ route('customer.poultry.layer-flocks.update', ['locale' => $currentLocale, 'layer_flock' => $flock->id]) }}">@method('PUT')@include('dashboard.customer.poultry.layer_flocks._form')<div class="mt-3 d-flex gap-2"><button class="btn btn-primary-green">{{ __('poultry.actions.save') }}</button><a class="btn btn-outline-white" href="{{ route('customer.poultry.layer-flocks.show', ['locale' => $currentLocale, 'layer_flock' => $flock->id]) }}">{{ __('poultry.actions.cancel') }}</a></div></form></div></div>
@endsection
