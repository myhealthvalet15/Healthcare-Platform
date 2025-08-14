$(document).ready(function () {
    const data = window.drugTemplateData || {};

    // Initialize Select2
    $('#drug_type').select2({ placeholder: 'Select Drug Type' });
    $('#drug_ingredients').select2({ placeholder: 'Select Ingredients', allowClear: true });

    // Prefill static fields
    $('#drug_type_name').val(data.drug_name || '');
    $('#drug_type_manufacturer').val(data.drug_manufacturer || '');
    $('#drug_strength').val(data.drug_strength || '');
    $('#restock_alert_count').val(data.restock_alert_count || '');
    $('#schedule').val(data.schedule || '');
    $('#id_no').val(data.id_no || '');
    $('#hsn_code').val(data.hsn_code || '');
    $('#unit_issue').val(data.unit_issue || '');
    $('#amount_per_strip').val(data.amount_per_strip || '');
    $('#tablet_in_strip').val(data.tablet_in_strip || '');
    $('#amount_per_tab').val(data.amount_per_tab || '');
    $('#discount').val(data.discount || '');
    $('#sgst').val(data.sgst || '');
    $('#cgst').val(data.cgst || '');
    $('#igst').val(data.igst || '');

    // Bill status toggle
    if (data.bill_status == 1) {
        $('#bill_status').prop('checked', true);
        $('#bill-status-label').text('Active');
    } else {
        $('#bill-status-label').text('Inactive');
    }

    $('#bill_status').change(function () {
        $('#bill-status-label').text(this.checked ? 'Active' : 'Inactive');
    });

    // OTC and CRD checkboxes
    $('#otc').prop('checked', data.otc == 1);
    $('#crd').prop('checked', data.crd == 1);

    // Fetch and populate select options
    apiRequest({
        url: "/DrugTemplate/getDrugTypesAndIngredients",
        method: 'GET',
        onSuccess: function (response) {
            if (response.drugTypes && response.drugIngredients) {
                // Drug Types
                const drugTypeSelect = $('#drug_type');
                drugTypeSelect.empty().append('<option></option>');
                response.drugTypes.forEach(function (drugType) {
                    drugTypeSelect.append(new Option(drugType.drug_type_name, drugType.id));
                });

                // Ingredients
                const drugIngredientsSelect = $('#drug_ingredients');
                drugIngredientsSelect.empty();
                response.drugIngredients.forEach(function (ingredient) {
                    drugIngredientsSelect.append(new Option(ingredient.drug_ingredients, ingredient.id));
                });

                // Prefill selected
                if (data.drug_type) {
                    $('#drug_type').val(data.drug_type).trigger('change');
                }

                if (data.drug_ingredient) {
                    const selected = data.drug_ingredient.split(',');
                    $('#drug_ingredients').val(selected).trigger('change');
                }
            }
        },
        onError: function (error) {
            console.error('Error fetching options:', error);
        }
    });

    // Submit logic
    $('#edit-drugtype').on('click', function () {
        $('.invalid-feedback').remove();
        $('.is-invalid').removeClass('is-invalid');

        let formIsValid = true;
        const otc = $('#otc').is(':checked') ? 1 : 0;
        const crd = $('#crd').is(':checked') ? 1 : 0;

        if (otc && crd) {
            formIsValid = false;
            ['#crd', '#otc'].forEach(selector => {
                $(selector).addClass('is-invalid').after(`<div class="invalid-feedback">You cannot select both CRD and OTC at the same time.</div>`);
            });
        } else if (!otc && !crd) {
            formIsValid = false;
            ['#crd', '#otc'].forEach(selector => {
                $(selector).addClass('is-invalid').after(`<div class="invalid-feedback">Please select either CRD or OTC.</div>`);
            });
        }

        if (!formIsValid) return;

        const formData = {
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
            otc,
            crd
        };

        const templateId = window.location.pathname.split('/').pop();

        apiRequest({
            url: `/DrugTemplate/drug-template/update/${templateId}`,
            method: 'POST',
            data: formData,
            onSuccess: function (response) {
                if (response.success) {
                    showToast('success', 'Drug template updated successfully!');
                    window.location.href = 'https://login-users.hygeiaes.com/drugs/drug-template-list';
                } else {
                    alert('Update failed. Please check your data.');
                }
            },
            onError: function (err) {
                console.error('Update failed:', err);
                alert('An error occurred while updating the drug template.');
            }
        });
    });
});
