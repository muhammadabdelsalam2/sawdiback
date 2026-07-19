@extends('layouts.customer.dashboard')
@section('title', __('poultry.titles.hatchery_batch_details'))
@push('styles')<link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">@endpush
@section('content')
<div class="container py-4 livestock-page"><div class="page-head"><h2 class="page-title">{{ __('poultry.titles.hatchery_batch_details') }}: {{ $batch->batch_number }}</h2><a class="btn btn-outline-white" href="{{ route('customer.poultry.hatchery-batches.index', ['locale' => $currentLocale]) }}">{{ __('poultry.actions.back') }}</a></div>@include('dashboard.customer.poultry.partials.flash')<div class="card-block"><div class="row g-3"><div class="col-md-3"><strong>{{ __('poultry.fields.machine') }}:</strong> {{ $batch->machine?->machine_number }}</div><div class="col-md-3"><strong>{{ __('poultry.fields.eggs_loaded') }}:</strong> {{ $batch->eggs_loaded }}</div><div class="col-md-3"><strong>{{ __('poultry.fields.chicks_produced') }}:</strong> {{ $batch->chicks_produced }}</div><div class="col-md-3"><strong>{{ __('poultry.fields.success_rate') }}:</strong> {{ $batch->success_rate }}%</div></div></div></div>
@endsection
