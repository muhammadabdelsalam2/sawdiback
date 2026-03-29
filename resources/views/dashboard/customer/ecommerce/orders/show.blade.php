@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Order {{ $order->order_no }}</h3>
        <a class="btn btn-light" href="{{ route('customer.ecommerce.orders.index', ['locale' => request()->route('locale')]) }}">Back</a>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <div class="row g-3">
        <div class="col-md-8">
            <div class="card mb-3"><div class="card-body">
                <h5 class="card-title">Order Items</h5>
                <table class="table align-middle">
                    <thead><tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Tax</th><th>Total</th></tr></thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->unit_price, 2) }}</td>
                                <td>{{ number_format($item->line_tax ?? 0, 2) }}</td>
                                <td>{{ number_format($item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div></div>

            <div class="card"><div class="card-body">
                <h5 class="card-title">Status Timeline</h5>
                <ul class="list-group list-group-flush">
                    @forelse($order->statusHistories as $history)
                        <li class="list-group-item d-flex justify-content-between">
                            <div>
                                <strong>{{ ucfirst(str_replace('_', ' ', $history->to_status)) }}</strong>
                                @if($history->notes)
                                    <div class="text-muted small">{{ $history->notes }}</div>
                                @endif
                            </div>
                            <span class="text-muted">{{ optional($history->changed_at)->format('Y-m-d H:i') }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">No status updates yet.</li>
                    @endforelse
                </ul>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card mb-3"><div class="card-body">
                <h5 class="card-title">Order Summary</h5>
                <div class="mb-2"><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $order->status)) }}</div>
                <div class="mb-2"><strong>Payment:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }} ({{ $order->payment_status }})</div>
                <div class="mb-2"><strong>Subtotal:</strong> {{ number_format($order->subtotal, 2) }}</div>
                <div class="mb-2"><strong>Shipping:</strong> {{ number_format($order->shipping, 2) }}</div>
                <div class="mb-2"><strong>Taxes:</strong> {{ number_format($order->taxes, 2) }}</div>
                <div class="mb-2"><strong>Discount:</strong> {{ number_format($order->discount, 2) }}</div>
                <div class="mb-2"><strong>Total:</strong> {{ number_format($order->total, 2) }}</div>
            </div></div>

            <div class="card mb-3"><div class="card-body">
                <h5 class="card-title">Shipping Address</h5>
                @if($order->address)
                    <div>{{ $order->address->label }}</div>
                    <div class="text-muted small">{{ $order->address->address_line_1 }}</div>
                    <div class="text-muted small">{{ $order->address->city }} {{ $order->address->country }}</div>
                    <div class="text-muted small">{{ $order->address->phone }}</div>
                @else
                    <div class="text-muted">No address</div>
                @endif
            </div></div>

            <div class="card"><div class="card-body">
                <h5 class="card-title">Update Status</h5>
                <form method="POST" action="{{ route('customer.ecommerce.orders.status', ['locale' => request()->route('locale'), 'order' => $order->id]) }}">
                    @csrf
                    <div class="mb-2">
                        <select name="status" class="form-select">
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <textarea name="notes" class="form-control" rows="2" placeholder="Notes (optional)"></textarea>
                    </div>
                    <button class="btn btn-primary w-100">Update</button>
                </form>
            </div></div>
        </div>
    </div>
</div>
@endsection
