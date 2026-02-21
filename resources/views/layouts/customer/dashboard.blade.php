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
    <link rel="stylesheet" href="{{ asset('assets/css/pages/datatables-global.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('assets/js/pages/dashboard.js') }}"></script>
    <script>
        (function() {
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

            function initGlobalDataTables() {
                if (!window.jQuery || !jQuery.fn.DataTable) return;

                const tables = jQuery('#content table').not('.no-datatable');

                tables.each(function() {
                    if (jQuery.fn.dataTable.isDataTable(this)) return;

                    const $table = jQuery(this);
                    const columnCount = $table.find('thead th').length;
                    const colspannedRows = $table.find('tbody tr').filter(function() {
                        return jQuery(this).find('td[colspan], td[rowspan]').length > 0;
                    });

                    if (colspannedRows.length) {
                        let hasComplexRows = false;

                        colspannedRows.each(function() {
                            const $row = jQuery(this);
                            const $cells = $row.children('td');
                            const $firstCell = $cells.first();
                            const hasFormContent = $row.find('form, input, select, textarea, button').length > 0;
                            const spanValue = parseInt($firstCell.attr('colspan') || '1', 10);
                            const isSimpleEmptyRow = $cells.length === 1 && !hasFormContent && spanValue >= columnCount;

                            if (isSimpleEmptyRow) {
                                $row.remove();
                            } else {
                                hasComplexRows = true;
                            }
                        });

                        if (hasComplexRows) {
                            return;
                        }
                    }

                    const noSortIndexes = [];
                    $table.find('thead th').each(function(idx) {
                        if (jQuery(this).hasClass('no-sort')) {
                            noSortIndexes.push(idx);
                        }
                    });

                    $table.DataTable({
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

            function initGlobalSelectSearch(root) {
                if (!window.jQuery || !jQuery.fn.select2) return;

                const $root = root ? jQuery(root) : jQuery(document);
                const selects = $root.find('#content select')
                    .not('.no-select-search')
                    .not('.select2-hidden-accessible');

                selects.each(function() {
                    const $select = jQuery(this);
                    const inModal = $select.closest('.modal').length > 0;

                    $select.select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        minimumResultsForSearch: 0,
                        dir: document.documentElement.getAttribute('dir') || 'ltr',
                        dropdownParent: inModal ? $select.closest('.modal') : jQuery(document.body)
                    });
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    initGlobalDataTables();
                    initGlobalSelectSearch();
                });
            } else {
                initGlobalDataTables();
                initGlobalSelectSearch();
            }
        })();
    </script>

    @stack('scripts')
</body>

</html>
