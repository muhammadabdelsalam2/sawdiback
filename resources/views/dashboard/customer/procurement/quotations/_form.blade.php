<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">{{ __('procurement.quotations.fields.rfq') }} *</label>
        <select name="rfq_id" class="form-select" required>
            <option value="">{{ __('procurement.common.select') }}</option>
            @foreach($rfqs as $rfq)
                @php $selected = old('rfq_id', $selectedRfqId ?? $quotation->rfq_id ?? null); @endphp
                <option value="{{ $rfq->id }}" @selected((string) $selected === (string) $rfq->id)>{{ $rfq->code }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('procurement.quotations.fields.supplier') }} *</label>
        <select name="supplier_id" class="form-select" required>
            <option value="">{{ __('procurement.common.select') }}</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}" @selected((string) old('supplier_id', $quotation->supplier_id ?? '') === (string) $supplier->id)>{{ $supplier->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('procurement.quotations.fields.status') }} *</label>
        <select name="status" class="form-select" required>
            @foreach(['submitted','selected','rejected'] as $value)
                <option value="{{ $value }}" @selected(old('status', $quotation->status ?? 'submitted') === $value)>{{ __('procurement.status.quotation.' . $value) }}</option>
            @endforeach
        </select>
    </div>
</div>

<hr>
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0">{{ __('procurement.quotations.items.title') }}</h5>
    <button type="button" class="btn btn-sm btn-outline-primary" id="add-item-row">{{ __('procurement.quotations.items.add') }}</button>
</div>

<div class="table-responsive">
    <table class="table" id="quotation-items-table">
        <thead><tr><th>{{ __('procurement.quotations.items.product') }}</th><th>{{ __('procurement.quotations.items.quantity') }}</th><th>{{ __('procurement.quotations.items.unit_price') }}</th><th></th></tr></thead>
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
                <td><input type="number" step="0.01" class="form-control" name="items[{{ $index }}][unit_price]" value="{{ old("items.$index.unit_price", $item['unit_price']) }}" required></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger remove-item">{{ __('procurement.quotations.items.remove') }}</button></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.querySelector('#quotation-items-table tbody');
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
            <td><input type="number" step="0.01" class="form-control" name="items[${index}][unit_price]" value="0" required></td>
            <td><button type="button" class="btn btn-sm btn-outline-danger remove-item">{{ __('procurement.quotations.items.remove') }}</button></td>
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
