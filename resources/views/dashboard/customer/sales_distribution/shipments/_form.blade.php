<div class="mb-3">
    <label class="form-label">{{ __('sales_dist.shipments.fields.order') }} *</label>
    <select name="sales_order_id" class="form-select" required>
        <option value="">{{ __('sales_dist.shipments.select_order') }}</option>
        @foreach($orders as $orderOption)
            <option value="{{ $orderOption->id }}" @selected((int) old('sales_order_id', $shipment->sales_order_id ?? 0) === $orderOption->id)>{{ $orderOption->order_no }}</option>
        @endforeach
    </select>
</div>
<div class="row g-3">
    <div class="col-md-6"><label class="form-label">{{ __('sales_dist.shipments.fields.shipment_no') }} *</label><input type="text" name="shipment_no" class="form-control" value="{{ old('shipment_no', $shipment->shipment_no ?? '') }}" required></div>
    <div class="col-md-6"><label class="form-label">{{ __('sales_dist.shipments.fields.shipping_company') }} *</label><input type="text" name="shipping_company" class="form-control" value="{{ old('shipping_company', $shipment->shipping_company ?? '') }}" required></div>
</div>
<div class="row g-3 mt-1">
    <div class="col-md-4"><label class="form-label">{{ __('sales_dist.shipments.fields.tracking_no') }}</label><input type="text" name="tracking_no" class="form-control" value="{{ old('tracking_no', $shipment->tracking_no ?? '') }}"></div>
    <div class="col-md-4">
        <label class="form-label">{{ __('sales_dist.common.status') }} *</label>
        <select name="status" class="form-select" required>
            @foreach(['pending', 'packed', 'shipped', 'delivered', 'returned'] as $value)
                <option value="{{ $value }}" @selected(old('status', $shipment->status ?? 'pending') === $value)>{{ __("sales_dist.status.shipment.$value") }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4"><label class="form-label">{{ __('sales_dist.shipments.fields.warehouse_id') }}</label><input type="number" name="warehouse_id" class="form-control" value="{{ old('warehouse_id', $shipment->warehouse_id ?? '') }}"></div>
</div>
<div class="row g-3 mt-1">
    <div class="col-md-6"><label class="form-label">{{ __('sales_dist.shipments.fields.shipped_at') }}</label><input type="datetime-local" name="shipped_at" class="form-control" value="{{ old('shipped_at', isset($shipment) && $shipment->shipped_at ? $shipment->shipped_at->format('Y-m-d\TH:i') : '') }}"></div>
    <div class="col-md-6"><label class="form-label">{{ __('sales_dist.shipments.fields.delivered_at') }}</label><input type="datetime-local" name="delivered_at" class="form-control" value="{{ old('delivered_at', isset($shipment) && $shipment->delivered_at ? $shipment->delivered_at->format('Y-m-d\TH:i') : '') }}"></div>
</div>
<div class="mb-3 mt-3">
    <label class="form-label">{{ __('sales_dist.shipments.fields.notes') }}</label>
    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $shipment->notes ?? '') }}</textarea>
</div>
