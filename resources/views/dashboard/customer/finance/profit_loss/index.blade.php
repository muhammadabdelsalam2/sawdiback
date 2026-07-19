@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('finance.profit_loss.title') }}</h3>
        <form class="d-flex gap-2" method="GET">
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            <button class="btn btn-outline-primary">{{ __('finance.common.filter') }}</button>
        </form>
    </div>

    <div class="card mb-3"><div class="card-body">
        <div class="row g-3">
            <div class="col-md-4"><strong>{{ __('finance.profit_loss.total_revenue') }}:</strong> {{ number_format($report['total_revenue'] ?? 0, 2) }}</div>
            <div class="col-md-4"><strong>{{ __('finance.profit_loss.total_expenses') }}:</strong> {{ number_format($report['total_expenses'] ?? 0, 2) }}</div>
            <div class="col-md-4"><strong>{{ __('finance.profit_loss.net_profit') }}:</strong> {{ number_format($report['net_profit'] ?? 0, 2) }}</div>
        </div>
    </div></div>

    <div class="card mb-3"><div class="card-body">
        <h5>{{ __('finance.profit_loss.livestock_pens') }}</h5>
        <div class="row g-3">
            <div class="col-md-3"><strong>{{ __('finance.profit_loss.pen_sales') }}:</strong> {{ number_format($report['livestock_pens']['total_sales'] ?? 0, 2) }}</div>
            <div class="col-md-3"><strong>{{ __('finance.profit_loss.pen_feed_costs') }}:</strong> {{ number_format($report['livestock_pens']['feed_costs'] ?? 0, 2) }}</div>
            <div class="col-md-3"><strong>{{ __('finance.profit_loss.pen_slaughter_packaging_costs') }}:</strong> {{ number_format($report['livestock_pens']['slaughter_packaging_costs'] ?? 0, 2) }}</div>
            <div class="col-md-3"><strong>{{ __('finance.profit_loss.pen_net_profit') }}:</strong> {{ number_format($report['livestock_pens']['net_profit'] ?? 0, 2) }}</div>
        </div>
    </div></div>

    <div class="card mb-3"><div class="card-body">
        <h5>{{ __('finance.profit_loss.department_profit') }}</h5>
        <div class="row g-3">
            @foreach($report['department_profit']['rows'] as $row)
                <div class="col-md-4">
                    <div class="border rounded p-3">
                        <strong>{{ $row['name'] }}</strong>
                        <div class="mt-2">{{ number_format($row['profit'], 2) }}</div>
                        <div class="text-muted small">{{ __('finance.profit_loss.revenue') }}: {{ number_format($row['revenue'], 2) }}</div>
                        <div class="text-muted small">{{ __('finance.profit_loss.cost') }}: {{ number_format($row['cost'], 2) }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div></div>

    <div class="card mb-3"><div class="card-body">
        <h5>{{ __('finance.profit_loss.sales_summary') }}</h5>
        <div class="row g-3">
            <div class="col-md-4"><strong>{{ __('finance.profit_loss.best_product') }}:</strong> {{ $report['sales_insights']['best_product']['name'] ?? __('finance.common.none') }}</div>
            <div class="col-md-4">
                <strong>{{ __('finance.profit_loss.highest_cost') }}:</strong>
                {{ $report['highest_cost']['label'] ?? __('finance.common.none') }}
                @if(!empty($report['highest_cost']))
                    <span class="text-muted small">({{ $report['highest_cost']['source'] }} - {{ number_format($report['highest_cost']['amount'], 2) }})</span>
                @endif
            </div>
            <div class="col-md-4"><strong>{{ __('finance.profit_loss.sales_value') }}:</strong> {{ number_format($report['sales_insights']['sales_revenue'] ?? 0, 2) }}</div>
        </div>
    </div></div>

    <div class="card mb-3"><div class="card-body">
        <h5>{{ __('finance.profit_loss.mortality_rate') }}</h5>
        <div class="row g-3">
            <div class="col-md-4"><strong>{{ __('finance.profit_loss.deaths') }}:</strong> {{ number_format($report['mortality_rate']['deaths'] ?? 0, 2) }}</div>
            <div class="col-md-4"><strong>{{ __('finance.profit_loss.total_population') }}:</strong> {{ number_format($report['mortality_rate']['total'] ?? 0, 2) }}</div>
            <div class="col-md-4"><strong>{{ __('finance.profit_loss.rate') }}:</strong> {{ number_format($report['mortality_rate']['rate'] ?? 0, 2) }}%</div>
        </div>
    </div></div>

    <div class="card mb-3"><div class="card-body">
        <h5>{{ __('finance.profit_loss.staff_performance') }}</h5>
        @if(!empty($report['staff_performance']['message']))
            <p class="text-muted mb-0">{{ $report['staff_performance']['message'] }}</p>
        @else
            <table class="table align-middle">
                <thead><tr><th>{{ __('finance.profit_loss.department') }}</th><th>{{ __('finance.profit_loss.employee_count') }}</th><th>{{ __('finance.profit_loss.attendance_count') }}</th><th>{{ __('finance.profit_loss.attendance_rate') }}</th></tr></thead>
                <tbody>
                    @foreach($report['staff_performance'] as $row)
                        <tr><td>{{ $row['department'] }}</td><td>{{ $row['employee_count'] }}</td><td>{{ $row['attendance_count'] }}</td><td>{{ number_format($row['attendance_rate'], 2) }}%</td></tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div></div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card"><div class="card-body">
                <h5>{{ __('finance.profit_loss.revenues') }}</h5>
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('finance.profit_loss.account') }}</th>
                            <th>{{ __('finance.profit_loss.amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report['revenues'] as $row)
                            <tr>
                                <td>{{ $row['code'] }} - {{ $row['name'] }}</td>
                                <td>{{ number_format($row['amount'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted py-4">{{ __('finance.profit_loss.empty') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div></div>
        </div>
        <div class="col-md-6">
            <div class="card"><div class="card-body">
                <h5>{{ __('finance.profit_loss.expenses') }}</h5>
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('finance.profit_loss.account') }}</th>
                            <th>{{ __('finance.profit_loss.amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report['expenses'] as $row)
                            <tr>
                                <td>{{ $row['code'] }} - {{ $row['name'] }}</td>
                                <td>{{ number_format($row['amount'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted py-4">{{ __('finance.profit_loss.empty') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div></div>
        </div>
    </div>
</div>
@endsection
