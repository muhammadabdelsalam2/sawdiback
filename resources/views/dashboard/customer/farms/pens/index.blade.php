@extends('layouts.customer.dashboard')

@section('title', __('farms.titles.pens'))

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .pens-page .card-main {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
            border: 1px solid #eef2f6;
            overflow: hidden;
        }

        .pens-page .table thead th {
            background-color: #f8fafc;
            color: #334155;
            font-size: 0.85rem;
            font-weight: 700;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 10px;
            vertical-align: middle;
            white-space: nowrap;
        }

        .pens-page .table tbody td {
            font-size: 0.88rem;
            color: #1e293b;
            vertical-align: middle;
            padding: 12px 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .pens-page .table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* منع السكرول غير الضروري وتنعيمه عند الحاجة */
        .pens-page .table-responsive {
            overflow-x: auto !important;
            overflow-y: hidden !important;
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 0, 0, 0.15) transparent;
        }
        .pens-page .table-responsive::-webkit-scrollbar {
            height: 4px;
        }
        .pens-page .table-responsive::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 4px;
        }

        /* شارات الحالات والأنواع */
        .type-badge {
            background-color: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 4px 10px;
            font-weight: 600;
            font-size: 0.8rem;
            display: inline-block;
        }

        .count-badge {
            background-color: #f8fafc;
            color: #334155;
            font-weight: 700;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 0.82rem;
            display: inline-block;
        }

        .status-badge-active {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.78rem;
            display: inline-block;
        }

        .status-badge-maintenance {
            background-color: #fef9c3;
            color: #854d0e;
            border: 1px solid #fef08a;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.78rem;
            display: inline-block;
        }

        .status-badge-quarantine {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.78rem;
            display: inline-block;
        }

        .status-badge-empty {
            background-color: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.78rem;
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

<div class="container-fluid my-4 pens-page">
    {{-- رأس الصفحة --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h2 class="h3 font-weight-bold text-dark mb-1">{{ __('farms.titles.pens') }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item text-muted">{{ __('farms.titles.pens') }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('customer.farm-pens.create', ['locale' => $currentLocale]) }}" class="btn btn-primary px-3 shadow-sm font-weight-bold">
                <i class="fas fa-plus mr-1"></i> {{ __('farms.actions.add_pen') }}
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

    {{-- أزرار التبديل بين النشطة والمحذوفة وفلتر المزرعة --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div class="d-flex gap-2">
            <a href="{{ route('customer.farm-pens.index', ['locale' => $currentLocale, 'status' => 'active', 'farm_id' => request('farm_id')]) }}" 
               class="btn btn-sm px-3 font-weight-bold {{ ($status ?? 'active') === 'active' ? 'btn-primary' : 'btn-outline-secondary' }}">
                <i class="fas fa-check-circle mr-1"></i> {{ $isArabic ? 'العناصر النشطة' : 'Active Pens' }}
            </a>
            <a href="{{ route('customer.farm-pens.index', ['locale' => $currentLocale, 'status' => 'trashed', 'farm_id' => request('farm_id')]) }}" 
               class="btn btn-sm px-3 font-weight-bold {{ ($status ?? '') === 'trashed' ? 'btn-danger' : 'btn-outline-danger' }}">
                <i class="fas fa-trash-restore mr-1"></i> {{ $isArabic ? 'سلة المحذوفات' : 'Trash' }}
            </a>
        </div>

        <form method="GET" action="{{ route('customer.farm-pens.index', ['locale' => $currentLocale]) }}" class="d-flex align-items-center">
            <input type="hidden" name="status" value="{{ $status ?? 'active' }}">
            <select name="farm_id" class="form-select form-select-sm border shadow-sm" onchange="this.form.submit()">
                <option value="">-- {{ __('farms.fields.farm') }}: {{ $isArabic ? 'جميع المزارع' : 'All Farms' }} --</option>
                @foreach($farms ?? [] as $f)
                    <option value="{{ $f->id }}" {{ request('farm_id') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- بطاقة الجدول الرئيسية --}}
    <div class="card-main">
        <div class="table-responsive">
            <table class="table table-hover mb-0 text-center">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>{{ __('farms.fields.pen_number') }}</th>
                        <th>{{ __('farms.fields.name') }}</th>
                        <th>{{ __('farms.fields.farm') }}</th>
                        <th>{{ __('farms.fields.type') }}</th>
                        <th>{{ __('farms.fields.capacity') }}</th>
                        <th>{{ __('farms.fields.current_count') }}</th>
                        <th>{{ __('farms.fields.status') }}</th>
                        <th style="width: 150px;">{{ __('farms.fields.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pens as $pen)
                        <tr>
                            <td class="text-muted font-weight-bold">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold text-dark font-monospace">{{ $pen->pen_number }}</td>
                            <td>{{ $pen->name ?? '-' }}</td>
                            <td class="font-weight-bold">{{ $pen->farm->name ?? '-' }}</td>
                            <td>
                                <span class="type-badge">
                                    {{ __('farms.pen_types.' . $pen->type) != 'farms.pen_types.' . $pen->type ? __('farms.pen_types.' . $pen->type) : ($pen->type ?? '-') }}
                                </span>
                            </td>
                            <td><span class="count-badge">{{ $pen->capacity ?? 0 }}</span></td>
                            <td>
                                <span class="count-badge text-primary font-weight-bold">
                                    {{ $pen->current_count ?? 0 }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusClass = match($pen->status) {
                                        'active'      => 'status-badge-active',
                                        'maintenance' => 'status-badge-maintenance',
                                        'quarantine'  => 'status-badge-quarantine',
                                        default       => 'status-badge-empty',
                                    };
                                @endphp
                                <span class="{{ $statusClass }}">
                                    {{ __('farms.options.' . $pen->status) != 'farms.options.' . $pen->status ? __('farms.options.' . $pen->status) : $pen->status }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    @if($pen->trashed())
                                        <form action="{{ route('customer.farm-pens.restore', ['locale' => $currentLocale, 'farm_pen' => $pen->id]) }}" method="POST" class="d-inline form-restore-confirm">
                                            @csrf
                                            <button type="button" class="btn-action-restore btn-restore-trigger" data-name="{{ $pen->pen_number . ($pen->name ? ' - ' . $pen->name : '') }}" title="{{ $isArabic ? 'استرجاع' : 'Restore' }}">
                                                <i class="fas fa-undo mr-1"></i> {{ $isArabic ? 'استرجاع' : 'Restore' }}
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('customer.farm-pens.show', ['locale' => $currentLocale, 'farm_pen' => $pen->id]) }}" class="btn-action-view" title="{{ __('farms.actions.view') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('customer.farm-pens.edit', ['locale' => $currentLocale, 'farm_pen' => $pen->id]) }}" class="btn-action-edit" title="{{ __('farms.actions.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('customer.farm-pens.destroy', ['locale' => $currentLocale, 'farm_pen' => $pen->id]) }}" method="POST" class="d-inline form-delete-confirm">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn-action-delete btn-delete-trigger" data-name="{{ $pen->pen_number . ($pen->name ? ' - ' . $pen->name : '') }}" title="{{ __('farms.actions.delete') }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-5 text-center text-muted">
                                <i class="fas fa-warehouse fa-2x mb-2 d-block text-secondary"></i>
                                {{ ($status ?? '') === 'trashed' ? ($isArabic ? 'لا توجد حظائر في سلة المحذوفات' : 'No pens in trash') : ($isArabic ? 'لا توجد حظائر مسجلة' : 'No pens found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pens->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $pens->links() }}
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
                    ? (name) => name ? `سيتم نقل الحظيرة (${name}) إلى سلة المحذوفات.` : 'سيتم نقل الحظيرة إلى سلة المحذوفات.'
                    : (name) => name ? `Pen (${name}) will be moved to trash.` : 'This pen will be moved to trash.',
                confirmDeleteBtn: isArabic ? 'نعم، قم بالحذف' : 'Yes, delete it!',
                cancelBtn: isArabic ? 'إلغاء' : 'Cancel',

                restoreTitle: isArabic ? 'تأكيد الاسترجاع' : 'Confirm Restore',
                restoreText: isArabic
                    ? (name) => name ? `هل تريد استعادة الحظيرة (${name}) إلى قائمة الحظائر النشطة؟` : 'هل تريد استعادة الحظيرة؟'
                    : (name) => name ? `Restore (${name}) back to active pens?` : 'Do you want to restore this pen?',
                confirmRestoreBtn: isArabic ? 'نعم، استرجع الحظيرة' : 'Yes, restore it!'
            };

            // معالجة تأكيد الحذف
            document.querySelectorAll('.btn-delete-trigger').forEach(function(button) {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const penName = this.getAttribute('data-name') || '';
                    const form = this.closest('form');

                    Swal.fire({
                        title: i18n.deleteTitle,
                        text: i18n.deleteText(penName),
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
                    const penName = this.getAttribute('data-name') || '';
                    const form = this.closest('form');

                    Swal.fire({
                        title: i18n.restoreTitle,
                        text: i18n.restoreText(penName),
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