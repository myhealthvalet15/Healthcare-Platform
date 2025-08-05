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
    var storeSelect = $('#stores');
    if (dt_basic_table.length) {
        var dt_basic = dt_basic_table.DataTable({
          ajax: {
                url: 'https://login-users.hygeiaes.com/PharmacyStock/getPharmacyStockDetails',
                dataSrc: function(json) {
                  console.log(json); 
                    return json.result && json.data.length > 0 ? json.data : []; // Ensure an empty array if no data
                }
            },
           
            columns: [
                {
        data: 'drug_id',
        visible: false // Hides the column
    },
                {
                    data: 'drug_name',
                    title: 'Drug Name - Strength',
                    render: function(data, type, row) {
        var capitalizedDrugName = row.drug_name.charAt(0).toUpperCase() + row.drug_name.slice(1).toLowerCase();
        return capitalizedDrugName + ' - ' + row.drug_strength;
    }
                },          
               
{ data: 'drug_type', title: 'Drug Type' ,render: function(data, type, row) {
        let drugTypes = {
                1: "Capsule", 2: "Cream", 3: "Drops", 4: "Foam", 5: "Gel", 6: "Inhaler", 7: "Injection",
                8: "Lotion", 9: "Ointment", 10: "Powder", 11: "Shampoo", 12: "Syringe", 13: "Syrup",
                14: "Tablet", 15: "Toothpaste", 16: "Suspension", 17: "Spray", 18: "Test"
            };
        return drugTypes[data] || 'Unknown';
    }}, 
    { data: 'drug_manufacturer', title: 'Drug Manufacturer'},             
    { 
    data: null, 
    title: 'Drug Batch / Quantity', 
    render: function(data, type, row) {
        return row.drug_batch + ' / ' + row.current_availability;
    }
},
{ 
    data: null, 
    title: 'Manufacturer  <br> / Expiry Date', 
    render: function(data, type, row) {
        let manufacterDate = new Date(row.manufacter_date).toLocaleDateString('en-GB'); 
        let expiryDate = new Date(row.expiry_date).toLocaleDateString('en-GB');

        return manufacterDate + ' <br> ' + expiryDate;
    }
},
                
                
                {
                    data: null,
                    title: 'Action',
                    render: function(data, type, row) {
                        return `<button class="btn btn-outline-primary btn-sm showDetailsBtn" data-id="${row.drug_template_id}" style="border:none;">
                                <i class="fa fa-eye"></i>
                            </button>
                        `;
                    }
                }
            ],
            order: [[0, 'desc']],
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
                exportOptions: { modifier: { page: 'all' } },
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
                    $('#modalName').text(rowData.drug_name);
                    $('#modaldrug_strength').text(rowData.drug_strength);
                    $('#modalNdrug_type').text(rowData.drug_type);
                    $('#modaldrug_manufacturer').text(rowData.drug_manufacturer);
                   // $('#modaldrug_ingredient').text(ingredientNames || 'Unknown Ingredient');  // Display ingredients
                    $('#modalrestock_alert_count').text(rowData.restock_alert_count);
                    $('#modalcrd').text(rowData.crd);
                    $('#modalschedule').text(rowData.schedule);
                    $('#modalhsn_code').text(rowData.hsn_code);
                    $('#modalid_no').text(rowData.id_no);
                    $('#modalamount_per_strip').text(rowData.amount_per_strip);
                    $('#modalunit_issue').text(rowData.unit_issue);
                    $('#modaltablet_in_strip').text(rowData.tablet_in_strip);
                    $('#modalamount_per_tab').text(rowData.amount_per_tab);
                    $('#modaldiscount').text(rowData.discount);
                    $('#modalsgst').text(rowData.sgst);
                    $('#modalcgst').text(rowData.cgst);
                    $('#modaligst').text(rowData.igst);
                    $('#modalcurrent_availability').text(rowData.current_availability);
                    $('#modaldrug_batch').val(rowData.drug_batch);
                    $('#modalquantity').text(rowData.quantity);
                    $('#modalmanufacter_date').val(rowData.manufacter_date);
                    $('#modalexpiry_date').val(rowData.expiry_date);
                    $('#modaldrug_template_id').text(rowData.drug_id );
                    $('#modalbill_status').text(rowData.bill_status == 1 ? 'Active' : 'Inactive');                    
                    $('#modaldid').val(rowData.drug_id); 
                    $('#employeeModal').modal('show');
                });
                api.rows().every(function() {
                    var rowData = this.data();
                    var expiryDate = new Date(rowData.expiry_date);
                    var today = new Date();
                    var diffTime = expiryDate - today;
                    var diffDays = Math.ceil(diffTime / (1000 * 3600 * 24)); // Convert milliseconds to days

                    // Check if expiry date is between 45 and 60 days
                    if (diffDays >= 45 && diffDays <= 60) {
                        $(this.node()).addClass('highlight-row'); // Highlight the row
                    }
                });
                var count = api.data().count();
                $('#employeeTypeLabel').text(`List of Stocks (${count})`);
                api.buttons().container().appendTo('#export-buttons');
            }
        });
       // Listen for the change event on the storeSelect element
       storeSelect.change(function() {
    var selectedStoreId = storeSelect.val(); // Get the selected store ID
    console.log("Selected Store ID: ", selectedStoreId);

    // Construct the URL with store_id as part of the path parameter
    var newUrl = '/PharmacyStock/getSubPharmacyDetails/' + selectedStoreId;

    $.ajax({
        url: newUrl,  // Pass store_id in the URL path
        method: 'GET',
        success: function(response) {
            // Check if the response contains data and update the DataTable
            if (response.result) {
                // Assuming the data table rows are being set via the `dataSrc`
                dt_basic.clear();  // Clear existing data
                dt_basic.rows.add(response.data);  // Add the new data to the table
                dt_basic.draw();  // Redraw the DataTable with the new data
            } else {
                console.log('No data found or error occurred');
            }
        },
        error: function(xhr, status, error) {
            console.log("Error fetching data: ", error);
        }
    });
});
$("#availabilitySelect").change(function() {
    var selectedAvailability = $(this).val();  // Get the selected availability option
    var selectedStoreId = $("#stores").val();  // Get the selected store ID

    console.log("Selected Availability: ", selectedAvailability);
    console.log("Selected Store ID: ", selectedStoreId);

    // Check if a store is selected
    if (selectedStoreId == '' || selectedStoreId == '-Select Store') {
        console.log('Please select a store first.');
        return; // Prevent the request if no store is selected
    }

    // Construct the URL with availability and store ID parameters
    var newUrl = '/PharmacyStock/getPharmacyStockByAvailability/' + selectedAvailability + '/' + selectedStoreId;

    $.ajax({
        url: newUrl,  // Pass both availability and store_id in the URL path
        method: 'GET',
        success: function(response) {
            // Check if the response contains data and update the DataTable
            if (response.result) {
                // Assuming the data table rows are being set via the `dataSrc`
                dt_basic.clear();  // Clear existing data
                dt_basic.rows.add(response.data);  // Add the new data to the table
                dt_basic.draw();  // Redraw the DataTable with the new data
            } else {
                console.log('No data found or error occurred');
            }
        },
        error: function(xhr, status, error) {
            console.log("Error fetching data: ", error);
        }
    });
});

        $('#DataTables_Table_0_filter label').contents().filter(function() {
    return this.nodeType === 3; // This filters out the text nodes (like "Search:")
}).remove();

