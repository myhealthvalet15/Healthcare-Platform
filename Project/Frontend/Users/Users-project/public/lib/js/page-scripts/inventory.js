if (typeof ohcRights !== 'undefined' && ohcRights.inventory == 2) {
    $('#addInventoryContainer').html(`
            <a href="/others/inventory-add" class="btn btn-secondary add-new btn-primary waves-effect waves-light" style="margin-right: 10px;">
                <span><i class="ti ti-plus me-0 me-sm-1 ti-xs" style="color:#fff;"></i>
                <span style="color:#fff;">Add New Inventory</span></span>
            </a>
        `);
}
'use strict';
let fv, offCanvasEl;
let dt_basic;
document.addEventListener('DOMContentLoaded', function (e) {
    (function () {
        const preloader = document.getElementById('preloader');
        const table = document.getElementById('drugtemplate-table');
        const tbody = document.getElementById('drugtemplate-body');
    })();
    $(function () {
        var dt_basic_table = $('.datatables-basic');
        if (dt_basic_table.length) {
            var dt_basic = dt_basic_table.DataTable({
                ajax: function (data, callback, settings) {
                    apiRequest({
                        url: '/others/inventoryList',
                        method: 'GET',
                        onSuccess: function (response) {
                            if (!response.result) {
                                toastr.error("Failed to fetch data: " + response.data);
                                callback({ data: [] });
                            } else {
                                callback({ data: response.data });
                            }
                        },
                        onError: function (error) {
                            toastr.error(error);
                            callback({ data: [] });
                        }
                    });
                },
                columns: [{
                    data: 'date',
                    title: 'Purchase Date',
                    render: function (data, type, row) {
                        let date = new Date(row.date);
                        if (type === 'sort' || type === 'type') {
                            return date;
                        }
                        if (type === 'display') {
                            return date.toLocaleDateString('en-GB');
                        }
                        return date;
                    }
                },
                {
                    data: 'equipment_name',
                    title: 'Equipment  Name',
                },
                {
                    data: 'manufacturers',
                    title: 'Manufacturer Name'
                },
                {
                    data: 'purchase_order',
                    title: 'Purchase Order',
                },
                {
                    data: 'vendors',
                    title: 'Vendors'
                },
                {
                    data: null,
                    title: 'Actions',
                    render: function (data, type, row) {
                        if (typeof ohcRights !== 'undefined' && ohcRights.inventory == 2) {
                            return `
                <a class="btn btn-sm btn-warning edit-record" 
                   data-id="${row.corporate_inventory_id}" 
                   href="/others/inventory-edit/${row.corporate_inventory_id}" 
                   style="color:#fff;">Edit</a>
            `;
                        } else {
                            return '';
                        }
                    }
                }
                ],
                order: [
                    [0, 'desc']
                ],
                searching: true,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"B>>' +
                    '<"col-sm-12"f>' +
                    't' +
                    '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                displayLength: 10,
                lengthMenu: [10, 25, 50, 75, 100],
                language: {
                    paginate: {
                        next: '<i class="ti ti-chevron-right ti-sm"></i>',
                        previous: '<i class="ti ti-chevron-left ti-sm"></i>'
                    }
                },
                buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fa-sharp fa-solid fa-file-excel"></i>',
                    titleAttr: 'Export to Excel',
                    filename: function () {
                        const today = new Date();
                        const dd = String(today.getDate()).padStart(2, '0');
                        const mm = String(today.getMonth() + 1).padStart(2, '0');
                        const yyyy = today.getFullYear();
                        const formattedDate = `${dd}-${mm}-${yyyy}`;
                        return `Inventory-${formattedDate}`;
                    },
                    className: 'btn-link ms-3',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    },
                }],
                responsive: true,
                initComplete: function () {
                    var api = this.api();
                    $('.datatables-basic tbody').on('click', '.showDetailsBtn', function () {
                        var rowId = $(this).data('drug_template_id');
                        var row = api.row($(this).closest('tr'));
                        var rowData = row.data();
                        if (!rowData) {
                            console.error("Row data not found!");
                            return;
                        }
                        $('#modalName').text(rowData.drug_name);
                        $('#modaldrug_strength').text(rowData.drug_strength);
                        $('#modaldrug_manufacturer').text(rowData.drug_manufacturer);
                        $('#modalrestock_alert_count').text(rowData.restock_alert_count);
                        $('#modalcrd').text(rowData.crd);
                        $('#modalschedule').text(rowData.schedule);
                        $('#modalhsn_code').text(rowData.hsn_code);
                        $('#modalamount_per_strip').text(rowData.amount_per_strip);
                        $('#modalunit_issue').text(rowData.unit_issue);
                        $('#modaltablet_in_strip').text(rowData.tablet_in_strip);
                        $('#modalamount_per_tab').text(rowData.amount_per_tab);
                        $('#modaldiscount').text(rowData.discount);
                        $('#modalsgst').text(rowData.sgst);
                        $('#modalcgst').text(rowData.cgst);
                        $('#modaligst').text(rowData.igst);
                        $('#modaldrug_template_id').text(rowData.drug_template_id);
                        $('#modalbill_status').text(rowData.bill_status == 1 ? 'Active' : 'Inactive');
                        $('#employeeModal').modal('show');
                    });
                    var count = api.data().count();
                    $('#employeeTypeLabel').text(`List of Inventory (${count})`);
                    api.buttons().container().appendTo('#export-buttons');
                }
            });
            $('#DataTables_Table_0_filter label').contents().filter(function () {
                return this.nodeType === 3;
            }).remove();
            $('input[type="search"]').css('width', '400px');
            $('input[type="search"]').css('height', '37px');
            $('input[type="search"]').css('font-size', '15px');
            $('input[type="search"]').css('margin-top', '4px');
            $('.dataTables_filter').addClass('search-container').prependTo('.d-flex.justify-content-end.align-items-center.card-header');
            $('#DataTables_Table_0_filter input').attr('placeholder', 'Search by Equipment Name / Manufacturer Name');
            var existingAddButton = $('.d-flex.justify-content-end.align-items-center.card-header .add-new');
            $('.d-flex.justify-content-end.align-items-center.card-header').append(existingAddButton);
            var excelExportButtonContainer = $('.dt-buttons.btn-group.flex-wrap');
            existingAddButton.removeClass('ms-auto');
            $('.d-flex.justify-content-end.align-items-center.card-header').append(excelExportButtonContainer);
            excelExportButtonContainer.find('button')
                .addClass('ms-3')
                .removeClass('ms-3');
            var excelExportButton = excelExportButtonContainer.find('.buttons-excel');
            excelExportButton
                .removeClass('btn-secondary')
                .addClass('btn-link')
                .find('span').addClass('d-flex justify-content-center')
                .html('<i class="fa-sharp fa-solid fa-file-excel" style="font-size:30px;"></i>');
            existingAddButton.addClass('ms-auto');
            var selectElement = $('.dataTables_length select');
            var targetCell = $('.advance-search th');
            targetCell.append(selectElement);
            $('.dataTables_length label').remove();
            selectElement.css({
                'width': '65px',
                'background-color': '#fff'
            });
            selectElement.addClass('ms-3');
            targetCell.find('.d-flex').append(selectElement);
            $('#searchBtn').on('click', function () {
                var status = $('#status').val();
                var queryParams = {};
                if (status) {
                    queryParams.status = status;
                }
                console.log('Query Params:', queryParams);
                var newUrl = dt_basic.ajax.url().split('?')[0];
                var urlWithParams = newUrl + "?" + $.param(queryParams);
                dt_basic.ajax.url(urlWithParams).load(function () {
                    var count = dt_basic.data().count();
                    $('#employeeTypeLabel').text(`List of Inventory (${count})`);
                });
            });
        }
        $('.datatables-basic tbody').on('click', '.edit-record', function () {
            var id = $(this).data('id');
            console.log(id);
        });
    });
});
