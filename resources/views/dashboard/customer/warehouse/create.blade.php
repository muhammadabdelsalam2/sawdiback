@extends('layouts.customer.dashboard')

@section('title', __('warehouse.title'))

@section('content')
<div class="container-fluid py-4">
    <h2 class="fw-bold mb-4">{{ __('warehouse.actions.add_asset') }}</h2>
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('customer.warehouse-assets.store', ['locale' => app()->getLocale()]) }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('warehouse.fields.name') }}</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('warehouse.fields.type') }}</label>
                        <select name="type" class="form-select" required>
                            <option value="equipment">{{ __('warehouse.types.equipment') }}</option>
                            <option value="water_pipes">{{ __('warehouse.types.water_pipes') }}</option>
                            <option value="iron">{{ __('warehouse.types.iron') }}</option>
                            <option value="other">{{ __('warehouse.types.other') }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('warehouse.fields.storage_location') }}</label>
                        <input type="text" name="storage_location" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('warehouse.fields.quantity_or_status') }}</label>
                        <input type="text" name="quantity_or_status" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('warehouse.fields.farm') }}</label>
                        <select name="farm_id" class="form-select">
                            <option value="">{{ __('warehouse.fields.select_farm') }}</option>
                            @foreach($farms as $farm)
                                <option value="{{ $farm->id }}">{{ $farm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('warehouse.fields.notes') }}</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('warehouse.fields.attachments') }}</label>
                        <input type="file" name="attachments[]" class="form-control" multiple>
                    </div>
                </div>
                <div class="mt-4">
                    <button class="btn btn-primary">{{ __('common.save') }}</button>
                    <a href="{{ route('customer.warehouse-assets.index', ['locale' => app()->getLocale()]) }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