$('#DataTables_Table_0_filter input').attr('placeholder', 'Drug Name/Manufacturer/Type/Batch');
// Adjust the search input width
$('input[type="search"]').css('width', '300px');  // Set width to 300px, adjust as needed
$('#DataTables_Table_0_filter input').css('height', '37px');
$('#DataTables_Table_0_filter input').css('font-size', '15px');

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
  .addClass('ms-3')  // Add margin-left if needed
  .removeClass('ms-3');  // Remove any previous margin that might not fit the layout

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
$('#submitQuantity').on('click', function () {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    let formIsValid = true;

    // Retrieve the drug ID from the input field
    var changequantity = $('#modalchange_quantity');  
    var enteredQuantity = parseInt(changequantity.val().trim()) || 0;  // Get entered quantity
    
    // Validate Quantity Field
    if (changequantity.val().trim() === '') {
        formIsValid = false;
        changequantity.addClass('is-invalid');
        if (changequantity.next('.invalid-feedback').length === 0) {
            changequantity.after('<div class="invalid-feedback">Please enter Quantity.</div>');
        }
    }

    // Drug Batch Validation
    var drug_batch = $('#modaldrug_batch');
    if (drug_batch.val().trim() === '') {
        formIsValid = false;
        drug_batch.addClass('is-invalid');
        if (drug_batch.next('.invalid-feedback').length === 0) {
            drug_batch.after('<div class="invalid-feedback">The Drug batch is required.</div>');
        }
    }

    // Manufacturer Date Validation
    var manufacture_date = $('#modalmanufacter_date');
    if (manufacture_date.val().trim() === '') {
        formIsValid = false;
        manufacture_date.addClass('is-invalid');
        if (manufacture_date.next('.invalid-feedback').length === 0) {
            manufacture_date.after('<div class="invalid-feedback">The Manufacturer Date is required.</div>');
        }
    }

    // Expiry Date Validation
    var expiry_date = $('#modalexpiry_date');
    if (expiry_date.val().trim() === '') {
        formIsValid = false;
        expiry_date.addClass('is-invalid');
        if (expiry_date.next('.invalid-feedback').length === 0) {
            expiry_date.after('<div class="invalid-feedback">Expiry Date is required.</div>');
        }
    }

    // Radio Button Validation (Add or Remove)
    var qtyAction = $('input[name="qty_action"]:checked');
    if (qtyAction.length === 0) {
        formIsValid = false;
        if ($('.qty-action-feedback').length === 0) {
            $('input[name="qty_action"]').last().after('<div class="invalid-feedback qty-action-feedback">Please select + Add or - Remove.</div>');
        }
    }

    // If form is valid, prepare the data and send the AJAX request
    if (formIsValid) { 
        let drugId = $('#modaldid').val();
        let currentAvailability = parseInt($('#modalcurrent_availability').text().trim(), 10) || 0;        
        let newAvailability;
        let soldQuantity = enteredQuantity;

        if (qtyAction.val() === "add") {  // Assuming qtyAction is a radio button with values 'add' or 'remove'
            newAvailability = currentAvailability + enteredQuantity;
        } else if (qtyAction.val() === "remove") {
            if (enteredQuantity > currentAvailability) {
                alert("Cannot remove more than available stock.");
                return;
            }
            newAvailability = currentAvailability - enteredQuantity;
        }

        // Prepare form data including all required fields
        var formData = {
            _token: csrfToken,
            drug_batch: $('#modaldrug_batch').val(),
            manufacture_date: $('#modalmanufacter_date').val(),
            expiry_date: $('#modalexpiry_date').val(),
            quantity: newAvailability,  // Quantity should reflect the updated availability (after add/remove)
            current_availability: newAvailability,
             };

        // Log the form data to check values
        console.log("Form Data:", formData);

        // Send data via AJAX to backend
        $.ajax({
            url: "/PharmacyStock/pharmacyStock/update/" + drugId, 
            method: 'POST',
            data: formData,
            success: function(response) {
                if (typeof response === "string") {
                    try {
                        response = JSON.parse(response); 
                    } catch (e) {
                        console.error("JSON Parsing Error:", e);
                    }
                }

                console.log('Response:', response);

                if (response.result === true) { 
                    showToast("success", "Drug template Updated successfully!");
                    window.location.href = 'https://login-users.hygeiaes.com/pharmacy/pharmacy-stock-list';
                } else {
                    alert('An error occurred while saving the Pharmacy Stock.');
                }
            },
            error: function(xhr, status, error) {
                console.error('An error occurred: ' + error);
                alert('An error occurred while saving pharmacy stock.');
            }
        });
    }
});

