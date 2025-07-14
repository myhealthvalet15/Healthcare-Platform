@extends('layouts/layoutMaster')

@section('title', 'Add Drug Template - Forms')

<!-- Vendor Styles -->
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.scss', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.js', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

<!-- Page Scripts -->

<!-- Include jQuery from CDN (Content Delivery Network) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


@section('content')
    <!-- Validation Wizard -->
    <div class="col-12 mb-6">

        <div id="wizard-validation" class="bs-stepper mt-2">
           
            <div class="bs-stepper-content">

                <form id="wizard-validation-form" method="post">
                    <!-- Account Details -->
                    <div id="account-details-validation" class="content" style="display:block;">

                        <div class="row g-6">
                            <div class="col-sm-6">
                                <label for="drug_type_name" class="form-label">Drug Type Name</label>
                                <input type="text" id="drug_type_name" name="drug_type_name" class="form-control"
                                    placeholder="Enter Drug Type Name" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="drug_type_manufacturer" class="form-label">Drug Manufacturer</label>
                                <input type="text" id="drug_type_manufacturer" class="form-control"
                                    placeholder="Enter Drug Manufacturer" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="formValidationDrugType">Drug Type</label>
                                <select id="drug_type" class="form-control select2" required>
                                    <!-- Options will be added dynamically -->
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="formValidationDrugIngerdient" required>Drug
                                    Ingredients</label>
                                <select id="drug_ingredients" class="form-control select2" multiple>
                                    <!-- Options will be added dynamically -->
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="formValidationDrugStength" required>Drug Strength</label>
                                <input type="text" id="drug_strength" class="form-control"
                                    placeholder="Enter Drug Strength">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="formValidationRestockalert" required>Restock Alert
                                    Count</label>
                                <input type="text" id="restock_alert_count" class="form-control"
                                    placeholder="Enter Restock Alert Count">
                            </div>
            

                            <div class="col-sm-6">
                                <label for="schedule" class="form-label">Schedule</label>
                                <input type="text" id="schedule" class="form-control" placeholder="Enter Schedule">
                            </div>
                            <div class="col-sm-6">
                                <label for="id_no" class="form-label" required>ID Number</label>
                                <input type="text" id="id_no" class="form-control" placeholder="Enter ID Number">
                            </div>
                            <div class="col-sm-6">
                                <label for="hsn_code" class="form-label" required>HSN Code</label>
                                <input type="text" id="hsn_code" class="form-control" placeholder="Enter HSN Code">
                            </div>
                            <div class="col-sm-6">
                                <label for="unit_issue" class="form-label" required>Unit to Issue</label>
                                <input type="text" id="unit_issue" class="form-control" placeholder="Enter Unit Issue">
                            </div>
                            <div class="col-sm-6">
                                <label for="amount_per_strip" class="form-label" required>Amount Per Strip</label>
                                <input type="text" id="amount_per_strip" class="form-control"
                                    placeholder="Enter Per Strip">
                            </div>
                            <div class="col-sm-6">
                                <label for="tablet_in_strip" class="form-label" required>Tablet in Strip</label>
                                <input type="text" id="tablet_in_strip" class="form-control"
                                    placeholder="Enter Tablet in Strip">
                            </div>
                            <div class="col-sm-6">
                                <label for="amount_per_tab" class="form-label" required>Amount per Tab</label>
                                <input type="text" id="amount_per_tab" class="form-control"
                                    placeholder="Enter Amount per Tab">
                            </div>
                            <div class="col-sm-6">
                                <label for="discount" class="form-label" required>Discount</label>
                                <input type="text" id="discount" class="form-control" placeholder="Enter Discount">
                            </div>
                            <div class="col-sm-6">
                                <label for="sgst" class="form-label" required>SGST</label>
                                <input type="text" id="sgst" class="form-control" placeholder="Enter SGST"
                                    required>
                            </div>
                            <div class="col-sm-6">
                                <label for="cgst" class="form-label" required>CGST</label>
                                <input type="text" id="cgst" class="form-control" placeholder="Enter CGST"
                                    required>
                            </div>
                            <div class="col-sm-6">
                                <label for="igst" class="form-label">IGST</label>
                                <input type="text" id="igst" class="form-control" placeholder="Enter IGST"
                                    required>
                            </div>
                            
                            <div class="col-sm-6">
                                <label for="bill_status" class="form-label">Bill Status</label>
                                <label class="switch">
                                    <input type="checkbox" class="switch-input" id="bill_status">
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"></span>
                                        <span class="switch-off"></span>
                                    </span>
                                    <span class="switch-label" id="bill-status-label">Inactive</span>
                                </label>
                            </div>
                            <div class="col-sm-6">
   
    <div class="form-check">
        <input type="checkbox" id="otc" name="otc" class="form-check-input">
        <label class="form-check-label" for="otc">OTC</label>
        &nbsp; &nbsp;
        <input type="checkbox" id="crd" name="crd">
        <label class="form-check-label" for="crd">CRD</label>
    </div>

    </div>
    



                            <div class="col-sm-6">
                                <button type="button" class="btn btn-primary" id="add-drugtype">Save Changes</button>
                                <button type="reset" class="btn btn-label-danger waves-effect"
                                    data-bs-dismiss="offcanvas">Cancel</button>

                            </div>

                        </div>
                        <br /><br />
                    </div>

                </form>
            </div>
        </div>
    </div>
    <!-- /Validation Wizard -->
    </div>
    <script>
    $(document).ready(function() {
        // Output the URL to check if it's correct
        console.log("Route URL: {{ route('drugTypesAndIngredients') }}");

        // CSRF Token
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        // Fetch drug types and ingredients when the page loads
        $.ajax({
            url: "{{ route('drugTypesAndIngredients') }}", // The route you defined
            method: 'GET',
            success: function(response) {
                console.log(response); // Log the response to inspect
                if (response.drugTypes && response.drugIngredients) {
                    // Populate the drug types select box
                    var drugTypeSelect = $('#drug_type');
                    drugTypeSelect.append(new Option('Select Drug Type', '', true, true));
                    response.drugTypes.forEach(function(drugType) {
                        drugTypeSelect.append(new Option(drugType.drug_type_name, drugType
                            .id)); // Correct field name
                    });

                    // Populate the drug ingredients select box
                    var drugIngredientsSelect = $('#drug_ingredients');
                    response.drugIngredients.forEach(function(ingredient) {
                        drugIngredientsSelect.append(new Option(ingredient.drug_ingredients,
                            ingredient.id)); // Correct field name
                    });
                } else {
                    console.error('No drug types or ingredients found');
                }
            },
            error: function(xhr, status, error) {
                console.error('An error occurred: ' + error);
            }
        });

        // Handle bill status change
        $('#bill_status').change(function() {
            if ($(this).prop('checked')) {
                $('#bill-status-label').text('Active');
            } else {
                $('#bill-status-label').text('Inactive');
            }
        });

        // Handle Add Drug Type button click
        $('#add-drugtype').click(function() {
            var drugIngredients = $('#drug_ingredients').val();

            console.log('drug_ingredients:', drugIngredients); // Log the value
            console.log('Type of drug_ingredients:', typeof drugIngredients); // Log the type

            // Since drug_ingredients is an array from the multiple select, you don't need to split it.
            if (Array.isArray(drugIngredients)) {
                // It’s already an array, no need to split.
                console.log('drug_ingredients is an array:', drugIngredients);
            } else {
                // If it's not an array for any reason, fallback to an empty array.
                drugIngredients = [];
            }   
            var formIsValid = true;
            var crdChecked = $('#crd').is(':checked');
    var otcChecked = $('#otc').is(':checked');
    
    if (crdChecked && otcChecked) {
        formIsValid = false;
        $('#crd').addClass('is-invalid');
        $('#otc').addClass('is-invalid');
        $('#crd').after('<div class="invalid-feedback">You cannot select both CRD and OTC at the same time.</div>');
        $('#otc').after('<div class="invalid-feedback">You cannot select both CRD and OTC at the same time.</div>');
    } else if (!crdChecked && !otcChecked) {
        formIsValid = false;
        $('#crd').addClass('is-invalid');
        $('#otc').addClass('is-invalid');
        $('#crd').after('<div class="invalid-feedback">Please select either CRD or OTC.</div>');
        $('#otc').after('<div class="invalid-feedback">Please select either CRD or OTC.</div>');
    } else {
        // Remove any previous invalid feedback if either one is selected
        $('#crd').removeClass('is-invalid');
        $('#otc').removeClass('is-invalid');
        $('#crd').next('.invalid-feedback').remove();
        $('#otc').next('.invalid-feedback').remove();
    }
            var drugName = $('#drug_type_name').val();
            if (drugName.trim() === '') {
                formIsValid = false;
                $('#drug_type_name').addClass('is-invalid');
                $('#drug_type_name').after(
                    '<div class="invalid-feedback">The Drug Type Name is required.</div>');
            }

            // Drug Manufacturer Validation
            var drugManufacturer = $('#drug_type_manufacturer').val();
            if (drugManufacturer.trim() === '') {
                formIsValid = false;
                $('#drug_type_manufacturer').addClass('is-invalid');
                $('#drug_type_manufacturer').after(
                    '<div class="invalid-feedback">The Drug Manufacturer is required.</div>');
            }

            // Drug Type Validation
            var drugType = $('#drug_type').val();
            if (drugType === '') {
                formIsValid = false;
                $('#drug_type').addClass('is-invalid');
                $('#drug_type').after('<div class="invalid-feedback">The Drug Type is required.</div>');
            }

            // Drug Ingredients Validation
            var drugIngredients = $('#drug_ingredients').val();
            if (drugIngredients === null || drugIngredients.length === 0) {
                formIsValid = false;
                $('#drug_ingredients').addClass('is-invalid');
                $('#drug_ingredients').after(
                    '<div class="invalid-feedback">At least one drug ingredient must be selected.</div>'
                    );
            }

            // Drug Strength Validation
            var drugStrength = $('#drug_strength').val();
            if (drugStrength.trim() === '') {
                formIsValid = false;
                $('#drug_strength').addClass('is-invalid');
                $('#drug_strength').after(
                    '<div class="invalid-feedback">The Drug Strength is required.</div>');
            }

            // Restock Alert Count Validation
            var restockAlertCount = $('#restock_alert_count').val();
            if (restockAlertCount.trim() === '' || isNaN(restockAlertCount)) {
                formIsValid = false;
                $('#restock_alert_count').addClass('is-invalid');
                $('#restock_alert_count').after(
                    '<div class="invalid-feedback">The Restock Alert Count is required and must be a number.</div>'
                    );
            }

            // CRD Validation
            var crd = $('#crd').val();
            if (crd.trim() === '') {
                formIsValid = false;
                $('#crd').addClass('is-invalid');
                $('#crd').after('<div class="invalid-feedback">The CRD is required.</div>');
            }

            // Schedule Validation
            var schedule = $('#schedule').val();
            if (schedule.trim() === '') {
                formIsValid = false;
                $('#schedule').addClass('is-invalid');
                $('#schedule').after('<div class="invalid-feedback">The Schedule is required.</div>');
            }

            // ID Number Validation
            var idNo = $('#id_no').val();
            if (idNo.trim() === '') {
                formIsValid = false;
                $('#id_no').addClass('is-invalid');
                $('#id_no').after('<div class="invalid-feedback">The ID Number is required.</div>');
            }

            // HSN Code Validation
            var hsnCode = $('#hsn_code').val();
            if (hsnCode.trim() === '') {
                formIsValid = false;
                $('#hsn_code').addClass('is-invalid');
                $('#hsn_code').after('<div class="invalid-feedback">The HSN Code is required.</div>');
            }

            // Unit to Issue Validation
            var unitIssue = $('#unit_issue').val();
            if (unitIssue.trim() === '' || isNaN(unitIssue)) {
                formIsValid = false;
                $('#unit_issue').addClass('is-invalid');
                $('#unit_issue').after(
                    '<div class="invalid-feedback">The Unit to Issue is required and must be a number.</div>'
                    );
            }

            // Amount Per Strip Validation
            var amountPerStrip = $('#amount_per_strip').val();
            if (amountPerStrip.trim() === '' || isNaN(amountPerStrip)) {
                formIsValid = false;
                $('#amount_per_strip').addClass('is-invalid');
                $('#amount_per_strip').after(
                    '<div class="invalid-feedback">The Amount Per Strip is required and must be a number.</div>'
                    );
            }

            // Tablet in Strip Validation
            var tabletInStrip = $('#tablet_in_strip').val();
            if (tabletInStrip.trim() === '' || isNaN(tabletInStrip)) {
                formIsValid = false;
                $('#tablet_in_strip').addClass('is-invalid');
                $('#tablet_in_strip').after(
                    '<div class="invalid-feedback">The Tablet in Strip is required and must be a number.</div>'
                    );
            }

            // Amount Per Tab Validation
            var amountPerTab = $('#amount_per_tab').val();
            if (amountPerTab.trim() === '' || isNaN(amountPerTab)) {
                formIsValid = false;
                $('#amount_per_tab').addClass('is-invalid');
                $('#amount_per_tab').after(
                    '<div class="invalid-feedback">The Amount per Tab is required and must be a number.</div>'
                    );
            }

            // Discount Validation
            var discount = $('#discount').val();
            if (discount.trim() === '' || isNaN(discount)) {
                formIsValid = false;
                $('#discount').addClass('is-invalid');
                $('#discount').after(
                    '<div class="invalid-feedback">The Discount is required and must be a number.</div>'
                    );
            }

            // SGST Validation
            var sgst = $('#sgst').val();
            if (sgst.trim() === '' || isNaN(sgst)) {
                formIsValid = false;
                $('#sgst').addClass('is-invalid');
                $('#sgst').after(
                    '<div class="invalid-feedback">The SGST is required and must be a number.</div>'
                    );
            }

            // CGST Validation
            var cgst = $('#cgst').val();
            if (cgst.trim() === '' || isNaN(cgst)) {
                formIsValid = false;
                $('#cgst').addClass('is-invalid');
                $('#cgst').after(
                    '<div class="invalid-feedback">The CGST is required and must be a number.</div>'
                    );
            }

            // IGST Validation
            var igst = $('#igst').val();
            if (igst.trim() !== '' && isNaN(igst)) {
                formIsValid = false;
                $('#igst').addClass('is-invalid');
                $('#igst').after('<div class="invalid-feedback">The IGST must be a number.</div>');
            }

            if (formIsValid) {
                var tabletInStrip = parseFloat($('#tablet_in_strip').val());
                var amountPerStrip = parseFloat($('#amount_per_strip').val());

                // Calculate amount_per_tab only if both values are valid
                var amountPerTab = '';
                if (!isNaN(tabletInStrip) && !isNaN(amountPerStrip) && amountPerStrip !== 0) {
                    amountPerTab = (tabletInStrip / amountPerStrip).toFixed(
                    2); // Calculate and limit to 2 decimal places
                } else {
                    amountPerTab = ''; // Reset if the values are invalid
                }
                var otcChecked = $('#otc').is(':checked') ? 1 : 0;
                var crdChecked = $('#crd').is(':checked') ? 1 : 0;
                
                var formData = {
                    _token: csrfToken,
                    drug_name: $('#drug_type_name').val(),
                    drug_manufacturer: $('#drug_type_manufacturer').val(),
                    drug_type: $('#drug_type').val(),
                    drug_ingredients: drugIngredients,
                    drug_strength: $('#drug_strength').val(),
                    restock_alert_count: $('#restock_alert_count').val(),
                    crd: $('#crd').val(),
                    schedule: $('#schedule').val(),
                    id_no: $('#id_no').val(),
                    hsn_code: $('#hsn_code').val(),
                    unit_issue: $('#unit_issue').val(),
                    amount_per_strip: amountPerStrip,
                    tablet_in_strip: tabletInStrip,
                    amount_per_tab: amountPerTab,
                    discount: $('#discount').val(),
                    sgst: $('#sgst').val(),
                    cgst: $('#cgst').val(),
                    igst: $('#igst').val(),
                    bill_status: $('#bill_status').is(':checked') ? 1 :0,
                    otc: otcChecked,
                    crd: crdChecked
                };

                // Send data via AJAX to backend
                $.ajax({
                    url: "{{ route('drugTemplate.store') }}", // The route for storing data
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
                        if (response.success === true) { // Only treat as success if true
                            showToast("success", "Drug template Added successfully!");
                            // Redirect to the specified URL after successful submission
                            window.location.href =
                                'https://login-users.hygeiaes.com/drugs/drug-template-list';
                        } else if (response.success === false) {
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

        // Select2 Initialization
        const select2 = $('.select2');
        if (select2.length) {
            select2.each(function() {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>');
                $this.select2({
                    placeholder: 'Select an option',
                    dropdownParent: $this.parent()
                });
            });
        }

        

        $('#drug_type_name').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

        $('#drug_type_manufacturer').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

        $('#drug_type').on('change', function() {
            if ($(this).val() !== '') {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove(); // Remove error message
            }
        });

        $('#drug_ingredients').on('change', function() {
            if ($(this).val() !== '') {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove(); // Remove error message
            }
        });

        $('#drug_strength').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

        $('#restock_alert_count').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

        $('#crd').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

        $('#schedule').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

        $('#id_no').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

        $('#hsn_code').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

        $('#unit_issue').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

        $('#amount_per_strip').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

        $('#tablet_in_strip').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

        $('#amount_per_tab').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

        $('#discount').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

        $('#sgst').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

        $('#cgst').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

        $('#igst').on('focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove(); // Remove error message
        });

    });
</script>

@endsection
<meta name="csrf-token" content="{{ csrf_token() }}">
