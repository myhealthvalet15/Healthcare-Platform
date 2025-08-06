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
$(document).ready(function () {
    const isOutPatientAdded = $('#isOutPatientAdded').val();
    const isOutPatientAddedAndOpen = $('#isOutPatientAddedAndOpen').val();
    const now = new Date();
    const formattedDateTime_1 = formatDateForInput(now);
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
    }, { url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllMedicalSystem', message: 'Retrieving Medical Systems...', selectId: 'select2Primary_medical_system' },];
    const apiPromises = apiSteps.map((step, index) => {
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                spinnerLabel.textContent = step.message;
                apiRequest({
                    url: step.url,
                    onSuccess: function (response) {
                        if (response.result && response.message) {
                            if (step.isMRNumber) {
                            } else if (Array.isArray(step.selectId)) {
                                step.selectId.forEach(id => populateSelect(id, response.message));
                            } else {
                                populateSelect(step.selectId, response.message);
                            }
                        }
                        resolve();
                    },
                    onError: function (error) {
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
    var rowCount = isPrefilling ? lastRowId + 1 : container.querySelectorAll('.prescription-row').length + 1;
    var newRow = `
        <div class="prescription-row" style="display: flex; align-items: center; gap: 10px;">
            <input type="hidden" name="rowid[]" value="${rowCount}">
            <div style="width: 35%;">      
                <div class="drug_name" title="drug_name">           
                    <select class="hiddendrugname select2" name="drug_template_id[]" id="drug_template_${rowCount}" style="height:25px;width:85%;font-weight:normal;">    
                        <option value="">Select a Drug</option>
                    </select>
                </div>
            </div>
            <div style="width: 26%;margin-left:160px;"><span id="result_${rowCount}"></span></div>
            <div style="width: 15%;">
                <input type="text" class="form-control" name="issue[]" placeholder="Issue" style="width:90%; height:36px!important;">
            </div>
            <div style="width: 5%; text-align: center;">
                <div style="cursor: pointer;" class="margin-t-8" onclick="deleteRow(this)">
                    <i class="fa-sharp fa-solid fa-minus"></i> 
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', newRow);
    var newSelectElement = document.querySelector(`#drug_template_${rowCount}`);
    $(newSelectElement).select2();
    fetchDrugOptions(newSelectElement);
}
function checkDrugSelection() {
    console.log('AM here');
    var selectedDrugs = [];
    var selects = document.querySelectorAll('.hiddendrugname.select2');
    Array.from(selects).forEach(function (select) {
        var selectedValue = select.value;
        if (selectedValue && selectedDrugs.includes(selectedValue)) {
            alert('This drug has already been selected. Please choose another drug.');
            select.value = '';
            $(select).trigger('change');
        } else if (selectedValue) {
            selectedDrugs.push(selectedValue);
        }
    });
}
$('.hiddendrugname').on('change', checkDrugSelection);
function deleteRow(btn) {
    var row = btn.closest('.prescription-row');
    row.remove();
}
function fetchDrugOptions(selectElement) {
    apiRequest({
        url: "{{ route('getDrugTemplateDetails') }}",
        method: 'GET',
        onSuccess: (response) => {
            const drugTypeMapping = {
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
                const defaultOption = document.createElement('option');
                defaultOption.text = 'Select Drug';
                defaultOption.value = '';
                defaultOption.selected = true;
                selectElement.appendChild(defaultOption);
                response.drugTemplate.forEach(function (drug) {
                    if ((drug.crd === 1 || drug.crd === "1") || (drug.otc === 1 || drug.otc === "1")) {
                        return;
                    }
                    const drugName = drug.drug_name || 'Unknown Drug';
                    const drugStrength = drug.drug_strength || 'Unknown Strength';
                    const drugType = drug.drug_type || 0;
                    const drugTypeName = drugTypeMapping[drugType] || 'Unknown Type';
                    const drugId = drug.drug_template_id;
                    const formattedDrug = `${drugName} - ${drugStrength} (${drugTypeName})`;
                    const option = document.createElement('option');
                    option.value = drugId;
                    option.textContent = formattedDrug;
                    selectElement.appendChild(option);
                });
            } else {
                console.error('No drug types available');
                const noOption = document.createElement('option');
                noOption.text = 'No drug types available';
                noOption.value = '';
                noOption.selected = true;
                selectElement.appendChild(noOption);
            }
        },
        onError: (error) => {
            console.error('Error fetching drug details: ' + error);
        }
    });
}
$(document).ready(function () {
    apiRequest({
        url: "{{ route('getDrugTemplateDetails') }}",
        method: 'GET',
        onSuccess: (response) => {
            console.log("Full Response:", response);
            const drugTypeMapping = {
                1: "Capsule", 2: "Cream", 3: "Drops", 4: "Foam", 5: "Gel", 6: "Inhaler",
                7: "Injection", 8: "Lotion", 9: "Ointment", 10: "Powder", 11: "Shampoo",
                12: "Syringe", 13: "Syrup", 14: "Tablet", 15: "Toothpaste", 16: "Suspension",
                17: "Spray", 18: "Test"
            };
            const drugSelect = $('#drug_template_0');
            const nonOtcDrugsDiv = $('#non_otc_drugs_div');
            const pharmacyId = $('#fav_pharmacy').val();
            drugSelect.empty().append(new Option('Select Drug Type', '', true, true));
            nonOtcDrugsDiv.empty();
            if (response && Array.isArray(response.drugTemplate) && response.drugTemplate.length > 0) {
                response.drugTemplate.forEach((drug, index) => {
                    if (drug.crd === 1 || drug.crd === "1") return;
                    const drugName = drug.drug_name || 'Unknown Drug';
                    const drugStrength = drug.drug_strength || 'Unknown Strength';
                    const drugTypeName = drugTypeMapping[drug.drug_type] || 'Unknown Type';
                    const drugId = drug.drug_template_id;
                    const formattedDrug = `${drugName} - ${drugStrength} (${drugTypeName})`;
                    if (drug.otc === 0 || drug.otc === "0") {
                        drugSelect.append(new Option(formattedDrug, drugId));
                    } else {
                        const drugDivId = `non_otc_drug_${drugId}`;
                        const drugInfoHtml = `
                                <div class="non-otc-drug" id="${drugDivId}">
                                    <div class="drug-row">
                                        <div class="drug-name" style="color: #4444e5;"><strong>${drugName}</strong> - ${drugStrength} <em>(${drugTypeName})</em></div>
                                        <div class="drug-availability availability">Loading...</div>
                                        <div class="drug-issue"><input style="padding-left:11px;" type="text" name="issue[]" id="issue_${drugId}" placeholder="Issue"></div>
                                    </div>
                                </div>`;
                        nonOtcDrugsDiv.append(drugInfoHtml);
                        apiRequest({
                            url: `/prescription/getStockByDrugIdAndPharmacyId/${drugId}/${pharmacyId}`,
                            method: 'GET',
                            onSuccess: (data) => {
                                const availability = data?.data?.total_current_availability;
                                $(`#${drugDivId} .availability`).text(
                                    availability !== undefined ? `  ${availability}` : ' (Not Available)'
                                );
                            },
                            onError: () => {
                                $(`#${drugDivId} .availability`).text(' (Error fetching availability)');
                            }
                        });
                    }
                });
                drugSelect.change(function () {
                    const selectedDrugId = $(this).val();
                    if (!selectedDrugId) {
                        $('#drug_details_section').hide();
                        return;
                    }
                    const selectedDrug = response.drugTemplate.find(drug => drug.drug_template_id == selectedDrugId);
                });
            } else {
                console.error('No drug types or ingredients found');
                drugSelect.append(new Option('No drug types available', '', true, true));
            }
        },
        onError: (error) => {
            console.error('Error fetching drug details: ' + error);
        }
    });
    $(document).on('change', '.hiddendrugname', function () {
        var drugTemplateId = $(this).val();
        var index = $(this).attr('id').split('_')[2];
        console.log("Drug Template ID:", drugTemplateId);
        console.log("Index:", index);
        if (drugTemplateId) {
            var isDuplicate = false;
            $('.hiddendrugname').each(function () {
                var selectedDrug = $(this).val();
                var currentIndex = $(this).attr('id').split('_')[2];
                if (selectedDrug && selectedDrug === drugTemplateId && currentIndex !== index) {
                    isDuplicate = true;
                }
            });
            if (isDuplicate) {
                alert('This drug has already been selected. Please choose another drug.');
                $(this).val('');
                $(this).trigger('change');
                $('#result_' + index).text('');
                return;
            }
            var pharmacyId = $('#fav_pharmacy').val();
            apiRequest({
                url: `/prescription/getStockByDrugIdAndPharmacyId/${drugTemplateId}/${pharmacyId}`,
                method: 'GET',
                onSuccess: (data) => {
                    console.log("Response Data:", data);
                    if (data && data.data && typeof data.data.total_current_availability !== 'undefined') {
                        $('#result_' + index).text(data.data.total_current_availability);
                    } else {
                        console.log("Availability is undefined or missing.");
                        $('#result_' + index).text('Not Available');
                    }
                },
                onError: (error) => {
                    console.log("Error fetching stock data:", error);
                    $('#result_' + index).text('Error');
                }
            });
        } else {
            $('#result_' + index).text('');
        }
    });
    function deleteRow(row) {
        if ($('.prescription-row').length > 1) {
            $(row).closest('.prescription-row').remove();
        }
    }
});
document.querySelector("input[name='submit_otc']").addEventListener("click", async function () {
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
    const shift = document.getElementById("workShift").value;
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
        apiRequest({
            url: "/otc/storeotc",
            method: "POST",
            data: postData,
            onSuccess: (result) => {
                alert("OTC data saved successfully!");
            },
            onError: (error) => {
                throw new Error("Submission failed.");
            }
        });
    } catch (err) {
        alert("Error submitting data: " + err.message);
    }
});
