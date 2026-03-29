@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('dashboard.sidebar.ecommerce_orders') }}</h3>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <form class="card card-body mb-3" method="GET">
        <div class="row g-2">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $value)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ ucfirst(str_replace('_', ' ', $value)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">Filter</button>
                <a class="btn btn-light w-100" href="{{ route('customer.ecommerce.orders.index', ['locale' => request()->route('locale')]) }}">Reset</a>
            </div>
        </div>
    </form>

    <div class="card"><div class="card-body">
        <table class="table align-middle">
            <thead><tr><th>#</th><th>Order No</th><th>Customer</th><th>Status</th><th>Total</th><th>Placed At</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->order_no }}</td>
                    <td>{{ $order->user?->name ?? 'N/A' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $order->status)) }}</td>
                    <td>{{ number_format($order->total, 2) }}</td>
                    <td>{{ optional($order->placed_at)->format('Y-m-d H:i') }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-info" href="{{ route('customer.ecommerce.orders.show', ['locale' => request()->route('locale'), 'order' => $order->id]) }}">View</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No orders found.</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $orders->links() }}
    </div></div>
</div>
@endsection
