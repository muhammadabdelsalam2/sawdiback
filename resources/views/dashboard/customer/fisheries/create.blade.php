@extends('layouts.customer.dashboard')

@section('title', __('fisheries.actions.add_batch') ?? __('إضافة حوض جديد'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <div class="page-head">
            <h2 class="page-title">{{ __('fisheries.actions.add_batch') ?? __('إضافة حوض جديد') }}</h2>
            <div class="quick-actions">
                <a class="btn btn-outline-white" href="{{ route('customer.fisheries.index', ['locale' => $currentLocale]) }}">
                    {{ __('poultry.actions.back') ?? __('رجوع') }}
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card-block">
            <form method="POST" action="{{ route('customer.fisheries.store', ['locale' => $currentLocale]) }}">
                @include('dashboard.customer.fisheries._form')

                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-primary-green" type="submit">
                        <i class="fas fa-save mr-1"></i> {{ __('poultry.actions.save') ?? __('حفظ') }}
                    </button>
                    <a class="btn btn-outline-white" href="{{ route('customer.fisheries.index', ['locale' => $currentLocale]) }}">
                        {{ __('poultry.actions.cancel') ?? __('إلغاء') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
