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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<<<<<<< Updated upstream
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
=======
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet">

    {{-- Icon In Title --}}
    <link rel="icon" href="{{ asset('assets/images/logo2.png') }}" type="image/png">
>>>>>>> Stashed changes
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

<<<<<<< Updated upstream
=======
            <x-modals.over />
            <x-modals.basic />
            <x-modals.notification />
            <x-modals.toast />
>>>>>>> Stashed changes
        </div>
    </main>

    {{-- @include('shared.dashboard.footer') --}}

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<<<<<<< Updated upstream
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
=======
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

>>>>>>> Stashed changes
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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

                const currentPath = window.location.pathname;

                // Safety guard: do not auto-initialize DataTables on SuperAdmin screens.
                // These pages have custom layouts and icons; automatic table initialization can break the UI.
                if (
                    currentPath.includes('/superadmin/users') ||
                    currentPath.includes('/superadmin/access-management')
                ) {
                    return;
                }

                // Only initialize tables explicitly marked for DataTables.
                // This prevents accidental frontend regressions on unrelated dashboard pages.
                const tables = jQuery(
                    '#content table.js-datatable, ' +
                    '#content table.data-table, ' +
                    '#content table.datatable, ' +
                    '#content table.registry-table, ' +
                    '#content table.sd-export-table'
                ).not('.no-datatable');

                tables.each(function () {
                    if (jQuery.fn.dataTable.isDataTable(this)) return;

                    const $table = jQuery(this);
                    const columnCount = $table.find('thead th').length;
<<<<<<< Updated upstream
                    const colspannedRows = $table.find('tbody tr').filter(function () {
=======

                    if (!columnCount) return;

                    const colspannedRows = $table.find('tbody tr').filter(function() {
>>>>>>> Stashed changes
                        return jQuery(this).find('td[colspan], td[rowspan]').length > 0;
                    });

                    if (colspannedRows.length) {
                        let hasComplexRows = false;

                        colspannedRows.each(function () {
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

                    const valid = $table.find('tbody tr').toArray().every(function (tr) {
                        return jQuery(tr).find('td').length === columnCount;
                    });

                    if (!valid) {
                        console.warn('Skipped DataTable due to column mismatch:', this);
                        return;
                    }

                    const noSortIndexes = [];
<<<<<<< Updated upstream
                    $table.find('thead th').each(function (idx) {
=======

                    $table.find('thead th').each(function(idx) {
>>>>>>> Stashed changes
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

                selects.each(function () {
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

            function initSalesDistributionExportTables() {
                if (!window.SDExportButtons || typeof window.SDExportButtons.init !== 'function') return;

                window.SDExportButtons.init('#content table.sd-export-table', {
                    language: dataTableLanguage,
                    buttonsText: sdExportButtonsText,
                    searchPlaceholder: @json(__('sales_dist.export.search_placeholder')),
                    isRtl: @json($direction === 'rtl')
                });
            }

<<<<<<< Updated upstream
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () {
                    function () {
                    initGlobalDataTables();
                    initSalesDistributionExportTables();
                }();
                    initGlobalSelectSearch();
                });
            } else {
=======
            function initDashboardPlugins() {
>>>>>>> Stashed changes
                initGlobalDataTables();
                initSalesDistributionExportTables();
                initGlobalSelectSearch();
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initDashboardPlugins);
            } else {
                initDashboardPlugins();
            }
        })();
<<<<<<< Updated upstream
=======

        // Global loading state management for AJAX requests
        (function() {
            let activeRequests = 0;

            function showLoading() {
                if (activeRequests === 0) {
                    jQuery('body').addClass('loading');
                }

                activeRequests++;
            }

            function hideLoading() {
                activeRequests = Math.max(activeRequests - 1, 0);

                if (activeRequests === 0) {
                    jQuery('body').removeClass('loading');
                }
            }

            jQuery(document).ajaxStart(showLoading).ajaxStop(hideLoading);
        })();

        (() => {
            'use strict';

            const forms = document.querySelectorAll('.needs-validation');

            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    form.classList.add('was-validated');
                }, false);
            });
        })();

        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');

            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });

                document.addEventListener('click', function(event) {
                    if (window.innerWidth <= 768) {
                        if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                            sidebar.classList.remove('show');
                        }
                    }
                });

                window.addEventListener('resize', function() {
                    if (window.innerWidth > 768) {
                        sidebar.classList.remove('show');
                    }
                });
            }
        });
>>>>>>> Stashed changes
    </script>

    @stack('scripts')
</body>

<<<<<<< Updated upstream
</html>

=======
</html>
>>>>>>> Stashed changes
