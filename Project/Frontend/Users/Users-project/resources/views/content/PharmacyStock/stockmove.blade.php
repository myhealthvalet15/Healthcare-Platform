@extends('layouts/layoutMaster')
@section('title', 'Pharmacy - Stock List')
@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection
@section('page-script')
@vite(['resources/assets/js/extended-ui-sweetalert2.js'])
@endsection
<!-- Vendor Scripts -->
@section('vendor-script')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection
@section('content')
<style>
    td:nth-child(1),
    th:nth-child(1) {
        /* Assuming this is the first column */
        width: 300px;
        /* Adjust to your desired width */
    }
</style>

<!-- Basic Bootstrap Table -->

<div class="card">


    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table">
            <thead>
                <tr class="advance-search mt-3">
                    <th colspan="9" style="background-color:rgb(107, 27, 199);">
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Text on the left side -->
                            <span style="color:#fff;" id="employeeTypeLabel">List of Stocks</span>
                        </div>
                    </th>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
 

                </tr>
            </thead>
        </table>
    </div>
</div>
<div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="employeeModalLabel">Drug Details</h5>
                <span>Pharmacy Stock Id: #<span id="modaldrug_template_id"></span></span>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Container for image and text -->
                <div class="d-flex">
                    <!-- Left side (Text content) -->

                    <div>
                        <p><strong>Drug name - Strength (Type) :</strong> <span id="modalName"></span>&nbsp;-&nbsp;
                            <span id="modaldrug_strength"></span>&nbsp;-&nbsp;(<span id="modalNdrug_type"></span>)</p>
                        <p><strong>Drug manufacturer :</strong> <span id="modaldrug_manufacturer"></span> <span
                                id="modalgender"></span></p>
                        <p><strong>Id No / HSN Code :</strong> <span id="modalhsn_code"></span> / <span
                                id="modalid_no"></span></p>
                        <p><strong>Restock Count :</strong> <span id="modalrestock_alert_count"></span></p>
                        <p><strong>Bill Status :</strong> <span id="modalbill_status"></span></p>
                        <p><strong>Schedule :</strong> <span id="modalschedule"></span></p>
                        <p><strong>CRD :</strong> <span id="modalcrd"></span></p>
                        <p><strong>MRP / MRP Per Unit</strong> <span id="modalamount_per_strip"></span> / <span
                                id="modalamount_per_tab"></p>
                        <p><strong>Package Unit :</strong> <span id="modaltablet_in_strip"></span></p>
                        <p><strong>Unit to Issue :</strong> <span id="modalunit_issue"></span></p>
                        <p><strong>SGST / CGST / IGST </strong> <span id="modalsgst"></span> / <span
                                id="modalcgst"></span> / <span id="modaligst"></span></p>
                        <p><strong>Discount :</strong> <span id="modaldiscount"></span></p>
                        <p><strong>Quantity :</strong> <span id="modalquantity"></span></p>
                        <p><strong>Current Availability :</strong> <span id="modalcurrent_availability"></span></p>
                        <p><strong>Change Stock:</strong> <input type="text" id="modalchange_quantity"
                                class="form-control" />
                        <div>
                            <label><input type="radio" name="qty_action" value="add"> + Plus</label>
                            <label><input type="radio" name="qty_action" value="remove"> - Minus</label>
                        </div>
                        </p>
                        <p><input type="hidden" id="modaldid" class="form-control" /></p>




                        <p><strong>Drug Batch:</strong> <input type="text" id="modaldrug_batch" class="form-control" />
                        </p>

                        <p><strong>Manufacturer Date:</strong>
                            <input type="date" id="modalmanufacter_date" class="form-control" />
                        </p>

                        <p><strong>Expiry Date:</strong>
                            <input type="date" id="modalexpiry_date" class="form-control" />
                        </p>




                    </div>

                    <!-- Right side (Image with padding) -->

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" id="submitQuantity" data-id="modaldid"
                    class="btn btn-primary mt-3">Submit</button>
            </div>
        </div>
    </div>
