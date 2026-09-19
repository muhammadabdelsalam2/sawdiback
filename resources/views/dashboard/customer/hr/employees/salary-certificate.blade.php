@extends('layouts.customer.dashboard')

@section('title', __('hr.salary_certificate') . ' - ' . $employee->full_name)

@section('content')
@php
    $currentLocale = app()->getLocale();
@endphp

<div class="container my-4">
    {{-- شريط التحكم وأزرار الإجراءات (يختفي عند الطباعة) --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <a href="{{ route('customer.hr.employees.show', ['locale' => $currentLocale, 'employee' => $employee->id]) }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-right mr-1"></i> {{ __('hr.actions.back') !== 'hr.actions.back' ? __('hr.actions.back') : 'رجوع' }}
        </a>
        <button onclick="window.print()" class="btn btn-primary shadow-sm">
            <i class="fas fa-print mr-1"></i> {{ __('hr.print_certificate') !== 'hr.print_certificate' ? __('hr.print_certificate') : 'طباعة الشهادة' }}
        </button>
    </div>

    {{-- ورقة الشهادة الرسمية المعتمدة --}}
    <div class="card p-5 shadow-sm border certificate-box bg-white">
        {{-- الترويسة الرسمية --}}
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-4">
            <div>
                <h3 class="font-weight-bold mb-1 text-dark">{{ auth()->user()->tenant->name ?? 'مزرعة السوادي' }}</h3>
                <span class="text-muted font-weight-bold">إدارة الموارد البشرية والشؤون الإدارية</span>
            </div>
            <div class="text-right text-muted" style="line-height: 1.8;">
                <div><strong class="text-dark">تاريخ الإصدار:</strong> {{ $certificateData['issue_date'] ?? date('Y-m-d') }}</div>
                <div><strong class="text-dark">الرقم المرجعي:</strong> CERT-EMP-{{ $employee->id }}-{{ date('Ym') }}</div>
            </div>
        </div>

        {{-- العنوان الرئيسي --}}
        <div class="text-center my-4">
            <h2 class="font-weight-bold text-uppercase border-bottom d-inline-block pb-2 text-dark">
                {{ __('hr.salary_certificate_title') !== 'hr.salary_certificate_title' ? __('hr.salary_certificate_title') : 'شهادة تعريف بالراتب' }}
            </h2>
            <p class="text-muted mt-2 font-weight-bold" style="font-size: 1.1rem;">
                {{ __('hr.to_whom_it_may_concern') !== 'hr.to_whom_it_may_concern' ? __('hr.to_whom_it_may_concern') : 'إلى من يهمه الأمر' }}
            </p>
        </div>

        {{-- نص الإفادة --}}
        <div class="my-4">
            <p style="font-size: 1.15rem; line-height: 2.2; text-align: justify;" class="text-dark">
                تشهد إدارة <strong>{{ auth()->user()->tenant->name ?? 'المنشأة' }}</strong> بأن الموظف الموضحة بياناته أدناه يعمل لدينا ولا يزال على رأس العمل حتى تاريخ إصدار هذه الشهادة:
            </p>
        </div>

        {{-- جدول البيانات الأساسية --}}
        <div class="table-responsive mb-4">
            <table class="table table-bordered mb-0 no-datatable" style="width: 100%;">
                <tbody>
                    <tr>
                        <th class="bg-light text-muted w-25">اسم الموظف</th>
                        <td class="w-25 font-weight-bold text-dark">{{ $certificateData['full_name'] }}</td>
                        <th class="bg-light text-muted w-25">الرقم الوظيفي</th>
                        <td class="w-25 font-weight-bold text-dark">{{ $certificateData['worker_number'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light text-muted">رقم الهوية / الإقامة</th>
                        <td class="font-weight-bold text-dark">{{ $certificateData['national_id'] ?? '-' }}</td>
                        <th class="bg-light text-muted">المسمى الوظيفي</th>
                        <td class="font-weight-bold text-dark">{{ $certificateData['profession'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light text-muted">القسم / الإدارة</th>
                        <td class="font-weight-bold text-dark">{{ $certificateData['department'] ?? '-' }}</td>
                        <th class="bg-light text-muted">تاريخ التعيين</th>
                        <td class="font-weight-bold text-dark">{{ $certificateData['hire_date'] ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- جدول التفاصيل المالية --}}
        <h5 class="font-weight-bold mt-4 mb-3 text-dark">البيان المالي للراتب والمستحقات:</h5>
        <div class="table-responsive my-3">
            <table class="table table-bordered text-center mb-0 no-datatable" style="width: 100%;">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 text-dark">{{ __('hr.basic_salary') !== 'hr.basic_salary' ? __('hr.basic_salary') : 'الراتب الأساسي' }}</th>
                        <th class="py-3 text-dark">{{ __('hr.allowances_and_increments') !== 'hr.allowances_and_increments' ? __('hr.allowances_and_increments') : 'الزيادات والبدلات المستمرة' }}</th>
                        <th class="py-3 text-dark">{{ __('hr.gross_salary') !== 'hr.gross_salary' ? __('hr.gross_salary') : 'إجمالي الراتب الحالي' }}</th>
                        <th class="py-3 text-dark">{{ __('hr.monthly_deduction') !== 'hr.monthly_deduction' ? __('hr.monthly_deduction') : 'استقطاع شهري (سلف)' }}</th>
                        <th class="py-3 bg-light text-primary font-weight-bold">{{ __('hr.net_salary') !== 'hr.net_salary' ? __('hr.net_salary') : 'صافي الراتب المستحق' }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="font-weight-bold align-middle">
                        <td class="py-3 text-dark">{{ number_format((float) ($certificateData['base_salary'] ?? 0), 2) }}</td>
                        <td class="py-3 text-success">+{{ number_format((float) ($certificateData['salary_increases'] ?? 0), 2) }}</td>
                        <td class="py-3 text-dark">{{ number_format((float) ($certificateData['total_salary'] ?? 0), 2) }}</td>
                        <td class="py-3 text-danger">
                            @if(($certificateData['monthly_loan_deduction'] ?? 0) > 0)
                                -{{ number_format((float) $certificateData['monthly_loan_deduction'], 2) }}
                            @else
                                0.00
                            @endif
                        </td>
                        <td class="py-3 text-primary h5 font-weight-bold mb-0">
                            {{ number_format((float) ($certificateData['net_salary'] ?? 0), 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- إخلاء المسؤولية الرسمي --}}
        <p class="mt-4 text-muted" style="font-size: 0.95rem; line-height: 1.8;">
            * أُعطيت له هذه الشهادة بناءً على طلبه لتقديمها لمن يهمه الأمر دون أدنى مسؤولية مالية أو قانونية على المنشأة تجاه التزامات الموظف الشخصية.
        </p>

        {{-- الاعتمادات والأختام --}}
        <div class="row mt-5 pt-4 text-center">
            <div class="col-6">
                <h6 class="font-weight-bold text-dark">{{ __('hr.hr_manager') !== 'hr.hr_manager' ? __('hr.hr_manager') : 'مدير الموارد البشرية' }}</h6>
                <div style="height: 70px;"></div>
                <p class="text-muted">التوقيع: .......................................</p>
            </div>
            <div class="col-6">
                <h6 class="font-weight-bold text-dark">{{ __('hr.company_stamp') !== 'hr.company_stamp' ? __('hr.company_stamp') : 'ختم المنشأة الرسمي' }}</h6>
                <div style="height: 70px;"></div>
                <p class="text-muted">( الختم الرسمي )</p>
            </div>
        </div>
    </div>
</div>

<style>
/* منع تداخل مكتبات DataTables */
.no-datatable {
    border-collapse: collapse !important;
}

.dataTables_wrapper .no-datatable {
    display: table !important;
}

/* تنسيقات الطباعة الرسمية */
@media print {
    @page {
        size: A4 portrait;
        margin: 15mm;
    }

    body {
        background: #ffffff !important;
        color: #000000 !important;
    }

    .no-print,
    .navbar,
    .sidebar,
    .main-sidebar,
    .main-header,
    .main-footer,
    nav,
    aside,
    footer,
    header,
    .dataTables_length,
    .dataTables_filter,
    .dataTables_info,
    .dataTables_paginate {
        display: none !important;
    }

    .container {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .card.certificate-box {
        border: none !important;
        box-shadow: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid #222 !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // تعطيل تهيئة DataTables للجدول المالي في هذه الصفحة
    if (window.jQuery && $.fn.DataTable) {
        $('.no-datatable').each(function() {
            if ($.fn.DataTable.isDataTable(this)) {
                $(this).DataTable().destroy();
            }
        });
    }

    document.querySelectorAll('.dataTables_wrapper').forEach(function(wrapper) {
        const table = wrapper.querySelector('.no-datatable');
        if (table) {
            wrapper.parentNode.insertBefore(table, wrapper);
            wrapper.remove();
        }
    });
});
</script>
@endsection