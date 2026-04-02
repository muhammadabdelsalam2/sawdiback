<div class="mb-3">
    <label class="form-label">{{ __('procurement.goods_receipts.fields.grn_number') }}</label>
    <input type="text" name="grn_number" class="form-control" value="{{ old('grn_number', $receipt->grn_number ?? '') }}">
</div>
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">{{ __('procurement.goods_receipts.fields.purchase_order') }} *</label>
        <select name="purchase_order_id" class="form-select" required>
            <option value="">{{ __('procurement.common.select') }}</option>
            @foreach($orders as $order)
                @php $selected = old('purchase_order_id', $selectedOrderId ?? $receipt->purchase_order_id ?? null); @endphp
                <option value="{{ $order->id }}" @selected((string) $selected === (string) $order->id)>{{ $order->po_number }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('procurement.goods_receipts.fields.received_by') }} *</label>
        <select name="received_by" class="form-select" required>
            <option value="">{{ __('procurement.common.select') }}</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" @selected((int) old('received_by', $receipt->received_by ?? 0) === $user->id)>{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('procurement.goods_receipts.fields.status') }} *</label>
        <select name="status" class="form-select" required>
            @foreach(['partial','completed'] as $value)
                <option value="{{ $value }}" @selected(old('status', $receipt->status ?? 'completed') === $value)>{{ __('procurement.status.goods_receipt.' . $value) }}</option>
            @endforeach
        </select>
    </div>
</div>

<hr>
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0">{{ __('procurement.goods_receipts.items.title') }}</h5>
    <button type="button" class="btn btn-sm btn-outline-primary" id="add-item-row">{{ __('procurement.goods_receipts.items.add') }}</button>
</div>

<div class="table-responsive">
    <table class="table" id="grn-items-table">
        <thead><tr><th>{{ __('procurement.goods_receipts.items.product') }}</th><th>{{ __('procurement.goods_receipts.items.quantity') }}</th><th></th></tr></thead>
        <tbody>
        @foreach($items as $index => $item)
            <tr>
                <td>
                    <select class="form-select" name="items[{{ $index }}][product_id]" required>
                        <option value="">{{ __('procurement.common.select') }}</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" @selected((int) old("items.$index.product_id", $item['product_id']) === $product->id)>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" step="0.01" class="form-control" name="items[{{ $index }}][quantity]" value="{{ old("items.$index.quantity", $item['quantity']) }}" required></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger remove-item">{{ __('procurement.goods_receipts.items.remove') }}</button></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.querySelector('#grn-items-table tbody');
    const addBtn = document.getElementById('add-item-row');

    addBtn.addEventListener('click', function () {
        const index = tableBody.querySelectorAll('tr').length;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <select class="form-select" name="items[${index}][product_id]" required>
                    <option value="">{{ __('procurement.common.select') }}</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" step="0.01" class="form-control" name="items[${index}][quantity]" value="1" required></td>
            <td><button type="button" class="btn btn-sm btn-outline-danger remove-item">{{ __('procurement.goods_receipts.items.remove') }}</button></td>
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
