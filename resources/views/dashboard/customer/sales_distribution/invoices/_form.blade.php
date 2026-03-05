<div class="mb-3">
    <label class="form-label">{{ __('sales_dist.invoices.fields.invoice_no') }} *</label>
    <input type="text" name="invoice_no" class="form-control" value="{{ old('invoice_no', $invoice->invoice_no ?? '') }}" required>
</div>
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">{{ __('sales_dist.invoices.fields.order') }} *</label>
        <select name="sales_order_id" class="form-select" required>
            <option value="">{{ __('sales_dist.invoices.select_order') }}</option>
            @foreach($orders as $orderOption)
                <option value="{{ $orderOption->id }}" @selected((int) old('sales_order_id', $invoice->sales_order_id ?? 0) === $orderOption->id)>{{ $orderOption->order_no }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('sales_dist.invoices.fields.customer') }} *</label>
        <select name="customer_id" class="form-select" required>
            <option value="">{{ __('sales_dist.invoices.select_customer') }}</option>
            @foreach($customers as $customerOption)
                <option value="{{ $customerOption->id }}" @selected((int) old('customer_id', $invoice->customer_id ?? 0) === $customerOption->id)>{{ $customerOption->name }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="row g-3 mt-1">
    <div class="col-md-3"><label class="form-label">{{ __('sales_dist.invoices.fields.invoice_date') }} *</label><input type="date" name="invoice_date" class="form-control" value="{{ old('invoice_date', isset($invoice) && $invoice->invoice_date ? $invoice->invoice_date->format('Y-m-d') : '') }}" required></div>
    <div class="col-md-3"><label class="form-label">{{ __('sales_dist.invoices.fields.due_date') }} *</label><input type="date" name="due_date" class="form-control" value="{{ old('due_date', isset($invoice) && $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '') }}" required></div>
    <div class="col-md-3"><label class="form-label">{{ __('sales_dist.invoices.fields.subtotal') }} *</label><input type="number" step="0.01" name="subtotal" class="form-control" value="{{ old('subtotal', $invoice->subtotal ?? '') }}" required></div>
    <div class="col-md-3"><label class="form-label">{{ __('sales_dist.invoices.fields.tax') }}</label><input type="number" step="0.01" name="tax" class="form-control" value="{{ old('tax', $invoice->tax ?? 0) }}"></div>
</div>
<div class="row g-3 mt-1">
    <div class="col-md-6">
        <label class="form-label">{{ __('sales_dist.invoices.fields.status') }} *</label>
        <select name="status" class="form-select" required>
            @foreach(['unpaid', 'partially_paid', 'paid', 'void'] as $value)
                <option value="{{ $value }}" @selected(old('status', $invoice->status ?? 'unpaid') === $value)>{{ __("sales_dist.status.invoice.$value") }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('sales_dist.invoices.fields.notes') }}</label>
        <input type="text" name="notes" class="form-control" value="{{ old('notes', $invoice->notes ?? '') }}">
    </div>
</div>
