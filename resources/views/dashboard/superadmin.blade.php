@extends('layouts.customer.dashboard')

@section('title', __('superadmin.farms_dashboard.title') . ' | EL-Sawady')

@section('content')
    @php($activeLocale = $currentLocale ?? session('locale_full', 'en-SA'))

    <div class="dashboard-body superadmin-dashboard">
        <section class="dashboard-hero mb-4">
            <div class="hero-copy">
                <span class="eyebrow">{{ __('superadmin.farms_dashboard.eyebrow') }}</span>
                <h1 class="dashboard-title mb-3">{{ __('superadmin.farms_dashboard.title') }}</h1>
                <p class="dashboard-desc mb-0">{{ __('superadmin.farms_dashboard.desc') }}</p>
            </div>
            <div class="hero-panel glass-panel">
                <div class="hero-panel-title">
                    <div>
                        <span class="eyebrow">{{ __('superadmin.farms_dashboard.live_data') }}</span>
                        <h2>{{ __('superadmin.farms_dashboard.farms_count', ['count' => $farms->count()]) }}</h2>
                    </div>
                    <span class="badge badge-soft-success">{{ __('superadmin.farms_dashboard.database_driven') }}</span>
                </div>
                <div class="hero-panel-stats">
                    <div class="hero-stat-card">
                        <small>{{ __('superadmin.statistics.total_farms') }}</small>
                        <strong>{{ number_format($farms->count()) }}</strong>
                    </div>
                    <div class="hero-stat-card">
                        <small>{{ __('superadmin.farms.animals') }}</small>
                        <strong>{{ number_format($farms->sum('animals_count')) }}</strong>
                    </div>
                    <div class="hero-stat-card">
                        <small>{{ __('superadmin.farms.pens') }}</small>
                        <strong>{{ number_format($farms->sum('pens_count')) }}</strong>
                    </div>
                    <div class="hero-stat-card">
                        <small>{{ __('superadmin.farms.orders') }}</small>
                        <strong>{{ number_format($farms->sum('orders_count')) }}</strong>
                    </div>
                </div>
            </div>
        </section>

        <section class="global-stats-section mb-4">
            <div class="section-header">
                <div>
                    <h2>{{ __('superadmin.farms.title') }}</h2>
                    <p>{{ __('superadmin.farms_dashboard.choose_farm_desc') }}</p>
                </div>
            </div>

            <div class="row g-3 stats-grid">
                @forelse ($farms as $farm)
                    <div class="col-lg-6 col-xl-3">
                        <a href="{{ route('superadmin.farms.dashboard', ['locale' => $activeLocale, 'farm' => $farm['id']]) }}" class="text-decoration-none text-reset">
                            <article class="enterprise-card farm-summary-card farm-summary-card-{{ ($loop->index % 4) + 1 }} h-100">
                                <div class="farm-cover mb-3">
                                    @if ($farm['image_url'])
                                        <img src="{{ $farm['image_url'] }}" alt="{{ $farm['name'] }}">
                                    @else
                                        <div class="farm-cover-placeholder">
                                            <i class="bi bi-house-heart"></i>
                                        </div>
                                    @endif
                                    <span class="status-pill status-{{ $farm['status'] }}">{{ __("superadmin.status.{$farm['status']}") }}</span>
                                </div>

                                <div class="farm-summary-title">
                                    <h3>{{ $farm['name'] }}</h3>
                                    <p>{{ $farm['location'] ?: __('superadmin.farms.no_location') }}</p>
                                </div>

                                <div class="farm-summary-metrics">
                                    <div>
                                        <span>{{ __('superadmin.farms.animals') }}</span>
                                        <strong>{{ number_format($farm['animals_count']) }}</strong>
                                    </div>
                                    <div>
                                        <span>{{ __('superadmin.farms.workers') }}</span>
                                        <strong>{{ number_format($farm['workers_count']) }}</strong>
                                    </div>
                                    <div>
                                        <span>{{ __('superadmin.farms.pens') }}</span>
                                        <strong>{{ number_format($farm['pens_count']) }}</strong>
                                    </div>
                                    <div>
                                        <span>{{ __('superadmin.farms.products') }}</span>
                                        <strong>{{ number_format($farm['products_count']) }}</strong>
                                    </div>
                                    <div>
                                        <span>{{ __('superadmin.farms.orders') }}</span>
                                        <strong>{{ number_format($farm['orders_count']) }}</strong>
                                    </div>
                                    <div>
                                        <span>{{ __('superadmin.farms.milk') }}</span>
                                        <strong>{{ number_format($farm['milk_total'], 2) }}</strong>
                                    </div>
                                </div>

                                <div class="modern-customer-foot mt-3">
                                    <span>{{ __('superadmin.farms.poultry_groups') }}</span>
                                    <strong>{{ number_format($farm['poultry_groups_count']) }}</strong>
                                </div>
                            </article>
                        </a>
                    </div>
                @empty
                    <div class="insights-empty-state">
                        <i class="bi bi-house-heart"></i>
                        <span>{{ __('superadmin.farms_dashboard.no_farms') }}</span>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <style>
        .farm-cover {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            aspect-ratio: 16 / 10;
            background: rgba(34, 197, 94, 0.08);
        }

        .farm-cover img,
        .farm-cover-placeholder {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .farm-cover-placeholder {
            display: grid;
            place-items: center;
            color: #226e3a;
            font-size: 38px;
        }

        .farm-cover .status-pill {
            position: absolute;
            inset-block-start: 10px;
            inset-inline-end: 10px;
        }

        .farm-summary-card {
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .farm-summary-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 34px rgba(15, 23, 42, .10);
        }
    </style>
@endsection
