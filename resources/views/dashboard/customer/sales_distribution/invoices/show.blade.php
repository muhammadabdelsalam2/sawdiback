@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('sales_dist.invoices.show_title') }}</h3>
        <a class="btn btn-outline-secondary" href="{{ route('customer.sales-distribution.invoices.index', ['locale' => request()->route('locale')]) }}">{{ __('sales_dist.common.back') }}</a>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul></div> @endif

    <div class="card mb-3"><div class="card-body">
        <div><strong>{{ __('sales_dist.invoices.fields.invoice_no') }}:</strong> {{ $invoice->invoice_no }}</div>
        <div><strong>{{ __('sales_dist.invoices.fields.customer') }}:</strong> {{ $invoice->customer->name }}</div>
        <div><strong>{{ __('sales_dist.invoices.fields.order') }}:</strong> {{ $invoice->order->order_no }}</div>
        <div><strong>{{ __('sales_dist.invoices.fields.invoice_date') }}:</strong> {{ $invoice->invoice_date?->format('Y-m-d') }}</div>
        <div><strong>{{ __('sales_dist.invoices.fields.due_date') }}:</strong> {{ $invoice->due_date?->format('Y-m-d') }}</div>
        <div><strong>{{ __('sales_dist.invoices.fields.status') }}:</strong> {{ __("sales_dist.status.invoice.$invoice->status") }}</div>
        <div><strong>{{ __('sales_dist.invoices.fields.subtotal') }}:</strong> {{ number_format($invoice->subtotal, 2) }}</div>
        <div><strong>{{ __('sales_dist.invoices.fields.tax') }}:</strong> {{ number_format($invoice->tax, 2) }}</div>
        <div><strong>{{ __('sales_dist.invoices.fields.total') }}:</strong> {{ number_format($invoice->total, 2) }}</div>
        <div><strong>{{ __('sales_dist.invoices.fields.notes') }}:</strong> {{ $invoice->notes ?: __('sales_dist.common.not_available') }}</div>
    </div></div>

    <div class="card mb-3"><div class="card-body">
        <h5>{{ __('sales_dist.payments.add_title') }}</h5>
        <form method="POST" action="{{ route('customer.sales-distribution.invoices.payments.store', ['locale' => request()->route('locale'), 'invoice' => $invoice->id]) }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">{{ __('sales_dist.payments.fields.amount') }} *</label><input type="number" step="0.01" class="form-control" name="amount" required></div>
                <div class="col-md-3"><label class="form-label">{{ __('sales_dist.payments.fields.paid_at') }} *</label><input type="datetime-local" class="form-control" name="paid_at" required></div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('sales_dist.payments.fields.method') }} *</label>
                    <select class="form-select" name="method" required>
                        <option value="cash">{{ __('sales_dist.status.payment_method.cash') }}</option>
                        <option value="bank">{{ __('sales_dist.status.payment_method.bank') }}</option>
                        <option value="other">{{ __('sales_dist.status.payment_method.other') }}</option>
                    </select>
                </div>
                <div class="col-md-2"><label class="form-label">{{ __('sales_dist.payments.fields.reference') }}</label><input type="text" class="form-control" name="reference"></div>
                <div class="col-md-2"><label class="form-label">{{ __('sales_dist.payments.fields.notes') }}</label><input type="text" class="form-control" name="notes"></div>
            </div>
            <button class="btn btn-primary mt-3">{{ __('sales_dist.payments.record') }}</button>
        </form>
    </div></div>

    <div class="card"><div class="card-body">
        <h5>{{ __('sales_dist.payments.title') }}</h5>
        <table class="table align-middle no-datatable sd-export-table"
            data-export-title="{{ $invoicePaymentsExportTitle }}"
            data-print-scope="page"
            data-pdf-orientation="landscape" data-pdf-page-size="A4">
            <thead><tr><th>#</th><th>{{ __('sales_dist.payments.fields.amount') }}</th><th>{{ __('sales_dist.payments.fields.paid_at') }}</th><th>{{ __('sales_dist.payments.fields.method') }}</th><th>{{ __('sales_dist.payments.fields.reference') }}</th><th class="text-end no-sort no-export">{{ __('sales_dist.common.actions') }}</th></tr></thead>
            <tbody>
            @forelse($invoice->payments as $payment)
                <tr>
                    <td>{{ $payment->id }}</td>
                    <td>{{ number_format($payment->amount, 2) }}</td>
                    <td>{{ $payment->paid_at?->format('Y-m-d H:i') }}</td>
                    <td>{{ __("sales_dist.status.payment_method.$payment->method") }}</td>
                    <td>{{ $payment->reference ?: __('sales_dist.common.not_available') }}</td>
                    <td class="text-end">
                        <form class="d-inline" method="POST" action="{{ route('customer.sales-distribution.invoices.payments.destroy', ['locale' => request()->route('locale'), 'invoice' => $invoice->id, 'payment' => $payment->id]) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('sales_dist.payments.confirm_delete') }}')">{{ __('sales_dist.common.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">{{ __('sales_dist.payments.empty') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>
</div>
@endsection
