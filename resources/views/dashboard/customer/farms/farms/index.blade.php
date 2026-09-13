@extends('layouts.customer.dashboard')

@section('title', __('farms.titles.farms'))

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .farms-page .card-main {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
            border: 1px solid #eef2f6;
            overflow: hidden;
        }

        .farms-page .table thead th {
            background-color: #f8fafc;
            color: #334155;
            font-size: 0.85rem;
            font-weight: 700;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 10px;
            vertical-align: middle;
        }

        .farms-page .table tbody td {
            font-size: 0.88rem;
            color: #1e293b;
            vertical-align: middle;
            padding: 12px 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .farms-page .table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* شارات الحالة */
        .status-badge-active {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.78rem;
            display: inline-block;
        }

        .status-badge-inactive {
            background-color: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.78rem;
            display: inline-block;
        }

        .count-badge {
            background-color: #e0f2fe;
            color: #0369a1;
            font-weight: 700;
            border-radius: 8px;
            padding: 4px 10px;
            font-size: 0.82rem;
            display: inline-block;
        }

        /* أزرار الإجراءات */
        .btn-action-view {
            color: #0284c7;
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 6px;
            padding: 5px 9px;
            transition: all 0.2s ease;
        }
        .btn-action-view:hover {
            background-color: #0284c7;
            color: #ffffff;
        }

        .btn-action-edit {
            color: #d97706;
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 5px 9px;
            transition: all 0.2s ease;
        }
        .btn-action-edit:hover {
            background-color: #d97706;
            color: #ffffff;
        }

        .btn-action-delete {
            color: #e11d48;
            background-color: #fff1f2;
            border: 1px solid #fecdd3;
            border-radius: 6px;
            padding: 5px 9px;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .btn-action-delete:hover {
            background-color: #e11d48;
            color: #ffffff;
        }

        .btn-action-restore {
            color: #16a34a;
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 5px 12px;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .btn-action-restore:hover {
            background-color: #16a34a;
            color: #ffffff;
        }
    </style>
@endpush

@section('content')
@php
    $currentLocale = request()->route('locale') ?? app()->getLocale();
    $isArabic = str_starts_with(strtolower($currentLocale), 'ar');
@endphp

<div class="container-fluid my-4 farms-page">
    {{-- رأس الصفحة --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h2 class="h3 font-weight-bold text-dark mb-1">{{ __('farms.titles.farms') }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item text-muted">{{ __('farms.titles.farms') }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('customer.farms.create', ['locale' => $currentLocale]) }}" class="btn btn-primary px-3 shadow-sm font-weight-bold">
                <i class="fas fa-plus mr-1"></i> {{ __('farms.actions.add_farm') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- أزرار التبديل بين النشطة والمحذوفة --}}
    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('customer.farms.index', ['locale' => $currentLocale, 'status' => 'active']) }}" 
           class="btn btn-sm px-3 font-weight-bold {{ ($status ?? 'active') === 'active' ? 'btn-primary' : 'btn-outline-secondary' }}">
            <i class="fas fa-check-circle mr-1"></i> {{ $isArabic ? 'العناصر النشطة' : 'Active Farms' }}
        </a>
        <a href="{{ route('customer.farms.index', ['locale' => $currentLocale, 'status' => 'trashed']) }}" 
           class="btn btn-sm px-3 font-weight-bold {{ ($status ?? '') === 'trashed' ? 'btn-danger' : 'btn-outline-danger' }}">
            <i class="fas fa-trash-restore mr-1"></i> {{ $isArabic ? 'سلة المحذوفات' : 'Trash' }}
        </a>
    </div>

    {{-- بطاقة الجدول الرئيسية --}}
    <div class="card-main">
        <div class="table-responsive">
            <table class="table table-hover mb-0 text-center">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>{{ __('farms.fields.name') }}</th>
                        <th>{{ __('farms.fields.code') }}</th>
                        <th>{{ __('farms.fields.location') }}</th>
                        <th>{{ __('farms.fields.area_sqm') }} ({{ $isArabic ? 'م²' : 'm²' }})</th>
                        <th>{{ __('farms.fields.pens_count') }}</th>
                        <th>{{ __('farms.fields.is_active') }}</th>
                        <th style="width: 150px;">{{ __('farms.fields.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($farms as $farm)
                        <tr>
                            <td class="text-muted font-weight-bold">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold text-dark">{{ $farm->name }}</td>
                            <td><span class="text-secondary font-monospace">{{ $farm->code ?? '-' }}</span></td>
                            <td>{{ $farm->location ?? '-' }}</td>
                            <td class="font-weight-bold">{{ $farm->area_sqm ? number_format($farm->area_sqm, 2) : '-' }}</td>
                            <td>
                                <span class="count-badge">
                                    <i class="fas fa-layer-group mr-1 text-primary"></i>
                                    {{ $farm->pens_count ?? $farm->pens->count() }}
                                </span>
                            </td>
                            <td>
                                @if($farm->is_active)
                                    <span class="status-badge-active">
                                        <i class="fas fa-circle mr-1" style="font-size: 7px;"></i> {{ $isArabic ? 'نشطة' : 'Active' }}
                                    </span>
                                @else
                                    <span class="status-badge-inactive">
                                        <i class="fas fa-circle mr-1" style="font-size: 7px;"></i> {{ $isArabic ? 'غير نشطة' : 'Inactive' }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    @if($farm->trashed())
                                        <form action="{{ route('customer.farms.restore', ['locale' => $currentLocale, 'farm' => $farm->id]) }}" method="POST" class="d-inline form-restore-confirm">
                                            @csrf
                                            <button type="button" class="btn-action-restore btn-restore-trigger" data-name="{{ $farm->name }}" title="{{ $isArabic ? 'استرجاع' : 'Restore' }}">
                                                <i class="fas fa-undo mr-1"></i> {{ $isArabic ? 'استرجاع' : 'Restore' }}
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('customer.farms.show', ['locale' => $currentLocale, 'farm' => $farm->id]) }}" class="btn-action-view" title="{{ __('farms.actions.view') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('customer.farms.edit', ['locale' => $currentLocale, 'farm' => $farm->id]) }}" class="btn-action-edit" title="{{ __('farms.actions.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('customer.farms.destroy', ['locale' => $currentLocale, 'farm' => $farm->id]) }}" method="POST" class="d-inline form-delete-confirm">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn-action-delete btn-delete-trigger" data-name="{{ $farm->name }}" title="{{ __('farms.actions.delete') }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-5 text-center text-muted">
                                <i class="fas fa-folder-open fa-2x mb-2 d-block text-secondary"></i>
                                {{ ($status ?? '') === 'trashed' ? ($isArabic ? 'لا توجد مزارع في سلة المحذوفات' : 'No farms in trash') : ($isArabic ? 'لا توجد مزارع مسجلة' : 'No farms found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($farms->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $farms->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const currentLang = '{{ strtolower($currentLocale) }}';
            const isArabic = currentLang.startsWith('ar');

            const i18n = {
                deleteTitle: isArabic ? 'هل أنت متأكد من الحذف؟' : 'Are you sure?',
                deleteText: isArabic 
                    ? (name) => name ? `سيتم نقل المزرعة (${name}) إلى سلة المحذوفات.` : 'سيتم نقل المزرعة إلى سلة المحذوفات.'
                    : (name) => name ? `Farm (${name}) will be moved to trash.` : 'This farm will be moved to trash.',
                confirmDeleteBtn: isArabic ? 'نعم، قم بالحذف' : 'Yes, delete it!',
                cancelBtn: isArabic ? 'إلغاء' : 'Cancel',

                restoreTitle: isArabic ? 'تأكيد الاسترجاع' : 'Confirm Restore',
                restoreText: isArabic
                    ? (name) => name ? `هل تريد استعادة المزرعة (${name}) إلى قائمة المزارع النشطة؟` : 'هل تريد استعادة المزرعة؟'
                    : (name) => name ? `Restore (${name}) back to active farms?` : 'Do you want to restore this farm?',
                confirmRestoreBtn: isArabic ? 'نعم، استرجع المزرعة' : 'Yes, restore it!'
            };

            // معالجة تأكيد الحذف
            document.querySelectorAll('.btn-delete-trigger').forEach(function(button) {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const farmName = this.getAttribute('data-name') || '';
                    const form = this.closest('form');

                    Swal.fire({
                        title: i18n.deleteTitle,
                        text: i18n.deleteText(farmName),
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e11d48',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: i18n.confirmDeleteBtn,
                        cancelButtonText: i18n.cancelBtn,
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // معالجة تأكيد الاسترجاع
            document.querySelectorAll('.btn-restore-trigger').forEach(function(button) {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const farmName = this.getAttribute('data-name') || '';
                    const form = this.closest('form');

                    Swal.fire({
                        title: i18n.restoreTitle,
                        text: i18n.restoreText(farmName),
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#16a34a',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: i18n.confirmRestoreBtn,
                        cancelButtonText: i18n.cancelBtn,
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush