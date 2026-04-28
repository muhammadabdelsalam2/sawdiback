@extends('layouts.customer.dashboard')

@section('content')

    {{-- Start Make UI For Farmer --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-light rounded-top-4  py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-white">
                <i class="fas fa-credit-card me-2"></i>{{ __('Subscriptions List') }}
            </h5>
            {{-- Alert If Validation Return Message --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

         </div>
                <div class="container py-4 ">
                    <div class="page-head d-flex justify-content-between align-items-center mb-4">
                        <h2 class="page-title">{{ __('farmer.titles.farmer') }}</h2>
                        <a class="btn btn-primary-green"
                            data-url="{{ route('customer.farmers.create', ['locale' => $currentLocale]) }}"
                            data-ajax-popup="true" data-title="{{ __('Create Farmer') }}" data-size="lg"
                            href="{{ route('customer.crops-feed.crops.create', ['locale' => $currentLocale]) }}">{{ __('crops_feed.actions.add_crop') }}</a>
                    </div>
                    <div class="card">
                        <div class="card-body table-container">
                            <table class="table table-striped table registry-table js-livestock-table mb-0 ">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($farmers as $farmer)
                                        <tr>
                                            <td>{{ $farmer->name }}</td>
                                            <td>{{ $farmer->email }}</td>
                                            <td>{{ $farmer->phone }}</td>
                                            <td>{{ $farmer->address }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                        <div class="mt-3">{{ $farmers->links('pagination::bootstrap-5') }}</div>
                    </div>
                </div>









@endsection