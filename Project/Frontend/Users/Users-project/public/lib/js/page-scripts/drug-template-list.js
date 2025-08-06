    'use strict';
    let fv, offCanvasEl;
    let dt_basic;
    document.addEventListener('DOMContentLoaded', function(e) {
        (function() {
            const preloader = document.getElementById('preloader');
            const table = document.getElementById('drugtemplate-table');
            const tbody = document.getElementById('drugtemplate-body');

        })();

        $(function() {
            var dt_basic_table = $('.datatables-basic');
            if (dt_basic_table.length) {
                var dt_basic = dt_basic_table.DataTable({
                    data: @json($drugtemplates),
                    columns: [{
                            data: 'drug_name',
                            title: 'Drug Name - Strength',
                            render: function(data, type, row) {
                                var capitalizedDrugName = row.drug_name.charAt(0).toUpperCase() + row.drug_name.slice(1).toLowerCase();
                                return capitalizedDrugName + ' - ' + row.drug_strength;
                            }
                        },
                        {
                            data: 'drug_type',
                            title: 'Drug Type',
                            render: function(data, type, row) {
                                var drugTypes = @json($drugTypes); // Assuming this is an object, like {1: 'Capsule', 2: 'Cream', ...}

                                // Access the drug type name by using the row.drug_type as the key
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
                            render: function(data, type, row) {
                                console.log("drug_ingredient ID: ", row.drug_ingredient); // Debugging output
                                var drugIngredients = @json($drugIngredients); // This is now an object: { 83: 'Levocetirizine', ... }

                                // Split the drug_ingredient string into an array of IDs
                                var ingredientIds = row.drug_ingredient.split(','); // e.g. "83,97,35,86" -> ["83", "97", "35", "86"]

                                // Map over the ingredientIds and find the corresponding ingredient names
                                var ingredientNames = ingredientIds.map(function(id) {
                                    // Directly access the drugIngredients object by id
                                    return drugIngredients[id] || null; // Return the ingredient name or null if not found
                                });

                                // Join the names into a comma-separated string
                                return ingredientNames.filter(function(name) {
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
                            render: function(data, type, row) {
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
                    initComplete: function() {
                        var api = this.api();
                        $('.datatables-basic tbody').on('click', '.showDetailsBtn', function() {
                            var rowId = $(this).data('drug_template_id');
                            var row = api.row($(this).closest('tr'));
                            var rowData = row.data();
                            if (!rowData) {
                                console.error("Row data not found!");
                                return;
                            }
                            var drugTypeName = @json($drugTypes)[rowData.drug_type] || 'Unknown';
                            var ingredientIds = rowData.drug_ingredient.split(','); // Split the ingredient IDs into an array
                            var ingredientNames = ingredientIds.map(function(id) {
                                return @json($drugIngredients)[id] || null; // Find the ingredient name using the ID
                            }).filter(function(name) {
                                return name !== null; // Filter out any null values
                            }).join(', ');
                            $('#modalName').text(rowData.drug_name);
                            $('#modaldrug_strength').text(rowData.drug_strength);
                            $('#modalNdrug_type').text(drugTypeName);
                            $('#modaldrug_manufacturer').text(rowData.drug_manufacturer);
                            $('#modaldrug_ingredient').text(ingredientNames || 'Unknown Ingredient'); // Display ingredients
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
                $('#DataTables_Table_0_filter label').contents().filter(function() {
                    return this.nodeType === 3; // This filters out the text nodes (like "Search:")
                }).remove();
                $('#DataTables_Table_0_filter input').attr('placeholder', 'Search By:Drug Name Or Type Or Manufacturer');

                // Adjust the search input width
                $('input[type="search"]').css('width', '370px');
                $('input[type="search"]').css('height', '37px'); // Set width to 300px, adjust as needed
                $('input[type="search"]').css('font-size', '15px');
                // Move the search filter to the left of the header (if needed)
                $('.dataTables_filter').addClass('search-container').prependTo('.d-flex.justify-content-end.align-items-center.card-header');

                // Find the "Add New Drug Template" button
                var existingAddButton = $('.d-flex.justify-content-end.align-items-center.card-header .add-new');

                // Append the "Add New Drug Template" button to the right end of the header
                $('.d-flex.justify-content-end.align-items-center.card-header').append(existingAddButton);

                // Move the Excel export button next to the "Add New Drug Template" button
                var excelExportButtonContainer = $('.dt-buttons.btn-group.flex-wrap');

                // Remove the existing "ms-auto" class from the add-new button (if necessary)
                existingAddButton.removeClass('ms-auto');

                // Add the Excel export button next to the "Add New Drug Template" button
                $('.d-flex.justify-content-end.align-items-center.card-header').append(excelExportButtonContainer);

                // Optionally, you can add a specific spacing or styling between the buttons if needed.
                excelExportButtonContainer.find('button')
                    .addClass('ms-3') // Add margin-left if needed
                    .removeClass('ms-3'); // Remove any previous margin that might not fit the layout

                // Modify the Excel export button appearance
                var excelExportButton = excelExportButtonContainer.find('.buttons-excel');
                excelExportButton
                    .removeClass('btn-secondary')
                    .addClass('btn-link')
                    .find('span').addClass('d-flex justify-content-center')
                    .html('<i class="fa-sharp fa-solid fa-file-excel" style="font-size:30px;"></i>');

                // Optionally, adjust the layout of the "Add New Drug Template" button if needed
                existingAddButton.addClass('ms-auto');

                // Move the select dropdown to the appropriate cell
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

            $('.datatables-basic tbody').on('click', '.edit-record', function() {
                var id = $(this).data('id');
                console.log(id);
            });
        });

    });
