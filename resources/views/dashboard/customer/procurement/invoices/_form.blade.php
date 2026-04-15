<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">{{ __('procurement.invoices.fields.number') }}</label>
        <input type="text" name="number" class="form-control" value="{{ old('number', $invoice->number ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('procurement.invoices.fields.supplier') }} *</label>
        <select name="supplier_id" class="form-select" required>
            <option value="">{{ __('procurement.common.select') }}</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}" @selected((string) old('supplier_id', $invoice->supplier_id ?? '') === (string) $supplier->id)>{{ $supplier->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('procurement.invoices.fields.department') }}</label>
        <select name="department_id" class="form-select">
            <option value="">{{ __('procurement.common.select') }}</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}" @selected((int) old('department_id', $invoice->department_id ?? 0) === $dept->id)>{{ $dept->name }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="row g-3 mt-1">
    <div class="col-md-4">
        <label class="form-label">{{ __('procurement.invoices.fields.purchase_order') }}</label>
        <select name="purchase_order_id" class="form-select">
            <option value="">{{ __('procurement.common.select') }}</option>
            @foreach($orders as $order)
                @php $selected = old('purchase_order_id', $selectedOrderId ?? $invoice->purchase_order_id ?? null); @endphp
                <option value="{{ $order->id }}" @selected((string) $selected === (string) $order->id)>{{ $order->po_number }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('procurement.invoices.fields.goods_receipt') }}</label>
        <select name="goods_receipt_id" class="form-select">
            <option value="">{{ __('procurement.common.select') }}</option>
            @foreach($receipts as $receipt)
                @php $selectedG = old('goods_receipt_id', $selectedReceiptId ?? $invoice->goods_receipt_id ?? null); @endphp
                <option value="{{ $receipt->id }}" @selected((string) $selectedG === (string) $receipt->id)>{{ $receipt->grn_number }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('procurement.invoices.fields.invoice_date') }} *</label>
        <input type="date" name="invoice_date" class="form-control" value="{{ old('invoice_date', isset($invoice) && $invoice->invoice_date ? $invoice->invoice_date->format('Y-m-d') : '') }}" required>
    </div>
</div>
<div class="row g-3 mt-1">
    <div class="col-md-3">
        <label class="form-label">{{ __('procurement.invoices.fields.subtotal') }} *</label>
        <input type="number" step="0.01" name="subtotal" class="form-control" value="{{ old('subtotal', $invoice->subtotal ?? '') }}" required>
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('procurement.invoices.fields.tax') }}</label>
        <input type="number" step="0.01" name="tax" class="form-control" value="{{ old('tax', $invoice->tax ?? 0) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('procurement.invoices.fields.discount') }}</label>
        <input type="number" step="0.01" name="discount" class="form-control" value="{{ old('discount', $invoice->discount ?? 0) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">{{ __('procurement.invoices.fields.status') }} *</label>
        <select name="status" class="form-select" required>
            @foreach(['draft','posted','paid','cancelled'] as $value)
                <option value="{{ $value }}" @selected(old('status', $invoice->status ?? 'draft') === $value)>{{ __('procurement.status.invoice.' . $value) }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="row g-3 mt-1">
    <div class="col-md-12">
        <label class="form-label">{{ __('procurement.invoices.fields.notes') }}</label>
        <input type="text" name="notes" class="form-control" value="{{ old('notes', $invoice->notes ?? '') }}">
    </div>
</div>
