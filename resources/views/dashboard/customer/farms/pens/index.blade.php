@extends('layouts.customer.dashboard')

@section('title', __('farms.titles.pens'))

@section('content')
@php
    $currentLocale = app()->getLocale();
@endphp

<div class="container-fluid my-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h2 class="h3 font-weight-bold text-gray-800 mb-1">{{ __('farms.titles.pens') }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item active" aria-current="page">{{ __('farms.titles.pens') }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('customer.farm-pens.create', ['locale' => $currentLocale]) }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> {{ __('farms.actions.add_pen') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- أزرار التبديل وفلتر المزارع --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div class="d-flex gap-2">
            <a href="{{ route('customer.farm-pens.index', ['locale' => $currentLocale, 'status' => 'active', 'farm_id' => request('farm_id')]) }}" 
               class="btn btn-sm {{ ($status ?? 'active') === 'active' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="fas fa-check-circle mr-1"></i> {{ __('farms.actions.view_active') }}
            </a>
            <a href="{{ route('customer.farm-pens.index', ['locale' => $currentLocale, 'status' => 'trashed', 'farm_id' => request('farm_id')]) }}" 
               class="btn btn-sm {{ ($status ?? '') === 'trashed' ? 'btn-danger' : 'btn-outline-danger' }}">
                <i class="fas fa-trash-restore mr-1"></i> {{ __('farms.actions.view_trashed') }}
            </a>
        </div>

        <form method="GET" action="{{ route('customer.farm-pens.index', ['locale' => $currentLocale]) }}" class="form-inline">
            <input type="hidden" name="status" value="{{ $status ?? 'active' }}">
            <select name="farm_id" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                <option value="">-- {{ __('farms.fields.farm') }}: {{ __('app.all') ?? 'الكل' }} --</option>
                @foreach($farms ?? [] as $f)
                    <option value="{{ $f->id }}" {{ request('farm_id') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('farms.fields.pen_number') }}</th>
                            <th>{{ __('farms.fields.name') }}</th>
                            <th>{{ __('farms.fields.farm') }}</th>
                            <th>{{ __('farms.fields.type') }}</th>
                            <th>{{ __('farms.fields.capacity') }}</th>
                            <th>{{ __('farms.fields.current_count') }}</th>
                            <th>{{ __('farms.fields.status') }}</th>
                            <th>{{ __('farms.fields.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pens as $pen)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="font-weight-bold">{{ $pen->pen_number }}</td>
                                <td>{{ $pen->name ?? '-' }}</td>
                                <td>{{ $pen->farm->name ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-light border">
                                        {{ __('farms.types.' . $pen->type) != 'farms.types.' . $pen->type ? __('farms.types.' . $pen->type) : $pen->type }}
                                    </span>
                                </td>
                                <td>{{ $pen->capacity ?? 0 }}</td>
                                <td>{{ $pen->current_count ?? 0 }}</td>
                                <td>
                                    <span class="badge badge-secondary">
                                        {{ __('farms.options.' . $pen->status) != 'farms.options.' . $pen->status ? __('farms.options.' . $pen->status) : $pen->status }}
                                    </span>
                                </td>
                                <td>
                                    @if($pen->trashed())
                                        <form action="{{ route('customer.farm-pens.restore', ['locale' => $currentLocale, 'farm_pen' => $pen->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('farms.actions.confirm_restore') }}')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="{{ __('farms.actions.restore') }}">
                                                <i class="fas fa-undo mr-1"></i> {{ __('farms.actions.restore') }}
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('customer.farm-pens.show', ['locale' => $currentLocale, 'farm_pen' => $pen->id]) }}" class="btn btn-sm btn-outline-info" title="{{ __('farms.actions.view') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('customer.farm-pens.edit', ['locale' => $currentLocale, 'farm_pen' => $pen->id]) }}" class="btn btn-sm btn-outline-primary" title="{{ __('farms.actions.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('customer.farm-pens.destroy', ['locale' => $currentLocale, 'farm_pen' => $pen->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('app.confirm_delete') ?? 'هل تريد الحذف؟' }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('farms.actions.delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-4 text-muted">
                                    {{ ($status ?? '') === 'trashed' ? __('farms.empty.no_trashed_pens') : __('farms.empty.no_pens') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($pens->hasPages())
            <div class="card-footer bg-white border-top">
                {{ $pens->links() }}
            </div>
        @endif
    </div>
</div>
@endsection