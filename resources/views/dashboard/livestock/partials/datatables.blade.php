@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
        (function() {
            const dataTableLanguage = {
                search: @json(__('livestock.datatable.search')),
                lengthMenu: @json(__('livestock.datatable.show')) + ' ' + @json(__('livestock.datatable.show_menu')) + ' ' + @json(__('livestock.datatable.entries')),
                info: @json(__('livestock.datatable.info')),
                infoEmpty: @json(__('livestock.datatable.info_empty')),
                zeroRecords: @json(__('livestock.datatable.zero_records')),
                emptyTable: @json(__('livestock.datatable.empty_table')),
                paginate: {
                    first: @json(__('livestock.datatable.first')),
                    last: @json(__('livestock.datatable.last')),
                    next: @json(__('livestock.datatable.next')),
                    previous: @json(__('livestock.datatable.previous'))
                }
            };

            function initLivestockTables() {
                if (!window.jQuery || !jQuery.fn.DataTable) return;

                jQuery('.js-livestock-table').each(function() {
                    if (jQuery.fn.dataTable.isDataTable(this)) return;

                    jQuery(this).DataTable({
                        pageLength: 10,
                        lengthMenu: [10, 25, 50, 100],
                        order: [],
                        autoWidth: false,
                        pagingType: 'simple_numbers',
                        language: dataTableLanguage
                    });
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initLivestockTables);
            } else {
                initLivestockTables();
            }
        })();
    </script>
@endpush
