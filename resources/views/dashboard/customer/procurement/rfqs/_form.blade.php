<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">{{ __('procurement.rfqs.fields.code') }}</label>
        <input type="text" name="code" class="form-control" value="{{ old('code', $rfq->code ?? '') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('procurement.rfqs.fields.requisition') }} *</label>
        <select name="purchase_requisition_id" class="form-select" required>
            <option value="">{{ __('procurement.common.select') }}</option>
            @foreach($requisitions as $req)
                @php $selected = old('purchase_requisition_id', $selectedRequisitionId ?? $rfq->purchase_requisition_id ?? null); @endphp
                <option value="{{ $req->id }}" @selected((string) $selected === (string) $req->id)>{{ $req->code }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">{{ __('procurement.rfqs.fields.status') }} *</label>
        <select name="status" class="form-select" required>
            @foreach(['open','sent','closed','awarded'] as $value)
                <option value="{{ $value }}" @selected(old('status', $rfq->status ?? 'open') === $value)>{{ __('procurement.status.rfq.' . $value) }}</option>
            @endforeach
        </select>
    </div>
</div>
