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
                data: drugtemplates,
                columns: [{
                    data: 'drug_name',
                    title: 'Drug Name - Strength',
                    render: function (data, type, row) {
                        var capitalizedDrugName = row.drug_name.charAt(0).toUpperCase() + row.drug_name.slice(1).toLowerCase();
                        return capitalizedDrugName + ' - ' + row.drug_strength;
                    }
                },
                {
                    data: 'drug_type',
                    title: 'Drug Type',
                    render: function (data, type, row) {
                        var drugTypes = drugTypes
                        return drugTypes[row.drug_type] || 'Unknown';
                    }
                },
                {
                    data: 'drug_manufacturer',
                    title: 'Drug Manufacturer',
                },
                {
                    data: 'hsn_code',
                    title: 'HSN Code'
                },
                {
                    data: 'drug_ingredient',
                    title: 'Ingredients',
                    render: function (data, type, row) {
                        console.log("drug_ingredient ID: ", row.drug_ingredient);
                        var ingredientIds = row.drug_ingredient.split(',');
                        var ingredientNames = ingredientIds.map(function (id) {
                            return drugIngredients[id] || null;
                        });
                        return ingredientNames.filter(function (name) {
                            return name;
                        }).join(', ') || 'Unknown Ingredient';
                    }
                },
                {
                    data: 'restock_alert_count',
                    title: 'Restock Count'
                },
                {
                    data: 'unit_issue',
                    title: 'Unit Issue',
                    visible: false
                },
                {
                    data: 'amount_per_strip',
                    title: 'Amount Per Strip',
                    visible: false
                },
                {
                    data: null,
                    title: 'Actions',
                    render: function (data, type, row) {
                        return `
                                <a class="btn btn-sm btn-warning edit-record" 
                                   data-id="${row.drug_template_id}" 
                                   target="_blank" 
                                   href="/drugs/drug-template-edit/${row.drug_template_id}" 
                                   style="color:#fff;">Edit</a>&nbsp;
                                <button class="btn btn-outline-primary btn-sm showDetailsBtn" data-id="${row.drug_template_id}" style="border:none;">
                                    <i class="fa fa-eye"></i>
                                </button>
                            `;
                    }
                }
                ],
                order: [
                    [2, 'desc']
                ],
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
                    filename: 'Drug_template_Export',
                    className: 'btn-link ms-3',
                    exportOptions: {
                        modifier: {
                            page: 'all'
                        }
                    },
                    columns: null
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
                        var ingredientIds = rowData.drug_ingredient.split(',');
                        var ingredientNames = ingredientIds.map(function (id) {
                            return drugIngredientsId;
                        }).filter(function (name) {
                            return name !== null;
                        }).join(', ');
                        $('#modalName').text(rowData.drug_name);
                        $('#modaldrug_strength').text(rowData.drug_strength);
                        $('#modalNdrug_type').text(drugTypeName);
                        $('#modaldrug_manufacturer').text(rowData.drug_manufacturer);
                        $('#modaldrug_ingredient').text(ingredientNames || 'Unknown Ingredient');
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
                    $('#employeeTypeLabel').text(`List of Drug Templates (${count})`);
                    api.buttons().container().appendTo('#export-buttons');
                }
            });
            $('#DataTables_Table_0_filter label').contents().filter(function () {
                return this.nodeType === 3;
            }).remove();
            $('#DataTables_Table_0_filter input').attr('placeholder', 'Search By:Drug Name Or Type Or Manufacturer');
            $('input[type="search"]').css('width', '370px');
            $('input[type="search"]').css('height', '37px');
            $('input[type="search"]').css('font-size', '15px');
            $('.dataTables_filter').addClass('search-container').prependTo('.d-flex.justify-content-end.align-items-center.card-header');
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
        }
        $('.datatables-basic tbody').on('click', '.edit-record', function () {
            var id = $(this).data('id');
            console.log(id);
        });
    });
});
