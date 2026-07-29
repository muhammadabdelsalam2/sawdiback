@extends('layouts.customer.dashboard')

@section('title', $farm->name . ' | ' . __('superadmin.farm_dashboard.title'))

@section('content')
    @php
        $activeLocale = $currentLocale ?? session('locale_full', 'en-SA');
        $stats = $dashboard['stats'];
        $card = $dashboard['card'];
        $milkLabels = collect($dashboard['charts']['milk'])->pluck('label')->values();
        $milkValues = collect($dashboard['charts']['milk'])->pluck('value')->map(fn ($value) => (float) $value)->values();
        $orderLabels = collect($dashboard['charts']['orders'])->pluck('label')->values();
        $orderValues = collect($dashboard['charts']['orders'])->pluck('value')->map(fn ($value) => (int) $value)->values();
        $animalLabels = collect($dashboard['charts']['animals'])->pluck('label')->values();
        $animalValues = collect($dashboard['charts']['animals'])->pluck('value')->map(fn ($value) => (int) $value)->values();
        $financeLabels = collect($dashboard['charts']['finance'])->pluck('label')->values();
        $financeValues = collect($dashboard['charts']['finance'])->pluck('value')->map(fn ($value) => (float) $value)->values();
    @endphp

    <div class="dashboard-body superadmin-dashboard">
        <section class="dashboard-hero mb-4">
            <div class="hero-copy">
                <span class="eyebrow">{{ __('superadmin.farm_dashboard.title') }}</span>
                <h1 class="dashboard-title mb-3">{{ $farm->name }}</h1>
                <p class="dashboard-desc mb-4">{{ $farm->location ?: __('superadmin.farms.no_location') }}</p>
                <div class="dashboard-hero-actions">
                    <a href="{{ route('superadmin.dashboard', ['locale' => $activeLocale]) }}" class="btn btn-outline-white">
                        <i class="bi bi-arrow-left"></i>
                        {{ __('superadmin.farm_dashboard.back_to_farms') }}
                    </a>
                </div>
            </div>
            <div class="hero-panel glass-panel">
                <div class="hero-panel-title">
                    <div>
                        <span class="eyebrow">{{ __('superadmin.farm_dashboard.farm_info') }}</span>
                        <h2>{{ __("superadmin.status.{$card['status']}") }}</h2>
                    </div>
                    <span class="badge badge-soft-success">{{ __('farms.options.' . $farm->type) }}</span>
                </div>
                <div class="hero-panel-stats">
                    <div class="hero-stat-card">
                        <small>{{ __('superadmin.farms.animals') }}</small>
                        <strong>{{ number_format($stats['animals']) }}</strong>
                    </div>
                    <div class="hero-stat-card">
                        <small>{{ __('superadmin.farms.pens') }}</small>
                        <strong>{{ number_format($stats['pens']) }}</strong>
                    </div>
                    <div class="hero-stat-card">
                        <small>{{ __('superadmin.farms.orders') }}</small>
                        <strong>{{ number_format($stats['orders']) }}</strong>
                    </div>
                    <div class="hero-stat-card">
                        <small>{{ __('superadmin.farm_dashboard.net') }}</small>
                        <strong>{{ number_format($stats['revenue'] - $stats['expenses'], 2) }} SAR</strong>
                    </div>
                </div>
            </div>
        </section>

        <section class="global-stats-section mb-4">
            <div class="section-header">
                <div>
                    <h2>{{ __('superadmin.farm_dashboard.all_data') }}</h2>
                    <p>{{ __('superadmin.farm_dashboard.all_data_desc') }}</p>
                </div>
            </div>

            <div class="row g-3 stats-grid">
                @foreach ([
                    ['icon' => 'bi-file-earmark-bar-graph', 'label' => __('superadmin.farm_dashboard.reports'), 'value' => $stats['reports']],
                    ['icon' => 'bi-people', 'label' => __('superadmin.farms.workers'), 'value' => $stats['workers']],
                    ['icon' => 'bi-clock-history', 'label' => __('superadmin.farm_dashboard.attendance'), 'value' => $stats['attendance_today']],
                    ['icon' => 'bi-heart-pulse', 'label' => __('superadmin.farms.animals'), 'value' => $stats['animals']],
                    ['icon' => 'bi-grid-3x3-gap', 'label' => __('superadmin.farms.pens'), 'value' => $stats['pens']],
                    ['icon' => 'bi-droplet', 'label' => __('superadmin.farm_dashboard.dairy_products'), 'value' => $stats['dairy_products']],
                    ['icon' => 'bi-box-seam', 'label' => __('superadmin.farms.products'), 'value' => $stats['products']],
                    ['icon' => 'bi-boxes', 'label' => __('superadmin.farm_dashboard.inventory'), 'value' => number_format($stats['inventory'], 2)],
                    ['icon' => 'bi-cart-check', 'label' => __('superadmin.farms.orders'), 'value' => $stats['orders']],
                    ['icon' => 'bi-person-vcard', 'label' => __('superadmin.farm_dashboard.customers'), 'value' => $stats['customers']],
                    ['icon' => 'bi-cash-stack', 'label' => __('superadmin.farm_dashboard.revenue'), 'value' => number_format($stats['revenue'], 2) . ' SAR'],
                    ['icon' => 'bi-receipt', 'label' => __('superadmin.farm_dashboard.expenses'), 'value' => number_format($stats['expenses'], 2) . ' SAR'],
                    ['icon' => 'bi-droplet-half', 'label' => __('superadmin.farms.milk'), 'value' => number_format($stats['milk_total'], 2)],
                    ['icon' => 'bi-bell', 'label' => __('superadmin.farm_dashboard.alerts'), 'value' => $stats['alerts']],
                ] as $metric)
                    <div class="col-md-6 col-xl-3">
                        <div class="enterprise-card">
                            <span class="enterprise-icon bg-soft-success"><i class="bi {{ $metric['icon'] }}"></i></span>
                            <p>{{ $metric['label'] }}</p>
                            <strong>{{ $metric['value'] }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="analytics-overview mb-4">
            <div class="row g-3">
                <div class="col-xl-6">
                    <div class="chart-card">
                        <div class="chart-card-header">
                            <div>
                                <h3>{{ __('superadmin.farm_dashboard.milk_chart') }}</h3>
                                <p>{{ __('superadmin.farm_dashboard.milk_chart_desc') }}</p>
                            </div>
                        </div>
                        <div class="chart-card-body"><canvas id="farmMilkChart"></canvas></div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="chart-card">
                        <div class="chart-card-header">
                            <div>
                                <h3>{{ __('superadmin.farm_dashboard.orders_chart') }}</h3>
                                <p>{{ __('superadmin.farm_dashboard.orders_chart_desc') }}</p>
                            </div>
                        </div>
                        <div class="chart-card-body"><canvas id="farmOrdersChart"></canvas></div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="chart-card chart-card-small">
                        <div class="chart-card-header">
                            <div>
                                <h3>{{ __('superadmin.farm_dashboard.animal_status') }}</h3>
                                <p>{{ __('superadmin.farm_dashboard.animal_status_desc') }}</p>
                            </div>
                        </div>
                        <div class="chart-card-body compact"><canvas id="farmAnimalsChart"></canvas></div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="chart-card chart-card-small">
                        <div class="chart-card-header">
                            <div>
                                <h3>{{ __('superadmin.farm_dashboard.finance_chart') }}</h3>
                                <p>{{ __('superadmin.farm_dashboard.finance_chart_desc') }}</p>
                            </div>
                        </div>
                        <div class="chart-card-body compact"><canvas id="farmFinanceChart"></canvas></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="customer-insights-section mb-4">
            <div class="customer-chart-grid">
                @include('dashboard.partials.superadmin-farm-list', ['title' => __('superadmin.farm_dashboard.recent_orders'), 'rows' => $dashboard['orders'], 'empty' => __('superadmin.messages.no_data'), 'columns' => ['order_no', 'status', 'total']])
                @include('dashboard.partials.superadmin-farm-list', ['title' => __('superadmin.farm_dashboard.products'), 'rows' => $dashboard['products'], 'empty' => __('superadmin.messages.no_data'), 'columns' => ['name', 'category', 'unit']])
                @include('dashboard.partials.superadmin-farm-list', ['title' => __('superadmin.farm_dashboard.animals'), 'rows' => $dashboard['animals'], 'empty' => __('superadmin.messages.no_data'), 'columns' => ['tag_number', 'status', 'health_status']])
                @include('dashboard.partials.superadmin-farm-list', ['title' => __('superadmin.farm_dashboard.pens'), 'rows' => $dashboard['pens'], 'empty' => __('superadmin.messages.no_data'), 'columns' => ['pen_number', 'type', 'capacity']])
                @include('dashboard.partials.superadmin-farm-list', ['title' => __('superadmin.farm_dashboard.workers'), 'rows' => $dashboard['workers'], 'empty' => $dashboard['notes']['workers'] ?? __('superadmin.messages.no_data'), 'columns' => ['full_name', 'phone', 'is_active']])
                @include('dashboard.partials.superadmin-farm-list', ['title' => __('superadmin.farm_dashboard.attendance'), 'rows' => $dashboard['attendance'], 'empty' => $dashboard['notes']['attendance'] ?? __('superadmin.messages.no_data'), 'columns' => ['full_name', 'day', 'check_in_at']])
                @include('dashboard.partials.superadmin-farm-list', ['title' => __('superadmin.farm_dashboard.customers'), 'rows' => $dashboard['customers'], 'empty' => __('superadmin.messages.no_data'), 'columns' => ['name', 'email']])
                @include('dashboard.partials.superadmin-farm-list', ['title' => __('superadmin.farm_dashboard.reports'), 'rows' => $dashboard['reports'], 'empty' => __('superadmin.messages.no_data'), 'columns' => ['title', 'desc']])
            </div>
        </section>

        <section class="analytics-overview mb-4">
            <div class="row g-3">
                <div class="col-xl-6">
                    <div class="chart-card chart-card-small">
                        <div class="chart-card-header"><h3>{{ __('superadmin.farm_dashboard.latest_activities') }}</h3></div>
                        <div class="activity-list">
                            @forelse ($dashboard['activities'] as $activity)
                                <div class="activity-row">
                                    <span class="enterprise-icon bg-soft-info"><i class="bi bi-activity"></i></span>
                                    <div>
                                        <strong>{{ $activity['title'] }}</strong>
                                        <p>{{ $activity['desc'] }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">{{ __('superadmin.messages.no_data') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="chart-card chart-card-small">
                        <div class="chart-card-header"><h3>{{ __('superadmin.farm_dashboard.alerts') }}</h3></div>
                        <div class="activity-list">
                            @foreach ($dashboard['alerts'] as $alert)
                                <div class="activity-row">
                                    <span class="enterprise-icon bg-soft-{{ $alert['type'] === 'danger' ? 'danger' : ($alert['type'] === 'warning' ? 'warning' : 'success') }}"><i class="bi bi-bell"></i></span>
                                    <div>
                                        <strong>{{ $alert['title'] }}</strong>
                                        <p>{{ $alert['desc'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script type="application/json" id="farmDashboardData">
        {!! json_encode([
            'milkLabels' => $milkLabels,
            'milkValues' => $milkValues,
            'orderLabels' => $orderLabels,
            'orderValues' => $orderValues,
            'animalLabels' => $animalLabels,
            'animalValues' => $animalValues,
            'financeLabels' => $financeLabels,
            'financeValues' => $financeValues,
            'text' => [
                'noDataYet' => __('superadmin.farm_dashboard.no_chart_data'),
                'noDistributionYet' => __('superadmin.farm_dashboard.no_distribution_data'),
            ],
        ], JSON_UNESCAPED_UNICODE) !!}
    </script>

    <script src="{{ asset('assets/js/pages/farm-dashboard.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const data = JSON.parse(document.getElementById('farmDashboardData')?.textContent || '{}');
            window.FarmDashboardCharts?.init(data, data.text || {});
        });
    </script>

    <style>
        .activity-list { display: grid; gap: 12px; padding: 18px; }
        .activity-row { display: flex; gap: 12px; align-items: flex-start; padding: 12px; border: 1px solid rgba(15, 23, 42, .08); border-radius: 8px; }
        .activity-row p { margin: 2px 0 0; color: #64748b; font-size: 13px; }
    </style>
@endsection
