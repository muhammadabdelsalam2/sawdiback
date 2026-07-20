@extends('layouts.customer.dashboard')

@section('title', __('dashboard.superadmin.title') . ' | EL-Sawady')

@section('content')
    @php
        $activeLocale = $currentLocale ?? session('locale_full', 'en-SA');

        // Summary shortcuts — avoids array access inside @json()
        $summaryCustomers   = $summary['customers'] ?? 0;
        $summaryOrders    = $summary['orders'] ?? 0;
        $summaryProducts  = $summary['products'] ?? 0;
        $summaryRevenue   = $summary['revenue'] ?? 0;
        $summaryAnalytics = $summary['analytics'] ?? 0;
        $summaryFarms     = $summary['farms'] ?? 0;

        $activityGrowth = $summaryOrders > 0
            ? round((($summaryOrders - ($summaryOrders * 0.84)) / ($summaryOrders ?: 1)) * 100, 1)
            : 0;

        $labelRevenue = __('superadmin.charts.customer_revenue_trend');

        $revenueLabels = collect($revenueTrend ?? [])
            ->pluck('date')
            ->map(fn ($d) => \Illuminate\Support\Carbon::parse($d)->format('m/d'))
            ->values()
            ->all();

        $revenueValues = collect($revenueTrend ?? [])
            ->pluck('total')
            ->map(fn ($v) => (float) $v)
            ->values()
            ->all();

        $ordersStatusLabels = collect($ordersByStatus ?? [])->keys()->values()->all();
        $ordersStatusValues = collect($ordersByStatus ?? [])->values()->map(fn ($v) => (int) $v)->values()->all();

        $growthLabels = collect($customerGrowth ?? [])->pluck('label')->values()->all();
        $growthCustomers = collect($customerGrowth ?? [])->pluck('customers')->map(fn ($v) => (int) $v)->values()->all();
        $growthOrders = collect($customerGrowth ?? [])->pluck('orders')->map(fn ($v) => (int) $v)->values()->all();
        $growthRevenue = collect($customerGrowth ?? [])->pluck('revenue')->map(fn ($v) => (float) $v)->values()->all();
        $growthActivity = collect($customerGrowth ?? [])->pluck('activity')->map(fn ($v) => (int) $v)->values()->all();
    @endphp

    <div class="dashboard-body superadmin-dashboard">
        <section class="dashboard-hero mb-4">
            <div class="hero-copy">
                <span class="eyebrow">{{ __('dashboard.superadmin.title') }}</span>
                <h1 class="dashboard-title mb-3">{{ __('dashboard.superadmin.title') }}</h1>
                <p class="dashboard-desc mb-4">{{ __('dashboard.superadmin.desc') }}</p>

                <div class="dashboard-hero-actions">
                    <a href="{{ route('superadmin.access-management', ['locale' => $activeLocale]) }}" class="btn btn-primary-green">
                        <i class="bi bi-shield-lock"></i>
                        {{ __('dashboard.superadmin.manage_roles') }}
                    </a>
                    <a href="{{ route('superadmin.users.index', ['locale' => $activeLocale]) }}" class="btn btn-outline-white">
                        <i class="bi bi-people"></i>
                        {{ __('dashboard.superadmin.manage_users') }}
                    </a>
                </div>
            </div>

            <div class="hero-panel glass-panel">
                <div class="hero-panel-title">
                    <div>
                        <span class="eyebrow">{{ __('superadmin.hero.live_overview') }}</span>
                        <h2>{{ __('superadmin.hero.platform_pulse') }}</h2>
                    </div>
                    <span class="badge badge-soft-success">+{{ $summaryCustomers }} {{ __('superadmin.statistics.total_customers') }}</span>
                </div>
                <div class="hero-panel-stats">
                    <div class="hero-stat-card">
                        <small>{{ __('superadmin.statistics.total_customers') }}</small>
                        <strong>{{ number_format($summaryCustomers) }}</strong>
                    </div>
                    <div class="hero-stat-card">
                        <small>{{ __('superadmin.statistics.total_revenue') }}</small>
                        <strong>{{ number_format($summaryRevenue, 2) }} SAR</strong>
                    </div>
                    <div class="hero-stat-card">
                        <small>{{ __('superadmin.statistics.customer_orders') }}</small>
                        <strong>{{ number_format($summaryOrders) }}</strong>
                    </div>
                    <div class="hero-stat-card">
                        <small>{{ __('superadmin.statistics.total_farms') }}</small>
                        <strong>{{ number_format($summaryFarms) }}</strong>
                    </div>
                </div>
                <div class="hero-trend-chart-wrapper">
                    <canvas id="heroTrendChart"></canvas>
                </div>
            </div>
        </section>

        <section class="global-stats-section mb-4">
            <div class="section-header">
                <div>
                    <h2>{{ __('superadmin.statistics.global_statistics') }}</h2>
                    <p>{{ __('superadmin.statistics.executive_metrics') }}</p>
                </div>
            </div>

            <div class="row g-3 stats-grid">
                <div class="col-lg-4 col-xl-2">
                    <div class="enterprise-card">
                        <span class="enterprise-icon bg-soft-primary"><i class="bi bi-people-fill"></i></span>
                        <p>{{ __('superadmin.statistics.total_customers') }}</p>
                        <strong>{{ number_format($summaryCustomers) }}</strong>
                    </div>
                </div>
                <div class="col-lg-4 col-xl-2">
                    <div class="enterprise-card">
                        <span class="enterprise-icon bg-soft-warning"><i class="bi bi-cart-fill"></i></span>
                        <p>{{ __('superadmin.statistics.customer_orders') }}</p>
                        <strong>{{ number_format($summaryOrders) }}</strong>
                    </div>
                </div>
                <div class="col-lg-4 col-xl-2">
                    <div class="enterprise-card">
                        <span class="enterprise-icon bg-soft-success"><i class="bi bi-box-seam"></i></span>
                        <p>{{ __('superadmin.statistics.total_products') }}</p>
                        <strong>{{ number_format($summaryProducts) }}</strong>
                    </div>
                </div>
                <div class="col-lg-4 col-xl-2">
                    <div class="enterprise-card">
                        <span class="enterprise-icon bg-soft-info"><i class="bi bi-cash-stack"></i></span>
                        <p>{{ __('superadmin.statistics.total_revenue') }}</p>
                        <strong>{{ number_format($summaryRevenue, 2) }} SAR</strong>
                    </div>
                </div>
                <div class="col-lg-4 col-xl-2">
                    <div class="enterprise-card">
                        <span class="enterprise-icon bg-soft-danger"><i class="bi bi-graph-up-arrow"></i></span>
                        <p>{{ __('superadmin.statistics.analytics') }}</p>
                        <strong>{{ number_format($summaryAnalytics) }}</strong>
                    </div>
                </div>
            </div>
        </section>

        <section class="global-stats-section mb-4">
            <div class="section-header">
                <div>
                    <h2>{{ __('superadmin.farms.title') }}</h2>
                    <p>{{ __('superadmin.farms.desc') }}</p>
                </div>
            </div>

            <div class="row g-3 stats-grid">
                @foreach ($farmSummaryCards as $farm)
                    <div class="col-lg-6 col-xl-3">
                        <article class="enterprise-card farm-summary-card farm-summary-card-{{ ($loop->index % 4) + 1 }} h-100">
                            <div class="farm-summary-head">
                                <span class="enterprise-icon bg-soft-success"><i class="bi bi-house-heart"></i></span>
                                <span class="farm-type-pill">{{ __('farms.options.' . $farm['type']) }}</span>
                            </div>
                            <div class="farm-summary-title">
                                <h3>{{ $farm['name'] }}</h3>
                                <p>{{ $farm['location'] ?: __('superadmin.farms.no_location') }}</p>
                            </div>

                            <div class="farm-summary-metrics">
                                <div>
                                    <span>{{ __('superadmin.farms.pens') }}</span>
                                    <strong>{{ number_format($farm['pens_count']) }}</strong>
                                </div>
                                <div>
                                    <span>{{ __('superadmin.farms.animals') }}</span>
                                    <strong>{{ number_format($farm['animals_count']) }}</strong>
                                </div>
                                <div>
                                    <span>{{ __('superadmin.farms.poultry_groups') }}</span>
                                    <strong>{{ number_format($farm['poultry_groups_count']) }}</strong>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="customer-insights-section mb-4">
            <div class="customer-insights-header">
                <div>
                    <span class="eyebrow">{{ __('superadmin.customer_insights.eyebrow') }}</span>
                    <h2>{{ __('superadmin.customer_insights.title') }}</h2>
                    <p>{{ __('superadmin.customer_insights.desc') }}</p>
                </div>
                <span class="badge badge-soft-info">{{ __('superadmin.customer_insights.modern_overview') }}</span>
            </div>

            <div class="customer-kpi-grid">
                @foreach ($analyticsCards as $card)
                    <article class="customer-kpi-widget tone-{{ $card['tone'] }}">
                        <span class="customer-kpi-icon"><i class="bi {{ $card['icon'] }}"></i></span>
                        <div>
                            <small>{{ $card['label'] }}</small>
                            <strong>{{ $card['value'] }}</strong>
                            <span>{{ $card['meta'] }}</span>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="customer-chart-grid">
                <article class="customer-chart-card wide">
                    <div class="customer-chart-head">
                        <div>
                            <h3>{{ __('superadmin.customer_insights.revenue_analytics') }}</h3>
                            <p>{{ __('superadmin.customer_insights.revenue_analytics_desc') }}</p>
                        </div>
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div class="customer-modern-chart">
                        <canvas id="customerRevenueAnalyticsChart"></canvas>
                    </div>
                </article>

                <article class="customer-chart-card">
                    <div class="customer-chart-head">
                        <div>
                            <h3>{{ __('superadmin.customer_insights.orders_activity') }}</h3>
                            <p>{{ __('superadmin.customer_insights.orders_activity_desc') }}</p>
                        </div>
                        <i class="bi bi-cart-check"></i>
                    </div>
                    <div class="customer-modern-chart compact">
                        <canvas id="customerOrdersActivityChart"></canvas>
                    </div>
                </article>

                <article class="customer-chart-card">
                    <div class="customer-chart-head">
                        <div>
                            <h3>{{ __('superadmin.customer_insights.engagement') }}</h3>
                            <p>{{ __('superadmin.customer_insights.engagement_desc') }}</p>
                        </div>
                        <i class="bi bi-activity"></i>
                    </div>
                    <div class="customer-modern-chart compact">
                        <canvas id="customerEngagementChart"></canvas>
                    </div>
                </article>

                <article class="customer-chart-card wide">
                    <div class="customer-chart-head">
                        <div>
                            <h3>{{ __('superadmin.customer_insights.monthly_growth_analytics') }}</h3>
                            <p>{{ __('superadmin.customer_insights.monthly_growth_desc') }}</p>
                        </div>
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div class="customer-modern-chart">
                        <canvas id="customerMonthlyGrowthChart"></canvas>
                    </div>
                </article>
            </div>

            <div class="customer-card-grid">
                @forelse ($customerInsightCards as $customer)
                    <article class="modern-customer-card">
                        <div class="modern-customer-top">
                            <div class="modern-customer-identity">
                                <span class="modern-customer-avatar">{{ \Illuminate\Support\Str::of($customer['name'])->substr(0, 1)->upper() }}</span>
                                <div>
                                    <h3>{{ $customer['name'] }}</h3>
                                    <p>{{ $customer['email'] }}</p>
                                </div>
                            </div>
                            <span class="status-pill status-{{ $customer['status'] }}">
                                {{ __("superadmin.status.{$customer['status']}") }}
                            </span>
                        </div>

                        <div class="modern-customer-body">
                            <div class="customer-ring" style="--progress: {{ $customer['activity_percent'] }}">
                                <div>
                                    <strong>{{ $customer['activity_percent'] }}%</strong>
                                    <span>{{ __('superadmin.customer_insights.activity') }}</span>
                                </div>
                            </div>
                            <div class="modern-customer-metrics">
                                <div>
                                    <small>{{ __('superadmin.customer_insights.revenue') }}</small>
                                    <strong>{{ number_format($customer['total_revenue'], 2) }} SAR</strong>
                                </div>
                                <div>
                                    <small>{{ __('superadmin.customer_insights.orders') }}</small>
                                    <strong>{{ number_format($customer['orders_count']) }}</strong>
                                </div>
                                <div>
                                    <small>{{ __('superadmin.customer_insights.products') }}</small>
                                    <strong>{{ number_format($customer['products_count']) }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="mini-area-chart">
                            <canvas class="customer-mini-chart" data-values='@json($customer['sparkline'])'></canvas>
                        </div>

                        <div class="modern-customer-foot">
                            <span>{{ __('superadmin.customer_insights.last_order') }}</span>
                            <strong>{{ $customer['last_order_date']?->diffForHumans() ?? __('superadmin.messages.no_data') }}</strong>
                        </div>
                    </article>
                @empty
                    <div class="insights-empty-state">
                        <i class="bi bi-people"></i>
                        <span>{{ __('superadmin.customer_insights.no_customers') }}</span>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="analytics-overview mb-4">
            <div class="row g-3">
                <div class="col-xl-7">
                    <div class="chart-card">
                        <div class="chart-card-header">
                            <div>
                                <h3>{{ __('superadmin.analytics.revenue_trend') }}</h3>
                                <p>{{ __('superadmin.analytics.growth_across') }}</p>
                            </div>
                            <span class="badge badge-soft-success">+{{ $activityGrowth }}%</span>
                        </div>
                        <div class="chart-card-body">
                            <canvas id="revenueTrendChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5">
                    <div class="chart-card chart-card-small">
                        <div class="chart-card-header">
                            <div>
                                <h3>{{ __('superadmin.analytics.order_mix') }}</h3>
                                <p>{{ __('superadmin.analytics.share_of_orders') }}</p>
                            </div>
                        </div>
                        <div class="chart-card-body compact">
                            <canvas id="ordersDonutChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <script type="application/json" id="superadminDashboardData">
        {!! json_encode([
            'revenueLabels' => $revenueLabels,
            'revenueValues' => $revenueValues,
            'ordersStatusLabels' => $ordersStatusLabels,
            'ordersStatusValues' => $ordersStatusValues,
            'labelRevenue' => $labelRevenue,
            'growthLabels' => $growthLabels,
            'growthCustomers' => $growthCustomers,
            'growthOrders' => $growthOrders,
            'growthRevenue' => $growthRevenue,
            'growthActivity' => $growthActivity,
        ], JSON_UNESCAPED_UNICODE) !!}
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Chart === 'undefined') return;

            const dataEl = document.getElementById('superadminDashboardData');
            const data = dataEl ? JSON.parse(dataEl.textContent || '{}') : {};

            const revenueLabels = data.revenueLabels || [];
            const revenueValues = data.revenueValues || [];
            const ordersStatusLabels = data.ordersStatusLabels || [];
            const ordersStatusValues = data.ordersStatusValues || [];
            const labelRevenue = data.labelRevenue || '';
            const growthLabels = data.growthLabels || [];
            const growthCustomers = data.growthCustomers || [];
            const growthOrders = data.growthOrders || [];
            const growthRevenue = data.growthRevenue || [];
            const growthActivity = data.growthActivity || [];

            const heroTrendCtx = document.getElementById('heroTrendChart');
            if (heroTrendCtx) {
                new Chart(heroTrendCtx, {
                    type: 'line',
                    data: {
                        labels: revenueLabels,
                        datasets: [{
                            label: labelRevenue,
                            data: revenueValues,
                            fill: true,
                            backgroundColor: 'rgba(56, 161, 105, 0.18)',
                            borderColor: 'rgba(34, 110, 58, 0.92)',
                            tension: 0.42,
                            pointRadius: 0,
                            borderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { x: { display: false }, y: { display: false } }
                    }
                });
            }

            const revenueTrendCtx = document.getElementById('revenueTrendChart');
            if (revenueTrendCtx) {
                new Chart(revenueTrendCtx, {
                    type: 'line',
                    data: {
                        labels: revenueLabels,
                        datasets: [{
                            label: labelRevenue,
                            data: revenueValues,
                            fill: true,
                            backgroundColor: 'rgba(34, 197, 94, 0.16)',
                            borderColor: 'rgba(34, 110, 58, 0.92)',
                            tension: 0.35,
                            pointRadius: 0,
                            borderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { display: false } },
                            y: { grid: { color: 'rgba(15, 23, 42, 0.06)' } }
                        }
                    }
                });
            }

            const ordersDonutCtx = document.getElementById('ordersDonutChart');
            if (ordersDonutCtx) {
                new Chart(ordersDonutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ordersStatusLabels,
                        datasets: [{
                            data: ordersStatusValues,
                            backgroundColor: ['#22c55e', '#3b82f6', '#f59e0b', '#6366f1'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom', labels: { boxWidth: 10 } } }
                    }
                });
            }

            const chartDefaults = {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true } },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.92)',
                        padding: 12,
                        cornerRadius: 12
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { color: 'rgba(15, 23, 42, 0.06)' } }
                }
            };

            const customerRevenueCtx = document.getElementById('customerRevenueAnalyticsChart');
            if (customerRevenueCtx) {
                new Chart(customerRevenueCtx, {
                    type: 'line',
                    data: {
                        labels: growthLabels,
                        datasets: [{
                            label: '{{ __('superadmin.customer_insights.revenue') }}',
                            data: growthRevenue,
                            fill: true,
                            borderColor: 'rgba(34, 110, 58, 0.95)',
                            backgroundColor: 'rgba(34, 197, 94, 0.16)',
                            tension: 0.42,
                            pointRadius: 3,
                            borderWidth: 3
                        }]
                    },
                    options: chartDefaults
                });
            }

            const customerOrdersCtx = document.getElementById('customerOrdersActivityChart');
            if (customerOrdersCtx) {
                new Chart(customerOrdersCtx, {
                    type: 'bar',
                    data: {
                        labels: growthLabels,
                        datasets: [{
                            label: '{{ __('superadmin.customer_insights.orders') }}',
                            data: growthOrders,
                            backgroundColor: 'rgba(59, 130, 246, 0.68)',
                            borderRadius: 12,
                            maxBarThickness: 34
                        }]
                    },
                    options: chartDefaults
                });
            }

            const customerEngagementCtx = document.getElementById('customerEngagementChart');
            if (customerEngagementCtx) {
                new Chart(customerEngagementCtx, {
                    type: 'line',
                    data: {
                        labels: growthLabels,
                        datasets: [{
                            label: '{{ __('superadmin.customer_insights.activity') }}',
                            data: growthActivity,
                            fill: true,
                            borderColor: 'rgba(245, 158, 11, 0.92)',
                            backgroundColor: 'rgba(245, 158, 11, 0.14)',
                            tension: 0.42,
                            pointRadius: 3,
                            borderWidth: 3
                        }]
                    },
                    options: chartDefaults
                });
            }

            const monthlyGrowthCtx = document.getElementById('customerMonthlyGrowthChart');
            if (monthlyGrowthCtx) {
                new Chart(monthlyGrowthCtx, {
                    type: 'bar',
                    data: {
                        labels: growthLabels,
                        datasets: [
                            {
                                type: 'line',
                                label: '{{ __('superadmin.customer_insights.revenue') }}',
                                data: growthRevenue,
                                borderColor: 'rgba(34, 110, 58, 0.95)',
                                backgroundColor: 'rgba(34, 197, 94, 0.14)',
                                tension: 0.35,
                                fill: true,
                                yAxisID: 'y1',
                                pointRadius: 3
                            },
                            {
                                label: '{{ __('superadmin.customer_insights.customers') }}',
                                data: growthCustomers,
                                backgroundColor: 'rgba(34, 197, 94, 0.72)',
                                borderRadius: 10,
                                yAxisID: 'y'
                            },
                            {
                                label: '{{ __('superadmin.customer_insights.orders') }}',
                                data: growthOrders,
                                backgroundColor: 'rgba(59, 130, 246, 0.62)',
                                borderRadius: 10,
                                yAxisID: 'y'
                            },
                            {
                                label: '{{ __('superadmin.customer_insights.activity') }}',
                                data: growthActivity,
                                backgroundColor: 'rgba(245, 158, 11, 0.62)',
                                borderRadius: 10,
                                yAxisID: 'y'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 10, usePointStyle: true } }
                        },
                        scales: {
                            x: { grid: { display: false } },
                            y: { beginAtZero: true, grid: { color: 'rgba(15, 23, 42, 0.06)' } },
                            y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } }
                        }
                    }
                });
            }

            document.querySelectorAll('.customer-mini-chart').forEach(function (canvas) {
                let values = [];
                try { values = JSON.parse(canvas.getAttribute('data-values') || '[]'); } catch (e) { values = []; }
                if (!values.length) values = [0, 0, 0, 0, 0, 0];

                new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels: values.map((_, index) => index + 1),
                        datasets: [{
                            data: values,
                            fill: true,
                            borderColor: 'rgba(34, 110, 58, 0.92)',
                            backgroundColor: 'rgba(34, 197, 94, 0.14)',
                            tension: 0.45,
                            pointRadius: 0,
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false }, tooltip: { enabled: false } },
                        scales: { x: { display: false }, y: { display: false } }
                    }
                });
            });

        });
    </script>


@endsection