$.ajax({
    url: 'https://login-users.hygeiaes.com/corporate/getAllPharmacyDetails',
    method: 'GET',
    success: function(response) {
        console.log('Response:', response); // Debugging: Log the entire response to check its structure

        // Clear any existing options (including the empty ones)
        $('#stores').empty();

        // Add the default option
        $('#stores').append('<option>-Select Store</option>');

        // Check if the response is successful and contains data
        if (response.result === true && Array.isArray(response.data) && response.data.length > 0) {
            const pharmacies = response.data; // The pharmacies array is inside the 'data' field

            // Variable to hold the first pharmacy with main_pharmacy = 1
            let firstMainPharmacy = null;

            // Loop through each pharmacy and append to the dropdown
            pharmacies.forEach(function(pharmacy) {
                console.log('Pharmacy:', pharmacy); // Log each pharmacy to check data

                const option = $('<option></option>')
                    .val(pharmacy.ohc_pharmacy_id) // Set the value of the option to ohc_pharmacy_id
                    .text(pharmacy.pharmacy_name); // Set the text of the option to pharmacy_name

                // Check if it's the first pharmacy with main_pharmacy = 1
                if (pharmacy.main_pharmacy === 1 && !firstMainPharmacy) {
                    firstMainPharmacy = pharmacy.ohc_pharmacy_id; // Store the first main pharmacy id
                    option.prop('selected', true); // Set it as selected
                }

                // Append the option to the select dropdown
                $('#stores').append(option);
            });

            // If no main pharmacy was found, optionally select the first pharmacy in the list
            if (!firstMainPharmacy && pharmacies.length > 0) {
                $('#stores option').first().prop('selected', true); // Select the first pharmacy
            }
        } else {
            console.log('No pharmacy data found or result is not true');
        }
    },
    error: function(xhr, status, error) {
        console.log('Error fetching pharmacy data:', error);
    }
});
flatpickr("#modalmanufacter_date", {
        dateFormat: "d/m/Y", // Set format to DD/MM/YYYY
    });
    
    flatpickr("#modalexpiry_date", {
        dateFormat: "d/m/Y", // Set format to DD/MM/YYYY
    });

});
