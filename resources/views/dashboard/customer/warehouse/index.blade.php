@extends('layouts.customer.dashboard')

@section('title', __('warehouse.title'))

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">{{ __('warehouse.title') }}</h2>
            <p class="text-muted mb-0">{{ __('warehouse.description') }}</p>
        </div>
        <a href="{{ route('customer.warehouse-assets.create', ['locale' => app()->getLocale()]) }}" class="btn btn-primary">
            {{ __('warehouse.actions.add_asset') }}
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                    <tr>
                        <th>{{ __('warehouse.fields.name') }}</th>
                        <th>{{ __('warehouse.fields.type') }}</th>
                        <th>{{ __('warehouse.fields.storage_location') }}</th>
                        <th>{{ __('warehouse.fields.quantity_or_status') }}</th>
                        <th>{{ __('warehouse.fields.farm') }}</th>
                        <th>{{ __('warehouse.fields.attachments') }}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($assets as $asset)
                        <tr>
                            <td>{{ $asset->name }}</td>
                            <td>{{ __('warehouse.types.' . $asset->type) }}</td>
                            <td>{{ $asset->storage_location }}</td>
                            <td>{{ $asset->quantity_or_status }}</td>
                            <td>{{ $asset->farm?->name }}</td>
                            <td>
                                @forelse($asset->attachments as $attachment)
                                    <a href="{{ $attachment->url }}" target="_blank" rel="noopener">{{ $attachment->name ?? basename($attachment->path) }}</a>@if(!$loop->last), @endif
                                @empty
                                    -
                                @endforelse
                            </td>
                            <td>
                                <a href="{{ route('customer.warehouse-assets.edit', ['locale' => app()->getLocale(), 'warehouse_asset' => $asset->id]) }}" class="btn btn-sm btn-outline-primary">{{ __('common.edit') }}</a>
                                <form action="{{ route('customer.warehouse-assets.destroy', ['locale' => app()->getLocale(), 'warehouse_asset' => $asset->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">{{ __('common.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">{{ __('warehouse.empty.no_assets') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $assets->links() }}
        </div>
    </div>
</div>
@endsection
