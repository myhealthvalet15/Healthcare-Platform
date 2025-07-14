@extends('layouts/layoutMaster')

@section('title', 'Add Drug Template - Forms')

@section('vendor-style')
@vite([
'resources/assets/vendor/libs/bs-stepper/bs-stepper.scss',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection

@section('vendor-script')
@vite([
'resources/assets/vendor/libs/bs-stepper/bs-stepper.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="col-12 mb-6">
    <div id="wizard-validation" class="bs-stepper mt-2">

        <div class="bs-stepper-content">
            <form id="wizard-validation-form" method="post">
                <!-- Select Drug Section -->
                <div class="row g-6">
                    <div class="col-md-4">
                        <label for="drug_template" class="form-label">Select Drug</label>
                        <select id="drug_template" class="form-control select2" required>
                            <option value="">Select a Drug</option>
                            <!-- Add drug options dynamically -->
                        </select>
                    </div>
                </div>

                <!-- Drug Details Section (Initially hidden) -->
                <div id="drug_details_section" style="display: none;">
                    <div class="row g-6">
                        <!-- First Column -->

                        <div class="col-md-4">
                            <label for="manufacturer" class="form-label">Manufacturer</label>
                            <input type="text" id="manufacturer" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="hsn_code" class="form-label">HSN Code</label>
                            <input type="text" id="hsn_code" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="restock_count" class="form-label">Re-stock Count</label>
                            <input type="text" id="restock_count" class="form-control" readonly>
                        </div>

                        <!-- Second Column -->
                        <div class="col-md-4">
                            <label for="id_no" class="form-label">ID No</label>
                            <input type="text" id="id_no" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="bill_status" class="form-label">Bill Status</label>
                            <input type="text" id="bill_status" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="schedule" class="form-label">Schedule</label>
                            <input type="text" id="schedule" class="form-control" readonly>
                        </div>

                        <!-- Third Column -->
                        <div class="col-md-4">
                            <label for="drug_ingredient" class="form-label">Drug Ingredient</label>
                            <input type="text" id="drug_ingredient" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="crd" class="form-label">CRD</label>
                            <input type="text" id="crd" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="mrp" class="form-label">MRP</label>
                            <input type="text" id="mrp" class="form-control" readonly>
                        </div>

                        <!-- First Column -->
                        <div class="col-md-4">
                            <label for="package_unit" class="form-label">Package Unit</label>
                            <input type="text" id="package_unit" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="mrp_per_unit" class="form-label">MRP Per Unit</label>
                            <input type="text" id="mrp_per_unit" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="unit_to_issue" class="form-label">Unit to Issue</label>
                            <input type="text" id="unit_to_issue" class="form-control" readonly>
                        </div>

                        <!-- Second Column -->
                        <div class="col-md-4">
                            <label for="sgst" class="form-label">SGST</label>
                            <input type="text" id="sgst" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="cgst" class="form-label">CGST</label>
                            <input type="text" id="cgst" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="igst" class="form-label">IGST</label>
                            <input type="text" id="igst" class="form-control" readonly>
                        </div>

                        <!-- Third Column -->
                        <div class="col-md-4">
                            <label for="discount" class="form-label">Discount</label>
                            <input type="text" id="discount" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="text" id="quantity" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="manufacture_date" class="form-label">Manufacture Date</label>
                            <input type="text" id="manufacture_date" class="form-control" placeholder="DD/MM/YYYY">
                        
                        </div>
                        <div class="col-md-4">
                            <label for="expiry_date" class="form-label">Expiry Date</label>
                            <input type="text" id="expiry_date" class="form-control" placeholder="DD/MM/YYYY">
                        </div>
                        <div class="col-md-4">
                            <label for="drug_batch" class="form-label">Drug Batch</label>
                            <input type="text" id="drug_batch" class="form-control">
                        </div>
                    </div>
                </div>

                <!-- Buttons for submitting or cancelling -->
                <div class="col-sm-12 mt-4">
                    <input type="hidden" id="drug_name" class="form-control">
                    <input type="hidden" id="drug_type" class="form-control">
                    <input type="hidden" id="drug_strength" class="form-control">
                    <input type="hidden" id="sold_quantity" class="form-control">
                    <input type="hidden" id="amount_per_tab" class="form-control">
                    <input type="hidden" id="ohc" class="form-control">
                    <input type="hidden" id="master_pharmacy_id" class="form-control">
                    <input type="hidden" id="tabletInStrip" class="form-control">
                    <input type="hidden" id="ohc_pharmacy_id" class="form-control">

                    <button type="button" class="btn btn-primary" id="add-pharmacystock">Save Changes</button>

                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include jQuery -->
<style>
    .error {
        border: 2px solid red !important;
        /* Red border for the dropdown */
    }

    .error-message {
        color: red;
        /* Red text for the error message */
        font-size: 14px;
        margin-top: 5px;
        /* Space between dropdown and error message */
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Ensure jQuery is included -->
<script>
    $(document).ready(function() {
        
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        // Fetch drug types and ingredients on page load
        $.ajax({
            url: "{{ route('getDrugTemplateDetails') }}",
            method: 'GET',
            dataType: 'json', // Ensure response is treated as JSON
            success: function(response) {
                console.log("Full Response:", response);

                var drugTypeMapping = {
                    1: "Capsule",
                    2: "Cream",
                    3: "Drops",
                    4: "Foam",
                    5: "Gel",
                    6: "Inhaler",
                    7: "Injection",
                    8: "Lotion",
                    9: "Ointment",
                    10: "Powder",
                    11: "Shampoo",
                    12: "Syringe",
                    13: "Syrup",
                    14: "Tablet",
                    15: "Toothpaste",
                    16: "Suspension",
                    17: "Spray",
                    18: "Test"
                };

                if (response && response.drugTemplate && Array.isArray(response.drugTemplate) && response.drugTemplate.length > 0) {
                    var drugSelect = $('#drug_template');
                    drugSelect.append(new Option('Select Drug Type', '', true, true));

                    response.drugTemplate.forEach(function(drug) {
                        var drugName = drug.drug_name || 'Unknown Drug';
                        var drugStrength = drug.drug_strength || 'Unknown Strength';
                        var drugType = drug.drug_type || 0;
                        var drugTypeName = drugTypeMapping[drugType] || 'Unknown Type';
                        var drugId = drug.drug_template_id;

                        var formattedDrug = `${drugName} - ${drugStrength} (${drugTypeName})`;
                        drugSelect.append(new Option(formattedDrug, drugId));
                    });

                    drugSelect.change(function() {
                        var selectedDrugId = $(this).val();
                        if (!selectedDrugId) {
                            $('#drug_details_section').hide();
                            return;
                        }

                        var selectedDrug = response.drugTemplate.find(function(drug) {
                            return drug.drug_template_id == selectedDrugId;
                        });

                        if (selectedDrug) {
                            $('#drug_details_section').show();
                            var billStatus = selectedDrug.bill_status || 'N/A';

                            $('#bill_status').val(billStatus === '0' ? 'Active' : billStatus === '1' ? 'Inactive' : 'N/A');
                            $('#manufacturer').val(selectedDrug.drug_manufacturer || 'N/A');
                            $('#hsn_code').val(selectedDrug.hsn_code || 'N/A');
                            $('#restock_count').val(selectedDrug.restock_alert_count || 'N/A');
                            $('#id_no').val(selectedDrug.id_no || 'N/A');
                            $('#schedule').val(selectedDrug.schedule || 'N/A');
                            $('#drug_ingredient').val(selectedDrug.ingredient_names || 'N/A');
                            $('#crd').val(selectedDrug.crd || 'N/A');
                            $('#mrp').val(selectedDrug.amount_per_strip || 'N/A');
                            $('#package_unit').val(selectedDrug.tablet_in_strip || 'N/A');
                            $('#mrp_per_unit').val(selectedDrug.amount_per_tab || 'N/A');
                            $('#unit_to_issue').val(selectedDrug.unit_issue || 'N/A');
                            $('#sgst').val(selectedDrug.sgst || 'N/A');
                            $('#cgst').val(selectedDrug.cgst || 'N/A');
                            $('#igst').val(selectedDrug.igst || 'N/A');
                            $('#drug_name').val(selectedDrug.drug_name || 'N/A');
                            $('#drug_type').val(selectedDrug.drug_type || 'N/A');
                            $('#drug_strength').val(selectedDrug.drug_strength || 'N/A');
                            $('#ohc').val(1);
                            $('#master_pharmacy_id').val(1);
                            $('#sold_quantity').val(0);
                            $('#discount').val(selectedDrug.discount || 'N/A');
                            $('#ohc_pharmacy_id').val(selectedDrug.ohc_pharmacy_id || 'N/A');

                        }
                    });
                } else {
                    console.error('No drug types or ingredients found');
                    $('#drug_template').append(new Option('No drug types available', '', true, true));
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching drug details: ' + error);
            }
        });

        // Prevent form submission if drug is not selected
        $('#wizard-validation-form').submit(function(e) {
            var drugTemplateValue = $('#drug_template').val();
            var drugSelect = $('#drug_template');

            if (!drugTemplateValue) {
                e.preventDefault(); // Prevent form submission

                // Apply error styles to the dropdown
                drugSelect.addClass('error'); // Red border

                // Display error message below the dropdown
                if ($('#drug_template_error').length === 0) {
                    // Insert the error message below the select dropdown
                    drugSelect.after('<div id="drug_template_error" class="error-message">Please select a drug first.</div>');
                }
            }
        });

        // Remove error styles and message once the user selects a drug
        $('#drug_template').change(function() {
            var drugSelect = $(this);
            if (drugSelect.val()) {
                drugSelect.removeClass('error'); // Remove red border
                $('#drug_template_error').remove(); // Remove the error message
            }
        });
        $('input').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        });
        $('#drug_template').on('change', function() {
            if ($(this).val().trim() !== '') {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        });
        $('#add-pharmacystock').click(function() {
            var formIsValid = true;
            var drug_template = $('#drug_template');
            if (!drug_template.val() || drug_template.val().trim() === '') {
                formIsValid = false;
                drug_template.addClass('is-invalid');
                if (drug_template.next('.invalid-feedback').length === 0) {
                    drug_template.after('<div class="invalid-feedback">Please select a drug.</div>');
                }
            }
            var quantity = $('#quantity');
            if (quantity.val().trim() === '') {
                formIsValid = false;
                quantity.addClass('is-invalid');
                if (quantity.next('.invalid-feedback').length === 0) {
                    quantity.after('<div class="invalid-feedback">Please Enter Quantity.</div>');
                }
            }

            // Manufacture Date Validation
            var manufacture_date = $('#manufacture_date');
            if (manufacture_date.val().trim() === '') {
                formIsValid = false;
                manufacture_date.addClass('is-invalid');
                if (manufacture_date.next('.invalid-feedback').length === 0) {
                    manufacture_date.after('<div class="invalid-feedback">The Manufacturer Date is required.</div>');
                }
            }

            // Expiry Date Validation
            var expiry_date = $('#expiry_date');
            if (expiry_date.val().trim() === '') {
                formIsValid = false;
                expiry_date.addClass('is-invalid');
                if (expiry_date.next('.invalid-feedback').length === 0) {
                    expiry_date.after('<div class="invalid-feedback">Expiry Date is required.</div>');
                }
            }

            // Drug Batch Validation
            var drug_batch = $('#drug_batch');
            if (drug_batch.val().trim() === '') {
                formIsValid = false;
                drug_batch.addClass('is-invalid');
                if (drug_batch.next('.invalid-feedback').length === 0) {
                    drug_batch.after('<div class="invalid-feedback">The Drug batch is required.</div>');
                }
            }

            if (formIsValid) {
                var formData = {
                    _token: csrfToken,
                    ohc_pharmacy_id: $('#ohc_pharmacy_id').val(),
                    drug_name: $('#drug_name').val(),
                    drug_template_id: $('#drug_template').val(),
                    drug_batch: $('#drug_batch').val(),
                    manufacture_date: $('#manufacture_date').val(),
                    expiry_date: $('#expiry_date').val(),
                    drug_type: $('#drug_type').val(),
                    drug_strength: $('#drug_strength').val(),
                    quantity: $('#quantity').val(),
                    current_availability: $('#quantity').val(),
                    tablet_in_strip: $('#package_unit').val(),
                    amount_per_tab: $('#mrp_per_unit').val(),
                    sold_quantity: $('#sold_quantity').val(),
                    discount: $('#discount').val(),
                    sgst: $('#sgst').val(),
                    cgst: $('#cgst').val(),
                    igst: $('#igst').val(),
                    ohc: $('#ohc').val(),

                };

                // Send data via AJAX to backend
                $.ajax({
                    url: "{{ route('pharmacyStock.store') }}", // The route for storing data
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        console.log("Full Response:", response);
                        console.log("Response Type:", typeof response);

                        if (typeof response === "string") {
                            try {
                                response = JSON.parse(
                                    response); // Parse JSON if it's a string
                            } catch (e) {
                                console.error("JSON Parsing Error:", e);
                            }
                        }

                        console.log('Response:', response); // Log the full response object

                        // Check if response.success is explicitly true or false
                        if (response.result === true) { // Only treat as success if true
                            showToast("success", "Drug template Added successfully!");
                            // Redirect to the specified URL after successful submission
                            window.location.href =
                                'https://login-users.hygeiaes.com/pharmacy/pharmacy-stock-list';
                        } else if (response.result === false) {
                            alert('An error occurred while saving the drug template.');
                        } else {
                            // If for any reason the value of response.success is not true or false, handle that case
                            alert('Unexpected response from the server.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('An error occurred: ' + error);
                        alert('An error occurred while saving the drug template.');
                    }
                });
            }

        });
        flatpickr("#manufacture_date", {
        dateFormat: "d/m/Y", // Set format to DD/MM/YYYY
    });
    
    flatpickr("#expiry_date", {
        dateFormat: "d/m/Y", // Set format to DD/MM/YYYY
    });
    });
   
</script>



@endsection