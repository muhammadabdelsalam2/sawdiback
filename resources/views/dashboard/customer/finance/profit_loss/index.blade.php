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
