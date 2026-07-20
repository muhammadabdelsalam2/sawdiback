@extends('layouts.customer.dashboard')

@section('title', __('warehouse.title'))

@section('content')
<div class="container-fluid py-4">
    <h2 class="fw-bold mb-4">{{ __('warehouse.actions.edit_asset') }}</h2>
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('customer.warehouse-assets.update', ['locale' => request()->route('locale'), 'warehouse_asset' => $warehouseAsset->id]) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('warehouse.fields.name') }}</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $warehouseAsset->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('warehouse.fields.type') }}</label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            @foreach(['equipment','water_pipes','iron','other'] as $type)
                                <option value="{{ $type }}" {{ old('type', $warehouseAsset->type) === $type ? 'selected' : '' }}>{{ __('warehouse.types.' . $type) }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('warehouse.fields.storage_location') }}</label>
                        <input type="text" name="storage_location" class="form-control @error('storage_location') is-invalid @enderror" value="{{ old('storage_location', $warehouseAsset->storage_location) }}">
                        @error('storage_location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('warehouse.fields.quantity_or_status') }}</label>
                        <input type="text" name="quantity_or_status" class="form-control @error('quantity_or_status') is-invalid @enderror" value="{{ old('quantity_or_status', $warehouseAsset->quantity_or_status) }}">
                        @error('quantity_or_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('warehouse.fields.farm') }}</label>
                        <select name="farm_id" class="form-select @error('farm_id') is-invalid @enderror">
                            <option value="">{{ __('warehouse.fields.select_farm') }}</option>
                            @foreach($farms as $farm)
                                <option value="{{ $farm->id }}" {{ old('farm_id', $warehouseAsset->farm_id) == $farm->id ? 'selected' : '' }}>{{ $farm->name }}</option>
                            @endforeach
                        </select>
                        @error('farm_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('warehouse.fields.notes') }}</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $warehouseAsset->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('warehouse.fields.attachments') }}</label>
                        <input type="file" name="attachments[]" class="form-control @error('attachments') is-invalid @enderror @error('attachments.*') is-invalid @enderror" multiple>
                        @error('attachments')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('attachments.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mt-4">
                    <button class="btn btn-primary">{{ __('warehouse.fields.save') }}</button>
                    <a href="{{ route('customer.warehouse-assets.index', ['locale' => request()->route('locale')]) }}" class="btn btn-outline-secondary">{{ __('warehouse.fields.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
