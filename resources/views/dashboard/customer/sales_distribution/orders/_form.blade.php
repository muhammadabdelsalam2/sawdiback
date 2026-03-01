<div class="mb-3">
    <label class="form-label">{{ __('sales_dist.orders.fields.order_no') }} *</label>
    <input type="text" name="order_no" class="form-control" value="{{ old('order_no', $order->order_no ?? '') }}" required>
</div>
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">{{ __('sales_dist.orders.fields.customer') }} *</label>
        <select name="customer_id" class="form-select" required>
            <option value="">{{ __('sales_dist.orders.select_customer') }}</option>
            @foreach($customers as $customerOption)
                <option value="{{ $customerOption->id }}" @selected((int) old('customer_id', $order->customer_id ?? 0) === $customerOption->id)>{{ $customerOption->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('sales_dist.orders.fields.contract') }}</label>
        <select name="contract_id" class="form-select">
            <option value="">{{ __('sales_dist.orders.select_contract') }}</option>
            @foreach($contracts as $contractOption)
                <option value="{{ $contractOption->id }}" @selected((int) old('contract_id', $order->contract_id ?? 0) === $contractOption->id)>{{ $contractOption->contract_code }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('sales_dist.orders.fields.order_date') }} *</label>
        <input type="date" name="order_date" class="form-control" value="{{ old('order_date', isset($order) && $order->order_date ? $order->order_date->format('Y-m-d') : '') }}" required>
    </div>
</div>
<div class="row g-3 mt-1">
    <div class="col-md-6">
        <label class="form-label">{{ __('sales_dist.orders.fields.status') }} *</label>
        <select name="status" class="form-select" required>
            @foreach(['draft', 'confirmed', 'fulfilled', 'cancelled'] as $value)
                <option value="{{ $value }}" @selected(old('status', $order->status ?? 'draft') === $value)>{{ __("sales_dist.status.order.$value") }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">{{ __('sales_dist.orders.fields.notes') }}</label>
        <input type="text" name="notes" class="form-control" value="{{ old('notes', $order->notes ?? '') }}">
    </div>
</div>

<hr>
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0">{{ __('sales_dist.orders.items.title') }}</h5>
    <button type="button" class="btn btn-sm btn-outline-primary" id="add-item-row">{{ __('sales_dist.orders.items.add') }}</button>
</div>

<div class="table-responsive">
    <table class="table" id="order-items-table">
        <thead><tr><th>{{ __('sales_dist.orders.items.product_id') }}</th><th>{{ __('sales_dist.orders.items.qty') }}</th><th>{{ __('sales_dist.orders.items.unit_price') }}</th><th>{{ __('sales_dist.orders.items.discount') }}</th><th></th></tr></thead>
        <tbody>
        @foreach($items as $index => $item)
            <tr>
                <td><input type="number" class="form-control" name="items[{{ $index }}][product_id]" value="{{ old("items.$index.product_id", $item['product_id']) }}" required></td>
                <td><input type="number" step="0.001" class="form-control" name="items[{{ $index }}][qty]" value="{{ old("items.$index.qty", $item['qty']) }}" required></td>
                <td><input type="number" step="0.01" class="form-control" name="items[{{ $index }}][unit_price]" value="{{ old("items.$index.unit_price", $item['unit_price']) }}" required></td>
                <td><input type="number" step="0.01" class="form-control" name="items[{{ $index }}][discount]" value="{{ old("items.$index.discount", $item['discount']) }}"></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger remove-item">{{ __('sales_dist.orders.items.remove') }}</button></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.querySelector('#order-items-table tbody');
    const addBtn = document.getElementById('add-item-row');

    addBtn.addEventListener('click', function () {
        const index = tableBody.querySelectorAll('tr').length;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="number" class="form-control" name="items[${index}][product_id]" required></td>
            <td><input type="number" step="0.001" class="form-control" name="items[${index}][qty]" required></td>
            <td><input type="number" step="0.01" class="form-control" name="items[${index}][unit_price]" required></td>
            <td><input type="number" step="0.01" class="form-control" name="items[${index}][discount]" value="0"></td>
            <td><button type="button" class="btn btn-sm btn-outline-danger remove-item">{{ __('sales_dist.orders.items.remove') }}</button></td>
        `;
        tableBody.appendChild(tr);
    });

    tableBody.addEventListener('click', function (event) {
        if (event.target.classList.contains('remove-item') && tableBody.querySelectorAll('tr').length > 1) {
            event.target.closest('tr').remove();
        }
    });
});
</script>
@endpush
