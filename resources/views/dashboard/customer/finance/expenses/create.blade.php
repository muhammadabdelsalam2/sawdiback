@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('finance.expenses.create_title') }}</h3>
        <a class="btn btn-outline-secondary" href="{{ route('customer.finance.expenses.index', ['locale' => request()->route('locale')]) }}">{{ __('finance.common.back') }}</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul></div>
    @endif

    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('customer.finance.expenses.store', ['locale' => request()->route('locale')]) }}">
            @csrf
            @include('dashboard.customer.finance.expenses._form')
            <button class="btn btn-primary">{{ __('finance.common.save') }}</button>
            <a class="btn btn-light" href="{{ route('customer.finance.expenses.index', ['locale' => request()->route('locale')]) }}">{{ __('finance.common.cancel') }}</a>
        </form>
    </div></div>
</div>
@endsection
