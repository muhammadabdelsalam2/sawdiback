@extends('layouts.customer.dashboard')

@section('title', __('dashboard.sidebar.dashboard') . ' - ' . config('app.name'))

@section('content')
@if (optional(auth()->user()->subscription)->is_valid)
        <div class="dashboard-body">
            <!-- Header Row -->
            <div class="row align-items-center mb-4 g-3">
                <div class="col-12 col-md">
                    <h1 class="dashboard-title">{{ __('dashboard.overview_title') }}</h1>
                    <p class="dashboard-desc">{{ __('dashboard.overview_desc') }}</p>
                </div>
                <div class="col-12 col-md-auto">
                    <div class="dropdown custom-dropdown">
                        <button class="btn dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                            <i class="fa-regular fa-calendar me-2"></i>
                            <span>{{ __('dashboard.last_30_days') }}</span>
                            <i class="fa-solid fa-chevron-down ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Cards Row -->
            <div class="row g-4 mb-4">
                <!-- Card 1: Total Livestock -->
                <div class="col-lg-3 col-md-6">
                    <div class="stats-card">
                        <div class="stats-card-header d-flex align-items-center">
                            <img src="{{ asset('assets/images/card-icon-1.svg') }}" alt="" class="stats-icon me-2">
                            <span class="stats-label">{{ __('dashboard.stats.total_livestock') }}</span>
                        </div>
                        <div class="stats-card-body">
                            <div class="d-flex align-items-baseline gap-2">
                                <h2 class="stats-value">{{ number_format($stats['total_livestock']) }}</h2>
                                <span class="stats-unit">{{ __('dashboard.stats.heads') }}</span>
                            </div>
                            <div class="stats-footer mt-2">
                                <span class="trend trend-up">
                                    <img src="{{ asset('assets/images/card-icon-5.svg') }}" alt="" class="trend-icon me-1">
                                    +{{ $stats['new_born_this_month'] }}
                                </span>
                                <span class="trend-label ms-1">{{ __('dashboard.stats.new_born') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Card 2: Daily Milk Yield -->
                <div class="col-lg-3 col-md-6">
                    <div class="stats-card">
                        <div class="stats-card-header d-flex align-items-center">
                            <img src="{{ asset('assets/images/card-icon-2.svg') }}" alt="" class="stats-icon me-2">
                            <span class="stats-label">{{ __('dashboard.stats.daily_milk_yield') }}</span>
                        </div>
                        <div class="stats-card-body">
                            <div class="d-flex align-items-baseline gap-2">
                                <h2 class="stats-value">{{ number_format($stats['daily_milk_yield']) }}</h2>
                                <span class="stats-unit">{{ __('dashboard.stats.liters') }}</span>
                            </div>
                            <div class="stats-footer mt-2">
                                <span class="trend {{ $stats['milk_yield_delta'] >= 0 ? 'trend-up' : 'trend-down-small' }}">
                                    <img src="{{ asset('assets/images/card-icon-' . ($stats['milk_yield_delta'] >= 0 ? '5' : '6') . '.svg') }}" alt="" class="trend-icon me-1">
                                    {{ $stats['milk_yield_delta'] >= 0 ? '+' : '' }}{{ $stats['milk_yield_delta'] }}%
                                </span>
                                <span class="trend-label ms-1">{{ __('dashboard.stats.vs_last_month') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Card 3: Feed Inventory -->
                <div class="col-lg-3 col-md-6">
                    <div class="stats-card">
                        <div class="stats-card-header d-flex align-items-center">
                            <img src="{{ asset('assets/images/card-icon-3.svg') }}" alt="" class="stats-icon me-2">
                            <span class="stats-label">{{ __('dashboard.stats.feed_inventory') }}</span>
                        </div>
                        <div class="stats-card-body">
                            <div class="d-flex align-items-baseline gap-2">
                                <h2 class="stats-value">{{ number_format($stats['feed_inventory']) }}</h2>
                                <span class="stats-unit">{{ __('dashboard.stats.tons') }}</span>
                            </div>
                            <div class="stats-footer mt-2">
                                <span class="trend {{ $stats['feed_delta'] >= 0 ? 'trend-up' : 'trend-down-large' }}">
                                    <img src="{{ asset('assets/images/card-icon-' . ($stats['feed_delta'] >= 0 ? '5' : '7') . '.svg') }}" alt="" class="trend-icon me-1">
                                    {{ $stats['feed_delta'] >= 0 ? '+' : '' }}{{ $stats['feed_delta'] }}%
                                </span>
                                <span class="trend-label ms-1">{{ __('dashboard.stats.low_stock_alert') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Card 4: Average Profit -->
                <div class="col-lg-3 col-md-6">
                    <div class="stats-card">
                        <div class="stats-card-header d-flex align-items-center">
                            <span class="stats-icon-text me-2">$</span>
                            <span class="stats-label">{{ __('dashboard.stats.average_profit_daily') }}</span>
                        </div>
                        <div class="stats-card-body">
                            <div class="d-flex align-items-baseline gap-2">
                                <h2 class="stats-value">{{ number_format($stats['average_profit']) }}</h2>
                                <span class="stats-unit">{{ __('dashboard.stats.aed') }}</span>
                            </div>
                            <div class="stats-footer mt-2">
                                <span class="trend trend-up">
                                    <img src="{{ asset('assets/images/card-icon-5.svg') }}" alt="" class="trend-icon me-1">
                                    +{{ $stats['profit_delta'] }}%
                                </span>
                                <span class="trend-label ms-1">{{ __('dashboard.stats.vs_last_month') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="chart-card">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h3 class="chart-title">{{ __('dashboard.charts.daily_production_performance') }}</h3>
                                <p class="chart-desc">{{ __('dashboard.charts.actual_vs_target') }}</p>
                            </div>
                            <div class="dropdown custom-dropdown border-0">
                                <button class="btn dropdown-toggle d-flex align-items-center text-muted p-0" type="button"
                                    data-bs-toggle="dropdown">
                                    <i class="fa-regular fa-calendar me-2"></i>
                                    <span class="small fw-bold">{{ __('dashboard.last_30_days') }}</span>
                                    <i class="fa-solid fa-chevron-down ms-2"></i>
                                </button>
                            </div>
                        </div>
                        <div class="chart-container">
                            <canvas id="productionChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-card">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h3 class="chart-title">{{ __('dashboard.charts.herd_composition_status') }}</h3>
                                <p class="chart-desc">{{ __('dashboard.charts.distribution_by_stage') }}</p>
                            </div>
                            <div class="dropdown custom-dropdown border-0">
                                <button class="btn dropdown-toggle d-flex align-items-center text-muted p-0" type="button"
                                    data-bs-toggle="dropdown">
                                    <i class="fa-regular fa-calendar me-2"></i>
                                    <span class="small fw-bold">{{ __('dashboard.last_30_days') }}</span>
                                    <i class="fa-solid fa-chevron-down ms-2"></i>
                                </button>
                            </div>
                        </div>
                        <div class="chart-container donut-container">
                            <canvas id="herdChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="chart-card">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h3 class="chart-title">{{ __('dashboard.charts.profitability_cost_center') }}</h3>
                                <p class="chart-desc">{{ __('dashboard.charts.net_profit_margin') }}</p>
                            </div>
                            <div class="dropdown custom-dropdown border-0">
                                <button class="btn dropdown-toggle d-flex align-items-center text-muted p-0" type="button"
                                    data-bs-toggle="dropdown">
                                    <i class="fa-regular fa-calendar me-2"></i>
                                    <span class="small fw-bold">{{ __('dashboard.last_30_days') }}</span>
                                    <i class="fa-solid fa-chevron-down ms-2"></i>
                                </button>
                            </div>
                        </div>
                        <div class="chart-container wide-chart">
                            <canvas id="profitabilityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerts Row -->
            <div class="row g-4 mb-4">
                <!-- Critical Alerts -->
                <div class="col-lg-4 col-md-6">
                    <div class="alert-card">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h3 class="chart-title mb-0">{{ __('dashboard.alerts.critical_alerts') }}</h3>
                                <p class="chart-desc mb-0">{{ __('dashboard.alerts.urgent_health_stock') }}</p>
                            </div>
                            <div class="return-info d-flex align-items-center">
                                <i class="fa-solid fa-rotate-left me-1"></i>
                                <span class="small">{{ __('dashboard.last_updated') }} {{ now()->format('H:i') }}</span>
                            </div>
                        </div>
                        <div class="alert-list">
                            @foreach($alerts['critical'] as $alert)
                            <div class="alert-item {{ $loop->last ? 'border-0' : '' }}">
                                <img src="{{ asset('assets/images/' . $alert['icon']) }}" alt="" class="alert-icon">
                                <div class="alert-content">
                                    <h4 class="alert-title">{{ $alert['title'] }}</h4>
                                    <p class="alert-desc">{{ $alert['desc'] }}</p>
                                </div>
                                <span class="badge badge-{{ $alert['type'] }}">
                                    <i class="fa-regular {{ $alert['type'] == 'urgent' ? 'fa-circle-xmark' : 'fa-triangle-exclamation' }} me-1"></i>
                                    {{ __('dashboard.alerts.' . $alert['type']) }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                        <button class="btn btn-view-all mt-3 d-flex align-items-center justify-content-center ms-auto">
                            <span class="me-2">{{ __('dashboard.buttons.view_all') }}</span>
                            <span class="badge-count">72</span>
                        </button>
                    </div>
                </div>

                <!-- Active Sales Orders -->
                <div class="col-lg-4 col-md-6">
                    <div class="alert-card">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h3 class="chart-title mb-0">{{ __('dashboard.alerts.active_sales_orders') }}</h3>
                                <p class="chart-desc mb-0">{{ __('dashboard.alerts.pending_approvals_processing') }}</p>
                            </div>
                            <div class="return-info d-flex align-items-center">
                                <i class="fa-solid fa-rotate-left me-1"></i>
                                <span class="small">{{ __('dashboard.last_updated') }} {{ now()->format('H:i') }}</span>
                            </div>
                        </div>
                        <div class="alert-list">
                            @foreach($alerts['sales'] as $alert)
                            <div class="alert-item {{ $loop->last ? 'border-0' : '' }}">
                                <img src="{{ asset('assets/images/' . $alert['icon']) }}" alt="" class="alert-icon">
                                <div class="alert-content">
                                    <h4 class="alert-title">{{ $alert['title'] }}</h4>
                                    <p class="alert-desc">{{ $alert['desc'] }}</p>
                                </div>
                                <span class="badge badge-warning">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                    {{ __('dashboard.alerts.warning') }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                        <button class="btn btn-view-all mt-3 d-flex align-items-center justify-content-center ms-auto">
                            <span class="me-2">{{ __('dashboard.buttons.view_all') }}</span>
                            <span class="badge-count">12</span>
                        </button>
                    </div>
                </div>

                <!-- Today's Operations -->
                <div class="col-lg-4 col-md-6">
                    <div class="alert-card">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h3 class="chart-title mb-0">{{ __('dashboard.alerts.todays_operations') }}</h3>
                                <p class="chart-desc mb-0">{{ __('dashboard.alerts.visits_maintenance_logistics') }}</p>
                            </div>
                            <div class="return-info d-flex align-items-center">
                                <i class="fa-solid fa-rotate-left me-1"></i>
                                <span class="small">{{ __('dashboard.last_updated') }} {{ now()->format('H:i') }}</span>
                            </div>
                        </div>
                        <div class="alert-list">
                            @foreach($alerts['operations'] as $alert)
                            <div class="alert-item {{ $loop->last ? 'border-0' : '' }}">
                                <img src="{{ asset('assets/images/' . $alert['icon']) }}" alt="" class="alert-icon">
                                <div class="alert-content">
                                    <h4 class="alert-title">{{ $alert['title'] }}</h4>
                                    <p class="alert-desc">{{ $alert['desc'] }}</p>
                                </div>
                                <span class="badge badge-info text-dark">
                                    <i class="fa-solid fa-info-circle me-1"></i>
                                    {{ __('dashboard.alerts.todays_operations') }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                        <button class="btn btn-view-all mt-3 d-flex align-items-center justify-content-center ms-auto">
                            <span class="me-2">{{ __('dashboard.buttons.view_all') }}</span>
                            <span class="badge-count">5</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty State / No Subscription UX -->
        <div class="dashboard-body d-flex flex-column justify-content-center align-items-center text-center py-5"
            style="min-height: 90vh;">
            <img src="{{ asset('assets/images/no-subscription.png') }}" alt="No Subscription" class="mb-4" style="width:200px;">
            <h1 class="h2 mb-2">{{ __('dashboard.no_plan_title') }}</h1>
            <p class="text-muted mb-4">{{ __('dashboard.no_plan_desc') }}</p>

            <a href="{{ route('customer.subscription.index', ['locale' => request()->route('locale')]) }}"
               class="btn btn-primary btn-lg d-flex align-items-center gap-2">
                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                {{ __('dashboard.go_to_subscription') }}
            </a>

            <div class="mt-5 w-75 text-start">
                <h4 class="mb-3">{{ __('dashboard.no_plan_features_title') }}</h4>
                <ul class="list-unstyled text-muted lh-lg">
                    <li><i class="fa-solid fa-circle-check text-success me-2"></i>{{ __('dashboard.no_plan_feature_1') }}</li>
                    <li><i class="fa-solid fa-circle-check text-success me-2"></i>{{ __('dashboard.no_plan_feature_2') }}</li>
                    <li><i class="fa-solid fa-circle-check text-success me-2"></i>{{ __('dashboard.no_plan_feature_3') }}</li>
                    <li><i class="fa-solid fa-circle-check text-success me-2"></i>{{ __('dashboard.no_plan_feature_4') }}</li>
                </ul>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    window.dashboardData = {
        production: {
            labels: {!! json_encode($productionData->pluck('date')) !!},
            data: {!! json_encode($productionData->pluck('total_yield')) !!}
        },
        herd: {
            labels: {!! json_encode($herdComposition->keys()) !!},
            data: {!! json_encode($herdComposition->values()) !!}
        }
    };
</script>
@endpush
