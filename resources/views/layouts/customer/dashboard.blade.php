<!DOCTYPE html>
<html lang="{{ $currentLang }}" dir="{{ $direction }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'My SaaS')</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.rtl.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/dashboard.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/datatables-global.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Main CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @stack('styles')
</head>

<body dir="{{ $direction }}">

    @include('shared.dashboard.navbar')

    <main>
        <div class="wrapper grow w-100">
            @if (auth()?->user()->hasRole('SuperAdmin'))
                @include('shared.dashboard.superadmin.partial.sidebar')
            @else
                @include('shared.dashboard.customer.partial.sidebar')
            @endif

            <main id="content">
                @yield('content')
            </main>

        </div>
    </main>

    {{-- @include('shared.dashboard.footer') --}}

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('assets/js/pages/dashboard.js') }}"></script>
    <script src="{{ asset('assets/js/pages/sd-export-buttons.js') }}"></script>
    <script>
        (function () {
            const dataTableLanguage = {
                search: @json(__('datatable.search')),
                lengthMenu: @json(__('datatable.show')) + ' ' + @json(__('datatable.show_menu')) + ' ' + @json(__('datatable.entries')),
                info: @json(__('datatable.info')),
                infoEmpty: @json(__('datatable.info_empty')),
                zeroRecords: @json(__('datatable.zero_records')),
                emptyTable: @json(__('datatable.empty_table')),
                paginate: {
                    first: @json(__('datatable.first')),
                    last: @json(__('datatable.last')),
                    next: @json(__('datatable.next')),
                    previous: @json(__('datatable.previous'))
                }
            };
            const sdExportButtonsText = {
                print: @json(__('sales_dist.export.buttons.print')),
                pdf: @json(__('sales_dist.export.buttons.pdf')),
                csv: @json(__('sales_dist.export.buttons.csv')),
                excel: @json(__('sales_dist.export.buttons.excel'))
            };

            function initGlobalDataTables() {
                if (!window.jQuery || !jQuery.fn.DataTable) return;

                const tables = jQuery('#content table').not('.no-datatable');

                tables.each(function () {

                    if (jQuery.fn.dataTable.isDataTable(this)) return;

                    // ✅ FIX: validate column count
                    const thCount = jQuery(this).find('thead th').length;
                    const valid = jQuery(this).find('tbody tr').toArray().every(tr => {
                        return jQuery(tr).find('td').length === thCount;
                    });

                    if (!valid) {
                        console.warn('Skipped DataTable due to column mismatch:', this);
                        return;
                    }

                    const noSortIndexes = [];
                    jQuery(this).find('thead th').each(function (idx) {
                        if (jQuery(this).hasClass('no-sort')) {
                            noSortIndexes.push(idx);
                        }
                    });

                    jQuery(this).DataTable({
                        pageLength: 10,
                        lengthMenu: [10, 25, 50, 100],
                        order: [],
                        autoWidth: false,
                        pagingType: 'simple_numbers',
                        language: dataTableLanguage,
                        columnDefs: noSortIndexes.length ? [{
                            targets: noSortIndexes,
                            orderable: false
                        }] : []
                    });

                });
            }

            function initSalesDistributionExportTables() {
                if (!window.SDExportButtons || typeof window.SDExportButtons.init !== 'function') return;

                window.SDExportButtons.init('#content table.sd-export-table', {
                    language: dataTableLanguage,
                    buttonsText: sdExportButtonsText,
                    searchPlaceholder: @json(__('sales_dist.export.search_placeholder')),
                    isRtl: @json($direction === 'rtl')
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () {
                    initGlobalDataTables();
                    initSalesDistributionExportTables();
                });
            } else {
                initGlobalDataTables();
                initSalesDistributionExportTables();
            }
        })();
    </script>
    @stack('scripts')
</body>

</html>
