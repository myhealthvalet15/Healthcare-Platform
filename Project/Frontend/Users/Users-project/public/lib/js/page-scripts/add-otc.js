
    function populateSelect(selectId, data) {
        const selectElement = document.getElementById(selectId);
        if (!selectElement) return;
        selectElement.innerHTML = '';
        data.forEach((item) => {
            let option = document.createElement('option');
            option.value = item.op_component_id;
            option.textContent = item.op_component_name;
            selectElement.appendChild(option);
        });
        if ($(selectElement).hasClass('select2')) {
            $(selectElement).trigger('change');
        }
    }

    function formatDateForInput(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }


    $(document).ready(function() {



        const isOutPatientAdded = $('#isOutPatientAdded').val();
        const isOutPatientAddedAndOpen = $('#isOutPatientAddedAndOpen').val();
        const now = new Date();
        const formattedDateTime_1 = formatDateForInput(now);
        // document.getElementById('leave-from').value = formattedDateTime_1;
        const dayLater = new Date(now);
        dayLater.setDate(dayLater.getDate() + 1);
        const formattedDateTime_2 = formatDateForInput(now);
        document.getElementById('reporting-datetime').value = formattedDateTime_2;



        const spinnerLabel = document.getElementById('spinnerLabeltext');
        const spinner = document.getElementById('add-registry-spinner');
        const registryCard = document.getElementById('add-registry-card');
        const apiSteps = [{
            url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllSymptoms',
            message: 'Retrieving Symptoms...',
            selectId: 'select2Primary_symptoms'
        },{ url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllMedicalSystem', message: 'Retrieving Medical Systems...', selectId: 'select2Primary_medical_system' },];
        const apiPromises = apiSteps.map((step, index) => {
            return new Promise((resolve, reject) => {
                setTimeout(() => {
                    spinnerLabel.textContent = step.message;
                    apiRequest({
                        url: step.url,
                        onSuccess: function(response) {
                            if (response.result && response.message) {
                                if (step.isMRNumber) {
                                    // document.getElementById('mrNumber').textContent = response.message;
                                } else if (Array.isArray(step.selectId)) {
                                    step.selectId.forEach(id => populateSelect(id, response.message));
                                } else {
                                    populateSelect(step.selectId, response.message);
                                }
                            }
                            resolve();
                        },
                        onError: function(error) {
                            console.error(`Error fetching ${step.message}:`, error);
                            showToast('error', 'Error', `Failed to load ${step.message}`);
                            reject(error);
                        }
                    });
                }, index * 500);
            });
        });
        Promise.all(apiPromises)
            .then(() => {
                if (isOutPatientAdded) {
                    showToast('success', 'Data Fetched Successfully.');
                }
                //spinnerLabel.textContent = "Preparing Outpatient Data...";
                setTimeout(() => {
                    spinner.style.display = 'none';
                    registryCard.style.display = 'block';
                }, 1000);
            })
            .catch((error) => {
                console.error('One or more API requests failed:', error);
            });
    });

    function addRow1(isPrefilling = false, lastRowId = 0) {
        var container = document.querySelector('.prescription-inputs');

        // Determine the next row id based on whether we are pre-filling or not
        var rowCount = isPrefilling ? lastRowId + 1 : container.querySelectorAll('.prescription-row').length + 1;

        // Create a new row with a unique ID for the select dropdown
        var newRow = `
        <div class="prescription-row" style="display: flex; align-items: center; gap: 10px;">
            <!-- Drug Name Input -->
            <input type="hidden" name="rowid[]" value="${rowCount}">
            
            <div style="width: 35%;">      
                <div class="drug_name" title="drug_name">           
                    <select class="hiddendrugname select2" name="drug_template_id[]" id="drug_template_${rowCount}" style="height:25px;width:85%;font-weight:normal;">    
                        <option value="">Select a Drug</option>
                        <!-- Add drug options dynamically -->
                    </select>
                </div>
            </div>
            <div style="width: 26%;margin-left:160px;"><span id="result_${rowCount}"></span></div>
           
            <!-- Remarks Input -->
            <div style="width: 15%;">
                <input type="text" class="form-control" name="issue[]" placeholder="Issue" style="width:90%; height:36px!important;">
            </div>

            <!-- Buttons for Add/Remove Rows -->
            <div style="width: 5%; text-align: center;">
                <div style="cursor: pointer;" class="margin-t-8" onclick="deleteRow(this)">
                    <i class="fa-sharp fa-solid fa-minus"></i> <!-- Replace plus with minus for subsequent rows -->
                </div>
            </div>
        </div>
    `;

        // Append the new row to the container
        container.insertAdjacentHTML('beforeend', newRow);

        // Initialize select2 for the newly added drug dropdown
        var newSelectElement = document.querySelector(`#drug_template_${rowCount}`);
        $(newSelectElement).select2();

        // Fetch and add drug options to the new select dropdown
        fetchDrugOptions(newSelectElement);
    }



    // Function to check if a selected drug has already been chosen in any row
    function checkDrugSelection() {
        console.log('AM here');
        var selectedDrugs = [];
        var selects = document.querySelectorAll('.hiddendrugname.select2'); // Get all select elements with the class 'hiddendrugname'

        // Convert NodeList to Array so you can use forEach
        Array.from(selects).forEach(function(select) {
            var selectedValue = select.value;

            // Check if a valid drug is selected and if it has already been selected
            if (selectedValue && selectedDrugs.includes(selectedValue)) {
                // If the drug has already been selected, alert the user
                alert('This drug has already been selected. Please choose another drug.');
                select.value = ''; // Clear the selection
                $(select).trigger('change'); // Trigger change to refresh the select2 UI
            } else if (selectedValue) {
                // Add the drug to the list if it is valid and not already selected
                selectedDrugs.push(selectedValue);
            }
        });
    }


    // Attach the change event to all select elements with the class 'hiddendrugname'
    $('.hiddendrugname').on('change', checkDrugSelection);

    function deleteRow(btn) {
        var row = btn.closest('.prescription-row');
        row.remove();
    }

    function fetchDrugOptions(selectElement) {
        $.ajax({
            url: "{{ route('getDrugTemplateDetails') }}",
            method: 'GET',
            dataType: 'json',
            success: function(response) {
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
                    var defaultOption = document.createElement('option');
                    defaultOption.text = 'Select Drug';
                    defaultOption.value = '';
                    defaultOption.selected = true;
                    selectElement.appendChild(defaultOption);

                    response.drugTemplate.forEach(function(drug) {
                        if ((drug.crd === 1 || drug.crd === "1") || (drug.otc === 1 || drug.otc === "1")) {
                            return;
                        }


                        var drugName = drug.drug_name || 'Unknown Drug';
                        var drugStrength = drug.drug_strength || 'Unknown Strength';
                        var drugType = drug.drug_type || 0;
                        var drugTypeName = drugTypeMapping[drugType] || 'Unknown Type';
                        var drugId = drug.drug_template_id;

                        var formattedDrug = `${drugName} - ${drugStrength} (${drugTypeName})`;
                        var option = document.createElement('option');
                        option.value = drugId;
                        option.textContent = formattedDrug;
                        selectElement.appendChild(option);
                    });
                } else {
                    console.error('No drug types available');
                    var noOption = document.createElement('option');
                    noOption.text = 'No drug types available';
                    noOption.value = '';
                    noOption.selected = true;
                    selectElement.appendChild(noOption);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching drug details: ' + error);
            }
        });
    }





    $(document).ready(function() {
     
       

        // Fetch drug template details on page load
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
                  
                var drugSelect = $('#drug_template_0');
                var nonOtcDrugsDiv = $('#non_otc_drugs_div'); // Create this div in your HTML
                drugSelect.empty().append(new Option('Select Drug Type', '', true, true));
                nonOtcDrugsDiv.empty(); // Clear any previous data

                if (response && response.drugTemplate && Array.isArray(response.drugTemplate) && response.drugTemplate.length > 0) {


                    drugSelect.append(new Option('Select Drug Type', '', true, true));

                    response.drugTemplate.forEach(function(drug, index) {
                        if (drug.crd === 1 || drug.crd === "1") {
                            return; // Skip discontinued drugs
                        }

                        var drugName = drug.drug_name || 'Unknown Drug';
                        var drugStrength = drug.drug_strength || 'Unknown Strength';
                        var drugType = drug.drug_type || 0;
                        var drugTypeName = drugTypeMapping[drugType] || 'Unknown Type';
                        var drugId = drug.drug_template_id;
                        var formattedDrug = `${drugName} - ${drugStrength} (${drugTypeName})`;
                        var pharmacyId = $('#fav_pharmacy').val();
                        if (drug.otc === 0 || drug.otc === "0") {
                            // Add to dropdown if otc is 0 (available for selection)
                            drugSelect.append(new Option(formattedDrug, drugId));
                        } else {
                            // Add to non-OTC div
                            var drugDivId = `non_otc_drug_${drugId}`;
                            var drugInfoHtml = `
    <div class="non-otc-drug" id="${drugDivId}">
        <div class="drug-row">
            <div class="drug-name"style="color: #4444e5;"><strong>${drugName}</strong> - ${drugStrength} <em>(${drugTypeName})</em></div>
            <div class="drug-availability availability">Loading...</div>
            <div class="drug-issue"><input style="padding-left:11px;" type="text" name="issue[]" id="issue_${drugId}" placeholder="Issue"></div>
        </div>
    </div>`;
                            nonOtcDrugsDiv.append(drugInfoHtml);


                            // Fetch and display availability for this drug
                            $.ajax({
                                url: `/prescription/getStockByDrugIdAndPharmacyId/${drugId}/${pharmacyId}`,
                                method: 'GET',
                                dataType: 'json',
                                success: function(data) {
                                    if (data && data.data && typeof data.data.total_current_availability !== 'undefined') {
                                        $(`#${drugDivId} .availability`).text(`  ${data.data.total_current_availability}`);
                                    } else {
                                        $(`#${drugDivId} .availability`).text(' (Not Available)');
                                    }
                                },
                                error: function() {
                                    $(`#${drugDivId} .availability`).text(' (Error fetching availability)');
                                }
                            });
                        }
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

                    });
                } else {
                    console.error('No drug types or ingredients found');
                    $('#drug_template_0').append(new Option('No drug types available', '', true, true));
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching drug details: ' + error);
            }
        });

        $(document).on('change', '.hiddendrugname', function() {
            var drugTemplateId = $(this).val();
            var index = $(this).attr('id').split('_')[2]; // Get the index number from the id (e.g., 0, 1, 2)

            console.log("Drug Template ID:", drugTemplateId);
            console.log("Index:", index);

            if (drugTemplateId) {
                var isDuplicate = false;

                // Check if the selected drug is already chosen in another select element
                $('.hiddendrugname').each(function() {
                    var selectedDrug = $(this).val();
                    var currentIndex = $(this).attr('id').split('_')[2]; // Get the index number of this select element

                    // Compare selected drugs, skip comparison if it's the same element (the one that triggered the change event)
                    if (selectedDrug && selectedDrug === drugTemplateId && currentIndex !== index) {
                        isDuplicate = true; // Mark as duplicate if found
                    }
                });

                if (isDuplicate) {
                    // Alert the user that the drug has already been selected
                    alert('This drug has already been selected. Please choose another drug.');
                    $(this).val(''); // Clear the current selection
                    $(this).trigger('change'); // Trigger change to refresh the select2 UI
                    $('#result_' + index).text(''); // Clear availability result
                    return; // Exit the function early
                }
                var pharmacyId = $('#fav_pharmacy').val();
                // If no duplicates, proceed to fetch stock availability
                $.ajax({
                    url: `/prescription/getStockByDrugIdAndPharmacyId/${drugTemplateId}/${pharmacyId}`,
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        console.log("Response Data:", data); // Log the full response

                        if (data && data.data && typeof data.data.total_current_availability !== 'undefined') {
                            $('#result_' + index).text(data.data.total_current_availability); // Set the availability
                        } else {
                            console.log("Availability is undefined or missing.");
                            $('#result_' + index).text('Not Available'); // Show 'Not Available' if the data is missing
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("Error fetching stock data:", error);
                        $('#result_' + index).text('Error');
                    }
                });
            } else {
                // Clear the result if no drug is selected
                $('#result_' + index).text('');
            }
        });



        // Function to delete a row
        function deleteRow(row) {
            // Ensure you don't delete the last row if only one row exists
            if ($('.prescription-row').length > 1) {
                $(row).closest('.prescription-row').remove();
            }
        }

    });
    document.querySelector("input[name='submit_otc']").addEventListener("click", async function () {
    // Clear previous errors
    document.getElementById("symptomsError").innerText = "";
    document.getElementById("systemError").innerText = "";
    document.getElementById("remarksError").innerText = "";
    document.querySelectorAll(".prescription-error").forEach(el => el.innerText = "");

    let isValid = true;

    const symptoms = Array.from(document.getElementById("select2Primary_symptoms").selectedOptions).map(opt => opt.value);
    const medicalSystems = Array.from(document.getElementById("select2Primary_medical_system").selectedOptions).map(opt => opt.value);
    const remarks = document.getElementById("doctorNotes").value.trim();
    const userId = document.getElementById("user_id").value;
    const ohc_pharmacy_id = document.getElementById("fav_pharmacy").value;
    const shift= document.getElementById("workShift").value;
    const first_aid_by = document.getElementById("firstAidBy").value;
    const created_date_time = document.getElementById("reporting-datetime").value;
        if (symptoms.length === 0) {
        document.getElementById("symptomsError").innerText = "Please select at least one symptom.";
        isValid = false;
    }

    if (medicalSystems.length === 0) {
        document.getElementById("systemError").innerText = "Please select at least one medical system.";
        isValid = false;
    }

    if (remarks === "") {
        document.getElementById("remarksError").innerText = "Remarks are required.";
        isValid = false;
    }

    const prescriptions = [];
    let hasAtLeastOneDrug = false;

    // ✅ Validate dropdown (OTC) drugs
    const rows = document.querySelectorAll(".prescription-row");

    rows.forEach((row) => {
        const drugSelect = row.querySelector("select[name='drug_template_id[]']");
        const issueInput = row.querySelector("input[name='issue[]']");
        const availableSpan = row.querySelector("span[id^='result_']");
        const errorDiv = row.querySelector(".prescription-error");

        if (errorDiv) {
            errorDiv.innerText = "";
        }

        const drugId = drugSelect?.value;
        const issue = parseInt(issueInput?.value) || 0;
        const available = parseInt(availableSpan?.innerText) || 0;

        if (drugId && drugId !== "Select a Drug") {
            hasAtLeastOneDrug = true;

            if (issue > available) {
                if (errorDiv) errorDiv.innerText = `Issue can't be more than available (${available}).`;
                isValid = false;
            }

            prescriptions.push({ drugId, issue });
        } else if (issue > 0) {
            if (errorDiv) errorDiv.innerText = "Select a drug.";
            isValid = false;
        }
    });

    // ✅ Validate non-OTC drugs
    document.querySelectorAll(".non-otc-drug").forEach((nonOtcDiv) => {
        const drugId = nonOtcDiv.id.replace("non_otc_drug_", "");
        const issueInput = nonOtcDiv.querySelector("input[name='issue[]']");
        const availabilityText = nonOtcDiv.querySelector(".availability").innerText.trim();

        let errorDiv = nonOtcDiv.querySelector(".prescription-error");
        if (!errorDiv) {
            errorDiv = document.createElement("div");
            errorDiv.classList.add("prescription-error");
            errorDiv.style.color = "red";
            nonOtcDiv.appendChild(errorDiv);
        }

        errorDiv.innerText = "";

        const issue = parseInt(issueInput?.value) || 0;
        const available = parseInt(availabilityText) || 0;

        if (issue > 0) {
            hasAtLeastOneDrug = true;

            if (issue > available) {
                errorDiv.innerText = `Issue can't be more than available (${available}).`;
                isValid = false;
            }

            prescriptions.push({ drugId, issue });
        }
    });

    if (!hasAtLeastOneDrug) {
        alert("Select at least one drug.");
        isValid = false;
    }

    if (!isValid) return;

    // ✅ Prepare data to submit
    const postData = {
        user_id: userId,
        ohc_pharmacy_id,
        shift,
        first_aid_by,
        created_date_time,
        symptoms,
        medicalSystems,
        remarks,
        prescriptions
    };

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const response = await fetch("/otc/storeotc", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": csrfToken
            },
            body: JSON.stringify(postData)
        });

        if (!response.ok) throw new Error("Submission failed.");

        const result = await response.json();
        alert("OTC data saved successfully!");
        // Optionally reload
        // location.reload();

    } catch (err) {
        alert("Error submitting data: " + err.message);
    }
});

