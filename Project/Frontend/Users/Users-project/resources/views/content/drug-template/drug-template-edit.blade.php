@extends('layouts/layoutMaster')

@section('title', 'Edit Drug Template - Forms')

<!-- Vendor Styles -->
@section('vendor-style')
@vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.scss', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.js', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

<!-- Page Scripts -->
@section('page-script')
@vite(['resources/assets/js/form-wizard-numbered.js', 'resources/assets/js/form-wizard-validation.js'])
@endsection
<!-- Include jQuery from CDN (Content Delivery Network) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



@section('content')
<!-- Validation Wizard -->
<div class="col-12 mb-6">

    <div id="wizard-validation" class="bs-stepper mt-2">
        
        <div class="bs-stepper-content">



            <form id="wizard-validation-form">
                <!-- Account Details -->
                <div id="account-details-validation" class="content" style="display:block;">

                    <div class="row g-6">
                        <div class="col-sm-6">
                            <label for="drug_type_name" class="form-label">Drug Type Name</label>
                            <input type="text" id="drug_type_name" class="form-control"
                                placeholder="Enter Drug Type Name">
                        </div>
                        <div class="col-sm-6">
                            <label for="drug_type_manufacturer" class="form-label">Drug Manufacturer</label>
                            <input type="text" id="drug_type_manufacturer" class="form-control"
                                placeholder="Enter Drug Manufacturer">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="formValidationDrugType">Drug Type</label>
                            <select id="drug_type" class="form-control select2">
                                <!-- Options will be added dynamically -->
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="formValidationDrugIngerdient">Drug Ingredients</label>
                            <select id="drug_ingredients" class="form-control select2" multiple>
                                <!-- Options will be added dynamically -->
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="formValidationDrugStength">Drug Strength</label>
                            <input type="text" id="drug_strength" class="form-control"
                                placeholder="Enter Drug Strength">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="formValidationRestockalert">Restock Alert Count</label>
                            <input type="text" id="restock_alert_count" class="form-control"
                                placeholder="Enter Restock Alert Count">
                        </div>
                        

                        <div class="col-sm-6">
                            <label for="schedule" class="form-label">Schedule</label>
                            <input type="text" id="schedule" class="form-control" placeholder="Enter Schedule">
                        </div>
                        <div class="col-sm-6">
                            <label for="id_no" class="form-label">ID Number</label>
                            <input type="text" id="id_no" class="form-control" placeholder="Enter ID Number">
                        </div>
                        <div class="col-sm-6">
                            <label for="hsn_code" class="form-label">HSN Code</label>
                            <input type="text" id="hsn_code" class="form-control" placeholder="Enter HSN Code">
                        </div>
                        <div class="col-sm-6">
                            <label for="unit_issue" class="form-label">Unit to Issue</label>
                            <input type="text" id="unit_issue" class="form-control" placeholder="Enter Unit Issue">
                        </div>
                        <div class="col-sm-6">
                            <label for="amount_per_strip" class="form-label">Amount Per Strip</label>
                            <input type="text" id="amount_per_strip" class="form-control"
                                placeholder="Enter Per Strip">
                        </div>
                        <div class="col-sm-6">
                            <label for="tablet_in_strip" class="form-label">Tablet in Strip</label>
                            <input type="text" id="tablet_in_strip" class="form-control"
                                placeholder="Enter Tablet in Strip">
                        </div>
                        <div class="col-sm-6">
                            <label for="amount_per_tab" class="form-label">Amount per Tab</label>
                            <input type="text" id="amount_per_tab" class="form-control"
                                placeholder="Enter Amount per Tab">
                        </div>
                        <div class="col-sm-6">
                            <label for="discount" class="form-label">Discount</label>
                            <input type="text" id="discount" class="form-control" placeholder="Enter Discount">
                        </div>
                        <div class="col-sm-6">
                            <label for="sgst" class="form-label">SGST</label>
                            <input type="text" id="sgst" class="form-control" placeholder="Enter SGST">
                        </div>
                        <div class="col-sm-6">
                            <label for="cgst" class="form-label">CGST</label>
                            <input type="text" id="cgst" class="form-control" placeholder="Enter CGST">
                        </div>
                        <div class="col-sm-6">
                            <label for="igst" class="form-label">IGST</label>
                            <input type="text" id="igst" class="form-control" placeholder="Enter IGST">
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
                            <button type="button" class="btn btn-primary" id="edit-drugtype">Save Changes</button>

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
        $('#bill_status').change(function() {
            if ($(this).is(':checked')) {
                $('#bill_status_label').text('Active');
            } else {
                $('#bill_status_label').text('Inactive');
            }

        });

        apiRequest({
            url: "{{ route('drugTypesAndIngredients') }}",
            method: 'GET',
            onSuccess: function(response) {
                if (response.drugTypes && response.drugIngredients) {
                    var drugTypeSelect = $('#drug_type');
                    drugTypeSelect.empty();
                    response.drugTypes.forEach(function(drugType) {
                        drugTypeSelect.append(new Option(drugType.drug_type_name, drugType.id));
                    });

                    var drugIngredientsSelect = $('#drug_ingredients');
                    drugIngredientsSelect.empty();
                    response.drugIngredients.forEach(function(ingredient) {
                        drugIngredientsSelect.append(new Option(ingredient.drug_ingredients, ingredient.id));
                    });

                    var selectedDrugIngredientIds = "{{ $drugtemplates['drug_ingredient'] ?? '' }}";
                    if (selectedDrugIngredientIds) {
                        var selectedIds = selectedDrugIngredientIds.split(',');
                        $('#drug_ingredients').val(selectedIds).trigger('change');
                    }
                } else {
                    console.error('No drug types or ingredients found');
                }
            },
            onError: function(error) {
                console.error('An error occurred:', error);
            }
        });



        $('#drug_type').select2({
            placeholder: 'Select Drug Type'

        });
        $('#drug_ingredients').select2({
            placeholder: 'Select Ingredients', // Optional placeholder
            allowClear: true // Optional clear button
        });


        // Prefill the Select2 dropdown with the stored value
        var selectedDrugTypeId = "{{ $drugtemplates['drug_type'] ?? '' }}";
        //console.log(selectedDrugTypeId);
        if (selectedDrugTypeId) {
            $('#drug_type').val(selectedDrugTypeId).trigger('change');

        }

        $('#drug_type_name').val("{{ $drugtemplates['drug_name'] ?? '' }}");

        $('#drug_type_manufacturer').val("{{ $drugtemplates['drug_manufacturer'] ?? '' }}");
        $('#drug_strength').val("{{ $drugtemplates['drug_strength'] ?? '' }}");
        $('#restock_alert_count').val("{{ $drugtemplates['restock_alert_count'] ?? '' }}");
        //$('#crd').val("{{ $drugtemplates['crd'] ?? '' }}");
        $('#schedule').val("{{ $drugtemplates['schedule'] ?? '' }}");
        $('#id_no').val("{{ $drugtemplates['id_no'] ?? '' }}");
        $('#hsn_code').val("{{ $drugtemplates['hsn_code'] ?? '' }}");
        $('#unit_issue').val("{{ $drugtemplates['unit_issue'] ?? '' }}");
        $('#amount_per_strip').val("{{ $drugtemplates['amount_per_strip'] ?? '' }}");
        $('#tablet_in_strip').val("{{ $drugtemplates['tablet_in_strip'] ?? '' }}");
        $('#amount_per_tab').val("{{ $drugtemplates['amount_per_tab'] ?? '' }}");
        $('#discount').val("{{ $drugtemplates['discount'] ?? '' }}");
        $('#sgst').val("{{ $drugtemplates['sgst'] ?? '' }}");
        $('#cgst').val("{{ $drugtemplates['cgst'] ?? '' }}");
        $('#igst').val("{{ $drugtemplates['igst'] ?? '' }}");

        // Bill status checkbox

    });

    $(document).ready(function() {
        @if(isset($drugtemplates['bill_status']) && $drugtemplates['bill_status'] == 1)
        $('#bill_status').prop('checked', true);
        $('#bill-status-label').text('Active');
        @else
        $('#bill-status-label').text('Inactive');
        @endif
        $('#bill_status').change(function() {
            if ($(this).is(':checked')) {
                $('#bill-status-label').text('Active');
            } else {
                $('#bill-status-label').text('Inactive');
            }
        });
        @if(isset($drugtemplates['otc']) && $drugtemplates['otc'] == 1)
        $('#otc').prop('checked', true);
        @endif
        @if(isset($drugtemplates['crd']) && $drugtemplates['crd'] == 1)
        $('#crd').prop('checked', true);
        @endif


    });

    $(document).on('click', '#edit-drugtype', function() {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        //console.log('Am Here');
        var otcChecked = $('#otc').is(':checked') ? 1 : 0; 
        var crdChecked = $('#crd').is(':checked') ? 1 : 0; if (crdChecked && otcChecked) {
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
   


        var formData = {
            _token: csrfToken,
            drug_name: $('#drug_type_name').val(),
            drug_manufacturer: $('#drug_type_manufacturer').val(),
            drug_type: $('#drug_type').val(),
            drug_ingredients: $('#drug_ingredients').val(),
            drug_strength: $('#drug_strength').val(),
            restock_alert_count: $('#restock_alert_count').val(),
            //crd: $('#crd').val(),
            schedule: $('#schedule').val(),
            id_no: $('#id_no').val(),
            hsn_code: $('#hsn_code').val(),
            unit_issue: $('#unit_issue').val(),
            amount_per_strip: $('#amount_per_strip').val(),
            tablet_in_strip: $('#tablet_in_strip').val(),
            amount_per_tab: $('#amount_per_tab').val(),
            discount: $('#discount').val(),
            sgst: $('#sgst').val(),
            cgst: $('#cgst').val(),
            igst: $('#igst').val(),
            bill_status: $('#bill_status').is(':checked') ? 1 : 0,
            otc: otcChecked,
            crd: crdChecked,
            
            // Convert to 1 or 0 based on checkbox status
        };
        var currentUrl = window.location.href;
        var templateId = currentUrl.substring(currentUrl.lastIndexOf('/') + 1);
        // console.log(templateId);

        // Send data via AJAX to backend
        $.ajax({

            url: "/DrugTemplate/drug-template/update/" + templateId, // The route for updating data
            method: 'POST',
            data: formData,
            success: function(response) {
                console.log(response); // Log the response
                if (response.success === true) { // Only treat as success if true
                    showToast("success", "Drug template updated successfully!");
                    // Redirect to the specified URL after successful submission
                    window.location.href =
                        'https://login-users.hygeiaes.com/drugs/drug-template-list';

                } else if (response.success === false) {
                    alert('Error.');
                } else {
                    // If for any reason the value of response.success is not true or false, handle that case
                    alert('Unexpected response from the server.');
                }
            },
            error: function(xhr, status, error) {
                console.error('An error occurred: ' + error);
                alert('An error occurred while updating the drug template.');
            }
        });
    });
</script>

@endsection
<meta name="csrf-token" content="{{ csrf_token() }}">