(function () {
    function resolveExportColumns($table) {
        const indexes = [];
        $table.find('thead th').each(function (idx) {
            if (!jQuery(this).hasClass('no-export')) {
                indexes.push(idx);
            }
        });

        return indexes;
    }

    function resolveNoSortColumns($table) {
        const indexes = [];
        $table.find('thead th').each(function (idx) {
            if (jQuery(this).hasClass('no-sort')) {
                indexes.push(idx);
            }
        });

        return indexes;
    }

    function buildButtons(options, title, exportColumns, pdfOrientation, pdfPageSize, printScope) {
        return [
            {
                extend: 'print',
                text: options.buttonsText.print,
                className: 'btn btn-secondary btn-sm',
                title: title,
                exportOptions: { columns: exportColumns },
                action: function (e, dt, node, config) {
                    if (printScope === 'page') {
                        window.print();
                        return;
                    }

                    jQuery.fn.dataTable.ext.buttons.print.action.call(this, e, dt, node, config);
                }
            },
            {
                extend: 'pdfHtml5',
                text: options.buttonsText.pdf,
                className: 'btn btn-danger btn-sm',
                title: title,
                orientation: pdfOrientation,
                pageSize: pdfPageSize,
                exportOptions: { columns: exportColumns },
                customize: function (doc) {
                    if (!options.isRtl) return;
                    doc.defaultStyle = doc.defaultStyle || {};
                    doc.defaultStyle.alignment = 'right';
                    if (doc.styles && doc.styles.tableHeader) {
                        doc.styles.tableHeader.alignment = 'right';
                    }
                }
            },
            {
                extend: 'csvHtml5',
                text: options.buttonsText.csv,
                className: 'btn btn-info btn-sm',
                title: title,
                exportOptions: { columns: exportColumns }
            },
            {
                extend: 'excelHtml5',
                text: options.buttonsText.excel,
                className: 'btn btn-success btn-sm',
                title: title,
                exportOptions: { columns: exportColumns }
            }
        ];
    }

    window.SDExportButtons = {
        init: function (selector, options) {
            if (!window.jQuery || !jQuery.fn.DataTable || !jQuery.fn.dataTable.Buttons) return;

            const settings = Object.assign({
                language: {},
                buttonsText: {
                    print: 'Print',
                    pdf: 'PDF',
                    csv: 'CSV',
                    excel: 'Excel',
                },
                searchPlaceholder: '',
                isRtl: false,
                dom: '<"d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2"fB>rt<"row mt-2"<"col-md-6"i><"col-md-6"p>>',
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
            }, options || {});

            jQuery(selector).each(function () {
                if (jQuery.fn.dataTable.isDataTable(this)) return;

                const $table = jQuery(this);
                const thCount = $table.find('thead th').length;
                const valid = $table.find('tbody tr').toArray().every(tr => {
                    const $cells = jQuery(tr).find('td');
                    if ($cells.length === 0) return true;
                    if ($cells.length === 1 && $cells.first().attr('colspan')) return true;
                    return $cells.length === thCount;
                });

                if (!valid) return;

                const exportColumns = resolveExportColumns($table);
                const noSortIndexes = resolveNoSortColumns($table);
                const title = $table.data('exportTitle') || document.title;
                const pdfOrientation = $table.data('pdfOrientation') || 'portrait';
                const pdfPageSize = $table.data('pdfPageSize') || 'A4';
                const printScope = $table.data('printScope') || 'table';

                $table.DataTable({
                    pageLength: settings.pageLength,
                    lengthMenu: settings.lengthMenu,
                    order: [],
                    autoWidth: false,
                    pagingType: 'simple_numbers',
                    language: settings.language,
                    dom: settings.dom,
                    buttons: buildButtons(settings, title, exportColumns, pdfOrientation, pdfPageSize, printScope),
                    columnDefs: noSortIndexes.length ? [{
                        targets: noSortIndexes,
                        orderable: false
                    }] : [],
                    initComplete: function () {
                        if (!settings.searchPlaceholder) return;
                        const $searchInput = $table.closest('.dataTables_wrapper').find('.dataTables_filter input');
                        $searchInput.attr('placeholder', settings.searchPlaceholder);
                    }
                });
            });
        }
    };
})();
