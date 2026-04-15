@extends('layouts.customer.dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ __('procurement.requisitions.title') }}</h3>
        <a class="btn btn-primary" href="{{ route('customer.procurement.requisitions.create', ['locale' => request()->route('locale')]) }}">{{ __('procurement.requisitions.add') }}</a>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <form class="card card-body mb-3" method="GET">
        <div class="row g-2">
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">{{ __('procurement.common.all_status') }}</option>
                    @foreach(['pending','approved','rejected','converted_to_po'] as $value)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ __('procurement.status.requisition.' . $value) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="department_id" class="form-select">
                    <option value="">{{ __('procurement.common.select') }}</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" @selected((int) request('department_id') === $dept->id)>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">{{ __('procurement.common.filter') }}</button>
                <a class="btn btn-light w-100" href="{{ route('customer.procurement.requisitions.index', ['locale' => request()->route('locale')]) }}">{{ __('procurement.common.reset') }}</a>
            </div>
        </div>
    </form>

    <div class="card"><div class="card-body">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('procurement.requisitions.fields.code') }}</th>
                    <th>{{ __('procurement.requisitions.fields.department') }}</th>
                    <th>{{ __('procurement.requisitions.fields.requested_by') }}</th>
                    <th>{{ __('procurement.requisitions.fields.status') }}</th>
                    <th class="text-end no-sort">{{ __('procurement.common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td>{{ $row->id }}</td>
                    <td>{{ $row->code }}</td>
                    <td>{{ $row->department?->name ?? '-' }}</td>
                    <td>{{ $row->requester?->name ?? '-' }}</td>
                    <td>{{ __('procurement.status.requisition.' . $row->status) }}</td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-info" href="{{ route('customer.procurement.requisitions.show', ['locale' => request()->route('locale'), 'requisition' => $row->id]) }}">{{ __('procurement.common.view') }}</a>
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('customer.procurement.requisitions.edit', ['locale' => request()->route('locale'), 'requisition' => $row->id]) }}">{{ __('procurement.common.edit') }}</a>
                        <form class="d-inline" method="POST" action="{{ route('customer.procurement.requisitions.destroy', ['locale' => request()->route('locale'), 'requisition' => $row->id]) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('procurement.requisitions.confirm_delete') }}')">{{ __('procurement.common.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">{{ __('procurement.requisitions.empty') }}</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $rows->links() }}
    </div></div>
</div>
@endsection
