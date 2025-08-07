$(document).ready(function () {
    apiRequest({
        url: "/DrugTemplate/getDrugTypesAndIngredients",
        method: "GET",
        onSuccess: function (response) {
            console.log(response);

            if (response.drugTypes && response.drugIngredients) {
                const drugTypeSelect = $('#drug_type');
                drugTypeSelect.append(new Option('Select Drug Type', '', true, true));

                response.drugTypes.forEach(function (drugType) {
                    drugTypeSelect.append(new Option(drugType.drug_type_name, drugType.id));
                });

                const drugIngredientsSelect = $('#drug_ingredients');
                response.drugIngredients.forEach(function (ingredient) {
                    drugIngredientsSelect.append(
                        new Option(ingredient.drug_ingredients, ingredient.id)
                    );
                });
            } else {
                console.error('No drug types or ingredients found');
            }
        },
        onError: function (errorMessage) {
            console.error('An error occurred:', errorMessage);
        }
    });

    $('#bill_status').change(function () {
        if ($(this).prop('checked')) {
            $('#bill-status-label').text('Active');
        } else {
            $('#bill-status-label').text('Inactive');
        }
    });
    $('#add-drugtype').click(function () {
        var drugIngredients = $('#drug_ingredients').val();
        console.log('drug_ingredients:', drugIngredients);
        console.log('Type of drug_ingredients:', typeof drugIngredients);
        if (Array.isArray(drugIngredients)) {
            console.log('drug_ingredients is an array:', drugIngredients);
        } else {
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
        var drugManufacturer = $('#drug_type_manufacturer').val();
        if (drugManufacturer.trim() === '') {
            formIsValid = false;
            $('#drug_type_manufacturer').addClass('is-invalid');
            $('#drug_type_manufacturer').after(
                '<div class="invalid-feedback">The Drug Manufacturer is required.</div>');
        }
        var drugType = $('#drug_type').val();
        if (drugType === '') {
            formIsValid = false;
            $('#drug_type').addClass('is-invalid');
            $('#drug_type').after('<div class="invalid-feedback">The Drug Type is required.</div>');
        }
        var drugIngredients = $('#drug_ingredients').val();
        if (drugIngredients === null || drugIngredients.length === 0) {
            formIsValid = false;
            $('#drug_ingredients').addClass('is-invalid');
            $('#drug_ingredients').after(
                '<div class="invalid-feedback">At least one drug ingredient must be selected.</div>'
            );
        }
        var drugStrength = $('#drug_strength').val();
        if (drugStrength.trim() === '') {
            formIsValid = false;
            $('#drug_strength').addClass('is-invalid');
            $('#drug_strength').after(
                '<div class="invalid-feedback">The Drug Strength is required.</div>');
        }
        var restockAlertCount = $('#restock_alert_count').val();
        if (restockAlertCount.trim() === '' || isNaN(restockAlertCount)) {
            formIsValid = false;
            $('#restock_alert_count').addClass('is-invalid');
            $('#restock_alert_count').after(
                '<div class="invalid-feedback">The Restock Alert Count is required and must be a number.</div>'
            );
        }
        var crd = $('#crd').val();
        if (crd.trim() === '') {
            formIsValid = false;
            $('#crd').addClass('is-invalid');
            $('#crd').after('<div class="invalid-feedback">The CRD is required.</div>');
        }
        var schedule = $('#schedule').val();
        if (schedule.trim() === '') {
            formIsValid = false;
            $('#schedule').addClass('is-invalid');
            $('#schedule').after('<div class="invalid-feedback">The Schedule is required.</div>');
        }
        var idNo = $('#id_no').val();
        if (idNo.trim() === '') {
            formIsValid = false;
            $('#id_no').addClass('is-invalid');
            $('#id_no').after('<div class="invalid-feedback">The ID Number is required.</div>');
        }
        var hsnCode = $('#hsn_code').val();
        if (hsnCode.trim() === '') {
            formIsValid = false;
            $('#hsn_code').addClass('is-invalid');
            $('#hsn_code').after('<div class="invalid-feedback">The HSN Code is required.</div>');
        }
        var unitIssue = $('#unit_issue').val();
        if (unitIssue.trim() === '' || isNaN(unitIssue)) {
            formIsValid = false;
            $('#unit_issue').addClass('is-invalid');
            $('#unit_issue').after(
                '<div class="invalid-feedback">The Unit to Issue is required and must be a number.</div>'
            );
        }
        var amountPerStrip = $('#amount_per_strip').val();
        if (amountPerStrip.trim() === '' || isNaN(amountPerStrip)) {
            formIsValid = false;
            $('#amount_per_strip').addClass('is-invalid');
            $('#amount_per_strip').after(
                '<div class="invalid-feedback">The Amount Per Strip is required and must be a number.</div>'
            );
        }
        var tabletInStrip = $('#tablet_in_strip').val();
        if (tabletInStrip.trim() === '' || isNaN(tabletInStrip)) {
            formIsValid = false;
            $('#tablet_in_strip').addClass('is-invalid');
            $('#tablet_in_strip').after(
                '<div class="invalid-feedback">The Tablet in Strip is required and must be a number.</div>'
            );
        }
        var amountPerTab = $('#amount_per_tab').val();
        if (amountPerTab.trim() === '' || isNaN(amountPerTab)) {
            formIsValid = false;
            $('#amount_per_tab').addClass('is-invalid');
            $('#amount_per_tab').after(
                '<div class="invalid-feedback">The Amount per Tab is required and must be a number.</div>'
            );
        }
        var discount = $('#discount').val();
        if (discount.trim() === '' || isNaN(discount)) {
            formIsValid = false;
            $('#discount').addClass('is-invalid');
            $('#discount').after(
                '<div class="invalid-feedback">The Discount is required and must be a number.</div>'
            );
        }
        var sgst = $('#sgst').val();
        if (sgst.trim() === '' || isNaN(sgst)) {
            formIsValid = false;
            $('#sgst').addClass('is-invalid');
            $('#sgst').after(
                '<div class="invalid-feedback">The SGST is required and must be a number.</div>'
            );
        }
        var cgst = $('#cgst').val();
        if (cgst.trim() === '' || isNaN(cgst)) {
            formIsValid = false;
            $('#cgst').addClass('is-invalid');
            $('#cgst').after(
                '<div class="invalid-feedback">The CGST is required and must be a number.</div>'
            );
        }
        var igst = $('#igst').val();
        if (igst.trim() !== '' && isNaN(igst)) {
            formIsValid = false;
            $('#igst').addClass('is-invalid');
            $('#igst').after('<div class="invalid-feedback">The IGST must be a number.</div>');
        }
        if (formIsValid) {
            var tabletInStrip = parseFloat($('#tablet_in_strip').val());
            var amountPerStrip = parseFloat($('#amount_per_strip').val());
            var amountPerTab = '';
            if (!isNaN(tabletInStrip) && !isNaN(amountPerStrip) && amountPerStrip !== 0) {
                amountPerTab = (tabletInStrip / amountPerStrip).toFixed(
                    2);
            } else {
                amountPerTab = '';
            }
            var otcChecked = $('#otc').is(':checked') ? 1 : 0;
            var crdChecked = $('#crd').is(':checked') ? 1 : 0;
            var formData = {
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
                bill_status: $('#bill_status').is(':checked') ? 1 : 0,
                otc: otcChecked,
                crd: crdChecked
            };

            apiRequest({
                url: "/DrugTemplate/drug-template/store",
                method: "POST",
                data: formData,
                onSuccess: function (response) {
                    console.log("Full Response:", response);
                    console.log("Response Type:", typeof response);

                    // Handle unexpected string responses
                    if (typeof response === "string") {
                        try {
                            response = JSON.parse(response);
                        } catch (e) {
                            console.error("JSON Parsing Error:", e);
                            alert('Unexpected response format from the server.');
                            return;
                        }
                    }

                    console.log('Response:', response);

                    if (response.success === true) {
                        showToast("success", "Drug template Added successfully!");
                        window.location.href = 'https://login-users.hygeiaes.com/drugs/drug-template-list';
                    } else if (response.success === false) {
                        alert('An error occurred while saving the drug template.');
                    } else {
                        alert('Unexpected response from the server.');
                    }
                },
                onError: function (errorMessage) {
                    console.error('An error occurred:', errorMessage);
                    alert('An error occurred while saving the drug template.');
                }
            });

        }
    });
    const select2 = $('.select2');
    if (select2.length) {
        select2.each(function () {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>');
            $this.select2({
                placeholder: 'Select an option',
                dropdownParent: $this.parent()
            });
        });
    }
    $('#drug_type_name').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#drug_type_manufacturer').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#drug_type').on('change', function () {
        if ($(this).val() !== '') {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });
    $('#drug_ingredients').on('change', function () {
        if ($(this).val() !== '') {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });
    $('#drug_strength').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#restock_alert_count').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#crd').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#schedule').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#id_no').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#hsn_code').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#unit_issue').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#amount_per_strip').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#tablet_in_strip').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#amount_per_tab').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#discount').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#sgst').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#cgst').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    $('#igst').on('focus', function () {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
});
