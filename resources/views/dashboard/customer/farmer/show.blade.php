@extends('layouts.customer.dashboard')

@section('title')
    Farmer Dashboard
@endsection

@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="dashboard-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2>{{ $farmer->name }}</h2>
                    <p class="mb-0">Livestock & Farm Management Dashboard</p>
                </div>
                <div class="col-md-4 text-end">
                    <i class="fas fa-tractor fa-4x opacity-50"></i>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="row g-4 mb-4">

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <h3>{{ $products->count() }}</h3>
                    <p>Total Products</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-cow"></i>
                    </div>
                    {{-- <h3>{{ $livestock->count() }}</h3> --}}
                    <p>Total Livestock</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-egg"></i>
                    </div>
                    {{-- <h3>{{ $animalStats['production'] }}</h3> --}}
                    <p>Daily Production</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    {{-- <h3>{{ $animalStats['health_rate'] }}%</h3> --}}
                    <p>Health Rate</p>
                </div>
            </div>

        </div>


        {{--  AI Features --}}

        {{-- AI Insights --}}
        {{-- <div class="section-card mb-4">
            <h4 class="mb-3">🤖 AI Insights</h4>

            <div class="row">
                <div class="col-md-4">
                    <div class="ai-card">
                        <h6>Prediction</h6>
                        <p>Milk production will increase by 12%</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="ai-card">
                        <h6>Health Alert</h6>
                        <p class="text-danger">2 cows need checkup</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="ai-card">
                        <h6>Recommendation</h6>
                        <p>Increase feed quality</p>
                    </div>
                </div>
            </div>
        </div> --}}

        
        <div class="row">

            {{-- Products --}}
            <div class="col-lg-8">
                <div class="section-card">

                    <h2 class="page-title mb-4">{{ __('warehouse.titles.products') }}</h2>
                    <div class="row mb-4 align-items-center">
                        <div class="col-6 d-flex  align-items-center">
                            <h4 class="mb-4">{{ __('farmer.products.title') }}</h4>
                        </div>
                        <div class="col-6 text-end">
                            <div class="page-head mb-3">
                                <a class="btn btn-primary-green"
                                    href="{{ route('customer.inventory.products.create', ['locale' => $currentLocale]) }}">
                                    {{ __('warehouse.actions.add_product') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <table class="table table-hover overflow-scroll text-center align-middle ">
                        <thead class="table-light text-center align-middle text-color fs-6 fw-bold text-uppercase ">
                            <tr>
                                <th>{{ __('product.code') }}</th>
                                <th>{{ __('product.image') }}</th>
                                <th>{{ __('product.name') }}</th>
                                <th>{{ __('product.quantity') }}</th>
                                <th>{{ __('product.price') }}</th>
                                <th>{{ __('product.unit') }}</th>
                                <th>{{ __('product.is_active') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr class="hover-row text-color text-center">
                                    <td>{{ $product->code }}</td>
                                    <td>
                                        @if ($product->image)
                                            <div class="w-25 h-50 bg-secondary-subtle border border-secondary text-secondary rounded-5 d-flex align-items-center justify-content-center"
                                                style="width: 50px; height: 50px;">
                                                <img src="{{ asset('storage/' . $product->image) }}"
                                                    alt="{{ $product->name }}" class="img-thumbnail w-100 h-100"
                                                    style="max-width: 50px; max-height: 50px;">
                                            </div>
                                        @else
                                            {{-- Default Product Image --}}
                                            <div class="w-25 h-50 bg-secondary-subtle border border-secondary text-secondary rounded-5 d-flex align-items-center justify-content-center"
                                                style="width: 50px; height: 50px;">
                                                <img class="w-100 h-100 rounded-5"
                                                    src="{{ asset('assets/images/products/default-product.png') }}"
                                                    alt="Default Product Image">
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->quantity }}</td>
                                    <td>${{ $product->price }}</td>
                                    <td>{{ $product->unit }}</td>
                                    <td>
                                        <span class="badge badge-{{ $product->active ? 'success' : 'secondary' }}">
                                            @if ($product->is_active)
                                                <i class="fa-solid fa-circle-check me-1 text-success"></i> <span
                                                    class="text-success">Active</span>
                                            @else
                                                <i class="fa-solid fa-circle-xmark me-1 text-danger"></i> <span
                                                    class="text-danger">Inactive</span>
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>

            {{-- Livestock --}}
            <div class="col-lg-4">
                <div class="section-card">
                    <h4 class="mb-4">Livestock Details</h4>

                    @foreach ($livestock as $animal)
                        <div class="animal-card mb-3">
                            <h6>{{ $animal->type }}</h6>
                            <p class="mb-1">Count: {{ $animal->count }}</p>
                            <p class="mb-1">Health: {{ $animal->health }}%</p>
                            <small class="text-muted">
                                Last Check: {{ $animal->updated_at->diffForHumans() }}
                            </small>
                        </div>
                    @endforeach

                </div>
            </div>

        </div>

        {{-- Analysis --}}
        <div class="section-card">
            <h4 class="mb-4">Farm Analysis</h4>

            <div class="row">

                <div class="col-md-4">
                    <h6>Top Product</h6>
                    {{-- <p>{{ $analysis['top_product'] }}</p> --}}
                </div>

                <div class="col-md-4">
                    <h6>Best Performing Animal</h6>
                    {{-- <p>{{ $analysis['best_livestock'] }}</p> --}}
                </div>

                <div class="col-md-4">
                    <h6>Revenue Trend</h6>
                    <p class="text-success">
                        {{-- +{{ $analysis['growth'] }}% --}}
                    </p>
                </div>

            </div>
        </div>
    </div>
@endsection
