$(document).ready(function () {
    $('#bill_status').change(function () {
        if ($(this).is(':checked')) {
            $('#bill_status_label').text('Active');
        } else {
            $('#bill_status_label').text('Inactive');
        }
    });
    apiRequest({
        url: "{{ route('drugTypesAndIngredients') }}",
        method: 'GET',
        onSuccess: function (response) {
            if (response.drugTypes && response.drugIngredients) {
                var drugTypeSelect = $('#drug_type');
                drugTypeSelect.empty();
                response.drugTypes.forEach(function (drugType) {
                    drugTypeSelect.append(new Option(drugType.drug_type_name, drugType.id));
                });
                var drugIngredientsSelect = $('#drug_ingredients');
                drugIngredientsSelect.empty();
                response.drugIngredients.forEach(function (ingredient) {
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
        onError: function (error) {
            console.error('An error occurred:', error);
        }
    });
    $('#drug_type').select2({
        placeholder: 'Select Drug Type'
    });
    $('#drug_ingredients').select2({
        placeholder: 'Select Ingredients',
        allowClear: true
    });
    var selectedDrugTypeId = "{{ $drugtemplates['drug_type'] ?? '' }}";
    if (selectedDrugTypeId) {
        $('#drug_type').val(selectedDrugTypeId).trigger('change');
    }
    $('#drug_type_name').val("{{ $drugtemplates['drug_name'] ?? '' }}");
    $('#drug_type_manufacturer').val("{{ $drugtemplates['drug_manufacturer'] ?? '' }}");
    $('#drug_strength').val("{{ $drugtemplates['drug_strength'] ?? '' }}");
    $('#restock_alert_count').val("{{ $drugtemplates['restock_alert_count'] ?? '' }}");
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
});
$(document).on('click', '#edit-drugtype', function () {
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
        $('#crd').removeClass('is-invalid');
        $('#otc').removeClass('is-invalid');
        $('#crd').next('.invalid-feedback').remove();
        $('#otc').next('.invalid-feedback').remove();
    }
    var formData = {
        drug_name: $('#drug_type_name').val(),
        drug_manufacturer: $('#drug_type_manufacturer').val(),
        drug_type: $('#drug_type').val(),
        drug_ingredients: $('#drug_ingredients').val(),
        drug_strength: $('#drug_strength').val(),
        restock_alert_count: $('#restock_alert_count').val(),
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
    };
    var currentUrl = window.location.href;
    var templateId = currentUrl.substring(currentUrl.lastIndexOf('/') + 1);
    apiRequest({
        url: `/DrugTemplate/drug-template/update/${templateId}`,
        method: "POST",
        data: formData,
        onSuccess: function (response) {
            console.log(response);

            if (response.success === true) {
                showToast("success", "Drug template updated successfully!");
                window.location.href = 'https://login-users.hygeiaes.com/drugs/drug-template-list';
            } else if (response.success === false) {
                alert('Error.');
            } else {
                alert('Unexpected response from the server.');
            }
        },
        onError: function (errorMessage) {
            console.error('An error occurred:', errorMessage);
            alert('An error occurred while updating the drug template.');
        }
    });
});
