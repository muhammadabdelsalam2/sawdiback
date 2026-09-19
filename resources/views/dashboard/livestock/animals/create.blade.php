@extends('layouts.customer.dashboard')

@section('title', __('livestock.titles.register_animal'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/livestock.css') }}">
    <style>
        .livestock-page .card-block {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            border: 1px solid #eef2f5;
            padding: 1.75rem;
        }
        .livestock-page .btn-primary-green {
            background-color: #15803d;
            border-color: #15803d;
            color: #ffffff;
            font-weight: 600;
            padding: 8px 24px;
            border-radius: 8px;
        }
        .livestock-page .btn-primary-green:hover {
            background-color: #166534;
            border-color: #166534;
            color: #ffffff;
        }
        .livestock-page .btn-cancel {
            background-color: #f1f5f9;
            border-color: #e2e8f0;
            color: #475569;
            font-weight: 600;
            padding: 8px 20px;
            border-radius: 8px;
            text-decoration: none;
        }
        .livestock-page .btn-cancel:hover {
            background-color: #e2e8f0;
            color: #1e293b;
        }
    </style>
@endpush

@section('content')
@php
    $currentLocale = request()->route('locale') ?? app()->getLocale();
@endphp

<div class="container py-4 livestock-page">
    <div class="page-head mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="page-title mb-1 font-weight-bold text-dark">{{ __('livestock.titles.register_animal') }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('customer.livestock.animals.index', ['locale' => $currentLocale]) }}">{{ __('livestock.titles.animals') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('livestock.titles.register_animal') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-block">
        <form method="POST" action="{{ route('customer.livestock.animals.store', ['locale' => $currentLocale]) }}">
            @csrf
            @include('dashboard.livestock.animals._form')

            <div class="mt-4 pt-3 border-top d-flex gap-2">
                <button class="btn btn-primary-green" type="submit">
                    <i class="fas fa-save mr-1"></i> {{ __('livestock.actions.save') }}
                </button>
                <a class="btn btn-cancel" href="{{ route('customer.livestock.animals.index', ['locale' => $currentLocale]) }}">
                    {{ __('livestock.actions.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection