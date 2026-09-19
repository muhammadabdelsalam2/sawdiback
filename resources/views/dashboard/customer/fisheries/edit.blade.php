@extends('layouts.customer.dashboard')

@section('title', __('fisheries.actions.edit_batch') ?? __('تعديل بيانات الحوض'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
@endpush

@section('content')
    <div class="container py-4 livestock-page">
        <div class="page-head">
            <h2 class="page-title">{{ __('fisheries.actions.edit_batch') ?? __('تعديل بيانات الحوض') }}: {{ $batch->pond_name }}</h2>
            <div class="quick-actions">
                <a class="btn btn-outline-white" href="{{ route('customer.fisheries.show', ['locale' => $currentLocale, 'fish_batch' => $batch->id]) }}">
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
            <form method="POST" action="{{ route('customer.fisheries.update', ['locale' => $currentLocale, 'fish_batch' => $batch->id]) }}">
                @method('PUT')
                @include('dashboard.customer.fisheries._form')

                <div class="mt-4 d-flex gap-2">
                    <button class="btn btn-primary-green" type="submit">
                        <i class="fas fa-save mr-1"></i> {{ __('poultry.actions.save') ?? __('تحديث البيانات') }}
                    </button>
                    <a class="btn btn-outline-white" href="{{ route('customer.fisheries.show', ['locale' => $currentLocale, 'fish_batch' => $batch->id]) }}">
                        {{ __('poultry.actions.cancel') ?? __('إلغاء') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
