<div class="mb-3">
    <label class="form-label">{{ __('procurement.requisitions.fields.code') }}</label>
    <input type="text" name="code" class="form-control" value="{{ old('code', $requisition->code ?? '') }}">
</div>
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">{{ __('procurement.requisitions.fields.department') }} *</label>
        <select name="department_id" class="form-select" required>
            <option value="">{{ __('procurement.common.select') }}</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}" @selected((int) old('department_id', $requisition->department_id ?? 0) === $dept->id)>{{ $dept->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('procurement.requisitions.fields.requested_by') }} *</label>
        <select name="requested_by" class="form-select" required>
            <option value="">{{ __('procurement.common.select') }}</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" @selected((int) old('requested_by', $requisition->requested_by ?? 0) === $user->id)>{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('procurement.requisitions.fields.status') }} *</label>
        <select name="status" class="form-select" required>
            @foreach(['pending','approved','rejected','converted_to_po'] as $value)
                <option value="{{ $value }}" @selected(old('status', $requisition->status ?? 'pending') === $value)>{{ __('procurement.status.requisition.' . $value) }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="row g-3 mt-1">
    <div class="col-md-12">
        <label class="form-label">{{ __('procurement.requisitions.fields.notes') }}</label>
        <input type="text" name="notes" class="form-control" value="{{ old('notes', $requisition->notes ?? '') }}">
    </div>
</div>

<hr>
<div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0">{{ __('procurement.requisitions.items.title') }}</h5>
    <button type="button" class="btn btn-sm btn-outline-primary" id="add-item-row">{{ __('procurement.requisitions.items.add') }}</button>
</div>

<div class="table-responsive">
    <table class="table" id="req-items-table">
        <thead><tr><th>{{ __('procurement.requisitions.items.product') }}</th><th>{{ __('procurement.requisitions.items.quantity') }}</th><th>{{ __('procurement.requisitions.items.estimated_price') }}</th><th></th></tr></thead>
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
                <td><input type="number" step="0.01" class="form-control" name="items[{{ $index }}][estimated_price]" value="{{ old("items.$index.estimated_price", $item['estimated_price']) }}"></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger remove-item">{{ __('procurement.requisitions.items.remove') }}</button></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.querySelector('#req-items-table tbody');
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
            <td><input type="number" step="0.01" class="form-control" name="items[${index}][estimated_price]" value="0"></td>
            <td><button type="button" class="btn btn-sm btn-outline-danger remove-item">{{ __('procurement.requisitions.items.remove') }}</button></td>
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