</div>

</div>
<hr class="my-12">
<script>
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
                    ajax: {
                        url: 'https://login-users.hygeiaes.com/PharmacyStock/getPharmacyStockDetails',
                        dataSrc: function (json) {
                            console.log(json);
                            return json.result && json.data.length > 0 ? json.data : []; // Ensure an empty array if no data
                        }
                    },
                    autoWidth: false,
                    columnDefs: [
                        {
                            targets: 0, // Index of the column, starting from 0
                            width: '300px' // Set your desired width
                        }
                    ],
                    columns: [
                        {
        data: 'drug_id',
        visible: false // Hides the column
    },
                        {
                            data: 'drug_name',
                            title: 'Drug Name - Strength (Type)',
                            render: function (data, type, row) {
                                // Capitalize drug name
                                var capitalizedDrugName = row.drug_name.charAt(0).toUpperCase() + row.drug_name.slice(1).toLowerCase();

                                // Map drug types to names
                                let drugTypes = {
                                    1: "Capsule", 2: "Cream", 3: "Drops", 4: "Foam", 5: "Gel", 6: "Inhaler", 7: "Injection",
                                    8: "Lotion", 9: "Ointment", 10: "Powder", 11: "Shampoo", 12: "Syringe", 13: "Syrup",
                                    14: "Tablet", 15: "Toothpaste", 16: "Suspension", 17: "Spray", 18: "Test"
                                };

                                // Get drug type or 'Unknown' if not found
                                let drugType = drugTypes[row.drug_type] || 'Unknown';

                                // Return combined column: Drug Name - Strength (Drug Type)
                                return `${capitalizedDrugName} - ${row.drug_strength} (${drugType})`;
                            }
                        },
                        {
                            data: null,
                            title: 'Manufacturer / Batch / Quantity',
                            render: function (data, type, row) {
                                // Combine Manufacturer, Batch, and Quantity in the format: Manufacturer - Batch / Quantity
                                return row.drug_manufacturer + ' - ' + row.drug_batch + ' / ' + row.current_availability;
                            }
                        },
                        {
                            data: null,
                            title: 'Manufacturer / Expiry Date',
                            render: function (data, type, row) {
                                let manufacterDate = new Date(row.manufacter_date).toLocaleDateString('en-GB');
                                let expiryDate = new Date(row.expiry_date).toLocaleDateString('en-GB');

                                return manufacterDate + ' <br> ' + expiryDate;
                            }
                        },
                        {
                            data: null,
                            title: 'No of Tablets',
                            render: function (data, type, row) {
                                // Render input for quantity
                                return `
                           
                            <input type="number" name="mqty" data-id="${row.drug_template_id}" style="width:50px;" max="20" required min="1">
       
                        `;
                            }
                        },
                        {
                            data: null,
                            title: 'Sub Pharmacy List',
                            render: function (data, type, row) {
                                // Render select dropdown for sub-pharmacy list
                                return `
                            <select id="stores_${row.drug_template_id}" name="stores" style=" margin-left: 23px; height: 27px; width: 150px; border-color:#e4d7d7; color:#6a5d5d">
                                <option>-Select Store</option>
                            </select>
                        `;
                            }
                        },
                        {
                            data: null,
                            title: 'Action',
                            render: function (data, type, row) {
                                return `<button class="btn btn-sm float-right m-1 btn-primary" main-store-id="${row.ohc_pharmacy_id}" data-id="${row.drug_template_id}" id ="showDetailsBtn"style="border:none;">
                                    Move
                                </button>`;
                            }
                        }
                    ],
                    order: [[0, 'desc']],
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6">>' +
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
                    responsive: true,
                    initComplete: function () {
                        var api = this.api();

                        var count = api.data().count();
                        $('#employeeTypeLabel')
  .text(`List of Stocks (${count})`)
  .css({
    'color': 'white',
    'font-weight': 'bold'
  });

                        
                        api.buttons().container().appendTo('#export-buttons');
                        $.ajax({
                            url: 'https://login-users.hygeiaes.com/corporate/getAllPharmacyDetails',
                            method: 'GET',
                            success: function (response) {
                                if (response.result === true && Array.isArray(response.data) && response.data.length > 0) {
                                    const pharmacies = response.data;

                                    // Loop through the rows and populate the pharmacy dropdown for each row
                                    dt_basic.rows().every(function () {
                                        var row = this.data();
                                        var selectElement = $(`#stores_${row.drug_template_id}`);

                                        // Clear existing options
                                        selectElement.empty();

                                        // Add the default option
                                        selectElement.append('<option>-Select Store</option>');

                                        // Add options for each pharmacy, excluding those with main_pharmacy === 1
                                        pharmacies.forEach(function (pharmacy) {
                                            if (pharmacy.main_pharmacy !== 1) {
                                                const option = $('<option></option>')
                                                    .val(pharmacy.ohc_pharmacy_id) // Set the value to ohc_pharmacy_id
                                                    .text(pharmacy.pharmacy_name); // Set the text to pharmacy_name

                                                selectElement.append(option); // Append the option to the dropdown
                                            }
                                        });
                                    });
                                }
                            },
                            error: function (xhr, status, error) {
                                console.log('Error fetching pharmacy data:', error);
                            }
                        });
                    }
                });

                // Handle the "Move" button click event
                $(document).on('click', '#showDetailsBtn', function () {
                    var drugTemplateId = $(this).data('id'); // Get the drug template ID from the button's data-id
                   // var quantity = $(`input[name="mqty"]`).val(); // Get the quantity from the input field
                   //var main_store_id =  $(this).data('ohc_pharmacy_id');
                   var mainStoreId = $(this).attr('main-store-id');
                   var quantity = $(`input[name="mqty"][data-id="${drugTemplateId}"]`).val(); 
                    
                    var storeId = $(`#stores_${drugTemplateId}`).val(); // Get the selected store ID
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');
                    // Validate the inputs
                    /* if (!quantity || !storeId || storeId === '-Select Store') {
                        alert('Please select a store and enter the quantity.');
                        return;
                    } */

                    // Send the data via AJAX POST request
                    $.ajax({
                        url: 'https://login-users.hygeiaes.com/pharmacy/moveStock',
                        method: 'POST',
                        data: {
                            drug_template_id: drugTemplateId,
                            mainStoreId: mainStoreId,
                            quantity: quantity,
                            store_id: storeId,
                            _token: csrfToken
                        },
                        success: function (response) {
                            if (response.result === true) {
                                showToast("success", "Pharmacy Stock Moved successfully!");
                            // Redirect to the specified URL after successful submission
                            window.location.href =
                                'https://login-users.hygeiaes.com/pharmacy/pharmacy-stock-list';
                            } else {
                                alert('Failed to move stock.');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.log('Error moving stock:', error);
                        }
                    });
                });

                $('#DataTables_Table_0_filter label').contents().filter(function () {
                    return this.nodeType === 3;
                }).remove();
                var searchContainer = document.getElementById('DataTables_Table_0_filter');

    // Apply the style to align the search bar to the left
    searchContainer.style.textAlign = 'left';
    searchContainer.style.marginLeft = '20px';

    // Alternatively, if you want to adjust the margin of the input field:
    var searchInput = searchContainer.querySelector('input');
    searchInput.style.marginLeft = '0'; 
                $('#DataTables_Table_0_filter input').attr('placeholder', 'Drug Name/Manufacturer/Drug Type/Batch');
                $('input[type="search"]').css('width', '340px');
                $('input[type="search"]').css('height', '37px');
                $('input[type="search"]').css('font-size', '15px');
                $('input[type="search"]').css('margin-top', '-19px');  // Set width to 300px, adjust as needed
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
        });





    });
</script>

@endsection
<meta name="csrf-token" content="{{ csrf_token() }}">