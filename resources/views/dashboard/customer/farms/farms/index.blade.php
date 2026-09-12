@extends('layouts.customer.dashboard')

@section('title', __('farms.titles.farms'))

@section('content')
@php
    $currentLocale = app()->getLocale();
@endphp

<div class="container-fluid my-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h2 class="h3 font-weight-bold text-gray-800 mb-1">{{ __('farms.titles.farms') }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item active" aria-current="page">{{ __('farms.titles.farms') }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('customer.farms.create', ['locale' => $currentLocale]) }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> {{ __('farms.actions.add_farm') }}
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

    {{-- أزرار التبديل بين النشطة والمحذوفة --}}
    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('customer.farms.index', ['locale' => $currentLocale, 'status' => 'active']) }}" 
           class="btn btn-sm {{ ($status ?? 'active') === 'active' ? 'btn-primary' : 'btn-outline-primary' }}">
            <i class="fas fa-check-circle mr-1"></i> {{ __('farms.actions.view_active') }}
        </a>
        <a href="{{ route('customer.farms.index', ['locale' => $currentLocale, 'status' => 'trashed']) }}" 
           class="btn btn-sm {{ ($status ?? '') === 'trashed' ? 'btn-danger' : 'btn-outline-danger' }}">
            <i class="fas fa-trash-restore mr-1"></i> {{ __('farms.actions.view_trashed') }}
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('farms.fields.name') }}</th>
                            <th>{{ __('farms.fields.code') }}</th>
                            <th>{{ __('farms.fields.location') }}</th>
                            <th>{{ __('farms.fields.area_sqm') }}</th>
                            <th>{{ __('farms.fields.pens_count') }}</th>
                            <th>{{ __('farms.fields.is_active') }}</th>
                            <th>{{ __('farms.fields.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($farms as $farm)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="font-weight-bold">{{ $farm->name }}</td>
                                <td>{{ $farm->code ?? '-' }}</td>
                                <td>{{ $farm->location ?? '-' }}</td>
                                <td>{{ $farm->area_sqm ? number_format($farm->area_sqm, 2) : '-' }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $farm->pens_count ?? $farm->pens->count() }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $farm->is_active ? 'success' : 'secondary' }}">
                                        {{ $farm->is_active ? __('farms.options.active') : __('farms.options.inactive') }}
                                    </span>
                                </td>
                                <td>
                                    @if($farm->trashed())
                                        <form action="{{ route('customer.farms.restore', ['locale' => $currentLocale, 'farm' => $farm->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('farms.actions.confirm_restore') }}')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="{{ __('farms.actions.restore') }}">
                                                <i class="fas fa-undo mr-1"></i> {{ __('farms.actions.restore') }}
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('customer.farms.show', ['locale' => $currentLocale, 'farm' => $farm->id]) }}" class="btn btn-sm btn-outline-info" title="{{ __('farms.actions.view') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('customer.farms.edit', ['locale' => $currentLocale, 'farm' => $farm->id]) }}" class="btn btn-sm btn-outline-primary" title="{{ __('farms.actions.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('customer.farms.destroy', ['locale' => $currentLocale, 'farm' => $farm->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('app.confirm_delete') ?? 'هل تريد الحذف؟' }}')">
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
                                <td colspan="8" class="py-4 text-muted">
                                    {{ ($status ?? '') === 'trashed' ? __('farms.empty.no_trashed_farms') : __('farms.empty.no_farms') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($farms->hasPages())
            <div class="card-footer bg-white border-top">
                {{ $farms->links() }}
            </div>
        @endif
    </div>
</div>
@endsection