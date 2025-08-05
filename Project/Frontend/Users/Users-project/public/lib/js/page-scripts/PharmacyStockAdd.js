    $(document).ready(function() {
        
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        // Fetch drug types and ingredients on page load
        $.ajax({
            url: "/PharmacyStock/getDrugTemplateDetails",
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
                    url: "/PharmacyStock/pharmacyStock/store", // The route for storing data
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
   

