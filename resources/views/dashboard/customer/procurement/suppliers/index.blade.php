@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('procurement.suppliers.title') }}</h3>
        <a class="btn btn-primary" href="{{ route('customer.procurement.suppliers.create', ['locale' => request()->route('locale')]) }}">{{ __('procurement.suppliers.add') }}</a>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <form class="card card-body mb-3" method="GET">
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control" placeholder="{{ __('procurement.common.filter') }}" value="{{ request('q') }}">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">{{ __('procurement.common.all_status') }}</option>
                    <option value="active" @selected(request('status') === 'active')>{{ __('procurement.status.supplier.active') }}</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>{{ __('procurement.status.supplier.inactive') }}</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">{{ __('procurement.common.filter') }}</button>
                <a class="btn btn-light w-100" href="{{ route('customer.procurement.suppliers.index', ['locale' => request()->route('locale')]) }}">{{ __('procurement.common.reset') }}</a>
            </div>
        </div>
    </form>

    <div class="card"><div class="card-body">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('procurement.suppliers.fields.name') }}</th>
                    <th>{{ __('procurement.suppliers.fields.email') }}</th>
                    <th>{{ __('procurement.suppliers.fields.phone') }}</th>
                    <th>{{ __('procurement.suppliers.fields.status') }}</th>
                    <th class="text-end no-sort">{{ __('procurement.common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->name }}</td>
                    <td>{{ $row->email ?? '-' }}</td>
                    <td>{{ $row->phone ?? '-' }}</td>
                    <td>{{ $row->is_active ? __('procurement.status.supplier.active') : __('procurement.status.supplier.inactive') }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('customer.procurement.suppliers.edit', ['locale' => request()->route('locale'), 'supplier' => $row->id]) }}">{{ __('procurement.common.edit') }}</a>
                        <form class="d-inline" method="POST" action="{{ route('customer.procurement.suppliers.destroy', ['locale' => request()->route('locale'), 'supplier' => $row->id]) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('procurement.suppliers.confirm_delete') }}')">{{ __('procurement.common.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">{{ __('procurement.suppliers.empty') }}</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $rows->links() }}
    </div></div>
</div>
@endsection
