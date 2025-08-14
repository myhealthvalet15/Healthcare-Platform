document.addEventListener('DOMContentLoaded', function () {
const attachmentInput = document.getElementById('prescriptionAttachment');
const thumbnailsContainer = document.getElementById('prescriptionThumbnails');
attachmentInput.addEventListener('change', function () {
    thumbnailsContainer.innerHTML = '';
    Array.from(this.files).forEach((file, index) => {
        if (!file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            const thumbWrapper = document.createElement('div');
            thumbWrapper.className = 'position-relative';
            thumbWrapper.style.width = '100px';
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'img-thumbnail';
            img.style.cursor = 'pointer';
            img.style.width = '100px';
            img.style.height = '100px';
            img.addEventListener('click', () => {
                document.getElementById('prescriptionModalImage').src = e.target.result;
                const modal = new bootstrap.Modal(document.getElementById('prescriptionModal'));
                modal.show();
            });
            const deleteBtn = document.createElement('span');
            deleteBtn.className = 'position-absolute top-0 end-0 translate-middle badge rounded-pill bg-danger';
            deleteBtn.textContent = '×';
            deleteBtn.style.cursor = 'pointer';
            deleteBtn.title = 'Remove';
            deleteBtn.onclick = () => {
                thumbWrapper.remove();
            };
            thumbWrapper.appendChild(img);
            thumbWrapper.appendChild(deleteBtn);
            thumbnailsContainer.appendChild(thumbWrapper);
        };
        reader.readAsDataURL(file);
    });
});
});

function addRow1(isPrefilling = false, lastRowId = 0) {
    var container = document.querySelector('.prescription-inputs');
    var rowCount = isPrefilling ? lastRowId + 1 : container.querySelectorAll('.prescription-row').length + 1;
    var newRow = `
        <div class="prescription-row" style="display: flex; align-items: center; gap: 10px;">
            <input type="hidden" name="rowid[]" value="${rowCount}">
            <div style="width: 31%;">      
                <div class="drug_name" title="drug_name">           
                    <select class="hiddendrugname select2" name="drug_template_id[]" id="drug_template_${rowCount}" style="height:25px;width:85%;font-weight:normal;">    
                        <option value="">Select a Drug</option>
                    </select>
                </div>
            </div>
            <div style="width: 5%;"><span id="result_${rowCount}"></span></div>
            <div style="width: 5%;">
                <input type="text" class="form-control" maxlength="3" name="duration[]" placeholder="Days" onkeypress="return ValidNumber(event)" style="width:65px;">
            </div>
            <div style="width: 30%;margin-left:20px;">
                <div style="float:left;width: 60px;">
                    <input type="text" maxlength="2" name="morning[]" class="form-control" placeholder="0" onkeypress="return ValidNumber(event)" style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                </div>
                <div style="float:left;width: 60px;">
                    <input type="text" maxlength="2" name="afternoon[]" class="form-control" placeholder="0" onkeypress="return ValidNumber(event)" style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                </div>
                <div style="float:left;width: 60px;">
                    <input type="text" maxlength="2" name="evening[]" class="form-control" placeholder="0" onkeypress="return ValidNumber(event)" style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                </div>
                <div style="float:left;width: 60px;">
                    <input type="text" maxlength="2" name="night[]" class="form-control" placeholder="0" onkeypress="return ValidNumber(event)" style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                </div>
            </div>
            <div style="width: 15%;text-align:center;">
                <select name="drugintakecondition[]" class="form-select">
                    <option value="">-Select-</option>
                    <option value="1">Before Food</option>
                    <option value="2">After Food</option>
                    <option value="3">With Food</option>
                    <option value="4">SOS</option>
                    <option value="5">Stat</option>
                </select>
            </div>
            <div style="width: 15%;">
                <input type="text" class="form-control" name="remarks[]" placeholder="Remarks" style="width:90%; height:36px!important;">
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
        url: "/PharmacyStock/getDrugTemplateDetails",
        method: "GET",
        onSuccess: function (response) {
            const drugTypeMapping = {
                1: "Capsule", 2: "Cream", 3: "Drops", 4: "Foam",
                5: "Gel", 6: "Inhaler", 7: "Injection", 8: "Lotion",
                9: "Ointment", 10: "Powder", 11: "Shampoo", 12: "Syringe",
                13: "Syrup", 14: "Tablet", 15: "Toothpaste", 16: "Suspension",
                17: "Spray", 18: "Test"
            };

            if (response && Array.isArray(response.drugTemplate) && response.drugTemplate.length > 0) {
                const defaultOption = document.createElement('option');
                defaultOption.text = 'Select Drug';
                defaultOption.value = '';
                defaultOption.selected = true;
                selectElement.appendChild(defaultOption);

                response.drugTemplate.forEach(function (drug) {
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
        onError: function (errorMessage) {
            console.error('Error fetching drug details:', errorMessage);
        }
    });
}
function addOutside() {
    var container = document.querySelector('.prescription-output');
    var rowCount = container.querySelectorAll('.prescription-row-output').length;
    var newRow = `
        <div class="prescription-row-output" style="display: flex; align-items: center; gap: 10px;">
            <input type="hidden" name="rowid[]" value="${rowCount}">
            <div style="width: 27%;">      
                <div class="drug_name" title="drug_name">
                           <input type="text" name="drugname[]" placeholder="Drug Name - Type - Strength" style="padding:5px;width:290px;height:37px;border:1px solid #d1d0d4;">
                        </div>
            </div>
            <div style="width: 5%;">
           </div>
            <div style="width: 5%;">
                <input type="text" class="form-control" maxlength="3" name="duration[]" placeholder="Days"  style="width:65px;">
            </div>
            <div style="width: 30%;margin-left:20px;">
                <div style="float:left;width: 60px;">
                    <input type="text" maxlength="2" name="morning" class="form-control" placeholder="0"  style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                </div>
                <div style="float:left;width: 60px;">
                    <input type="text" maxlength="2" name="afternoon[]" class="form-control" placeholder="0"  style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                </div>
                <div style="float:left;width: 60px;">
                    <input type="text" maxlength="2" name="evening[]" class="form-control" placeholder="0"  style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                </div>
                <div style="float:left;width: 60px;">
                    <input type="text" maxlength="2" name="night[]" class="form-control" placeholder="0"  style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                </div>
            </div>
            <div style="width: 15%;text-align:center;">
                <select name="drugintakecondition[]" class="form-select">
                    <option value="">-Select-</option>
                    <option value="1">Before Food</option>
                    <option value="2">After Food</option>
                    <option value="3">With Food</option>
                    <option value="4">SOS</option>
                    <option value="5">Stat</option>
                </select>
            </div>
            <div style="width: 15%;">
                <input type="text" class="form-control" name="remarks[]" placeholder="Remarks" style="width:90%; height:36px!important;">
            </div>
            <div style="width: 5%; text-align: center;">
                <div style="cursor: pointer;" class="margin-t-8" onclick="deleteRowoutpatient(this)">
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
function deleteRowoutpatient(btn) {
    var row = btn.closest('.prescription-row-output');
    row.remove();
}
$(document).ready(function () {
    flatpickr("#prescription_date", {
        dateFormat: "d-m-Y",
        maxDate: "today",
        defaultDate: "today",
    });
    function ValidNumber(event) {
        const key = event.key;
        return /\d/.test(key) || event.keyCode === 8 || event.keyCode === 9;
    }
    const currentUrl = window.location.href;
    const empIdMatch = currentUrl.match(/emp(\d{5})/);
    if (empIdMatch) {
        document.getElementById('emp_id').value = empIdMatch[0];
    } else {
        console.error('Employee ID not found in the URL.');
    }
    apiRequest({
        url: "/PharmacyStock/getDrugTemplateDetails",
        method: 'GET',
        onSuccess: function (response) {
            console.log("Full Response:", response);
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
                const drugSelect = $('#drug_template_0');
                drugSelect.append(new Option('Select Drug Type', '', true, true));
                response.drugTemplate.forEach(function (drug) {
                    const drugName = drug.drug_name || 'Unknown Drug';
                    const drugStrength = drug.drug_strength || 'Unknown Strength';
                    const drugType = drug.drug_type || 0;
                    const drugTypeName = drugTypeMapping[drugType] || 'Unknown Type';
                    const drugId = drug.drug_template_id;
                    const formattedDrug = `${drugName} - ${drugStrength} (${drugTypeName})`;
                    drugSelect.append(new Option(formattedDrug, drugId));
                });
                drugSelect.change(function () {
                    const selectedDrugId = $(this).val();
                    if (!selectedDrugId) {
                        $('#drug_details_section').hide();
                        return;
                    }
                    const selectedDrug = response.drugTemplate.find(function (drug) {
                        return drug.drug_template_id == selectedDrugId;
                    });
                });
            } else {
                console.error('No drug types or ingredients found');
                $('#drug_template_0').append(new Option('No drug types available', '', true, true));
            }
        },
        onError: function (error) {
            console.error('Error fetching drug details: ' + error);
        }
    });
    $('#add_prescription').on('click', function (e) {
        e.preventDefault();
        const prescriptionDate = document.getElementById('prescription_date').value;
        if (!prescriptionDate) {
            alert("Please select a prescription date.");
            return;
        }
        const prescriptionTemplate = document.getElementById('prescriptionTemplate');
        const rows = document.querySelectorAll('.prescription-row');
        let isValid = true;
        rows.forEach((row, index) => {
            const drugTemplate = row.querySelector('select[name="drug_template_id[]"]');
            const duration = row.querySelector('input[name="duration[]"]');
            const intakeCondition = row.querySelector('select[name="drugintakecondition[]"]');
            const morning = row.querySelector('input[name="morning[]"]');
            const afternoon = row.querySelector('input[name="afternoon[]"]');
            const evening = row.querySelector('input[name="evening[]"]');
            const night = row.querySelector('input[name="night[]"]');
            if (drugTemplate.value === "" && prescriptionTemplate.value === "") {
                alert("Please select a Drug Template.");
                isValid = false;
                return;
            }
            if (drugTemplate.value !== "") {
                if (!duration.value || isNaN(duration.value) || duration.value <= 0) {
                    alert(`Please enter a valid duration for drug template at row ${index + 1}.`);
                    isValid = false;
                }
                if (!intakeCondition.value) {
                    alert(`Please select an intake condition for drug template at row ${index + 1}.`);
                    isValid = false;
                }
                if (duration.value && (!morning.value && !afternoon.value && !evening.value && !night.value)) {
                    alert(`Please enter at least one time value (Morning, Afternoon, Evening, Night) for drug template at row ${index + 1}.`);
                    isValid = false;
                }
            }
        });
        if (!isValid) {
            return;
        }
        let prescription_Date = $('input[name="prescription_date"]').val();
        let formattedDate = '';
        if (prescription_Date) {
            let parts = prescription_Date.split('-');
            if (parts.length === 3) {
                let yyyy = parts[0];
                let mm = parts[1].padStart(2, '0');
                let dd = parts[2].padStart(2, '0');
                formattedDate = `${yyyy}-${mm}-${dd}`;
            }
        }
        const formData = {
            prescriptionTemplate: $('#prescriptionTemplate').val(),
            drugs: [],
            pharmacy: $('select[name="fav_pharmacy"]').val(),
            shareWithPatient: $('input[name="share_patient"]:checked').length > 0 ? 1 : 0,
            sendMailToPatient: $('input[name="share_patient"]:checked').length > 0 ? 1 : 0,
            doctorNotes: $('textarea[name="doctorNotes"]').val(),
            patientNotes: $('textarea[name="patientNotes"]').val(),
            user_id: $('input[name="emp_id"]').val(),
            prescription_date: formattedDate,
            ohc: 1,
            op_registry_id: opRegistryId,
            prescription_attachments: prescriptionFilesBase64,
        };
        $('.prescription-row').each(function (index, row) {
            var rowData = {
                drugTemplateId: $(row).find('select[name="drug_template_id[]"]').val(),
                drugName: 0,
                duration: $(row).find('input[name="duration[]"]').val(),
                morning: $(row).find('input[name="morning[]"]').val(),
                afternoon: $(row).find('input[name="afternoon[]"]').val(),
                evening: $(row).find('input[name="evening[]"]').val(),
                night: $(row).find('input[name="night[]"]').val(),
                drugIntakeCondition: $(row).find('select[name="drugintakecondition[]"]').val(),
                remarks: $(row).find('input[name="remarks[]"]').val(),
                ohc: 1,
                prescription_type: 'Type1'
            };
            formData.drugs.push(rowData);
        });
        let drugNameAvailable = false;
        $('.prescription-row-output').each(function (index, row) {
            var drugName = $(row).find('input[name="drugname[]"]').val();
            if (drugName) {
                drugNameAvailable = true;
            }
            var rowOutsideData = {
                drugTemplateId: 0,
                drugName: drugName,
                duration: $(row).find('input[name="duration[]"]').val(),
                morning: $(row).find('input[name="morning[]"]').val(),
                afternoon: $(row).find('input[name="afternoon[]"]').val(),
                evening: $(row).find('input[name="evening[]"]').val(),
                night: $(row).find('input[name="night[]"]').val(),
                drugIntakeCondition: $(row).find('select[name="drugintakecondition[]"]').val(),
                remarks: $(row).find('input[name="remarks[]"]').val(),
                ohc: 2,
                prescription_type: 'Type2',
                op_registry_id: opRegistryId
            };
            if (drugNameAvailable) {
                formData.drugs.push(rowOutsideData);
            }
        });
        
        apiRequest({
            url: "/prescription/store_EmployeePrescription",
            method: 'POST',
            data: formData,
            onSuccess: function (response) {
                if (response.message) {
                    toastr.success('Prescription saved successfully!', 'Success');
                    setTimeout(function () {
                        if (userType === 'MasterUser') {
                            window.location.href = 'https://login-users.hygeiaes.com/UserEmployee/userPrescription';
                        } else {
                            window.location.href = 'https://login-users.hygeiaes.com/prescription/prescription-view';
                        }
                    }, 2000);
                } else {
                    toastr.error('An error occurred while saving the prescription.', 'Error');
                }
            },
            onError: function (error) {
                console.error('API Request Error:', error);
                alert('An error occurred while saving the prescription.');
            }
        });
    });
    $('#addTest').on('click', function (e) {
        e.preventDefault();
        const prescriptionDate = document.getElementById('prescription_date').value;
        if (!prescriptionDate) {
            alert("Please select a prescription date.");
            return;
        }
        const prescriptionTemplate = document.getElementById('prescriptionTemplate');
        const rows = document.querySelectorAll('.prescription-row');
        let isValid = true;
        rows.forEach((row, index) => {
            const drugTemplate = row.querySelector('select[name="drug_template_id[]"]');
            const duration = row.querySelector('input[name="duration[]"]');
            const intakeCondition = row.querySelector('select[name="drugintakecondition[]"]');
            const morning = row.querySelector('input[name="morning[]"]');
            const afternoon = row.querySelector('input[name="afternoon[]"]');
            const evening = row.querySelector('input[name="evening[]"]');
            const night = row.querySelector('input[name="night[]"]');
            if (drugTemplate.value === "" && prescriptionTemplate.value === "") {
                alert("Please select a Drug Template.");
                isValid = false;
                return;
            }
            if (drugTemplate.value !== "") {
                if (!duration.value || isNaN(duration.value) || duration.value <= 0) {
                    alert(`Please enter a valid duration for drug template at row ${index + 1}.`);
                    isValid = false;
                }
                if (!intakeCondition.value) {
                    alert(`Please select an intake condition for drug template at row ${index + 1}.`);
                    isValid = false;
                }
                if (duration.value && (!morning.value && !afternoon.value && !evening.value && !night.value)) {
                    alert(`Please enter at least one time value (Morning, Afternoon, Evening, Night) for drug template at row ${index + 1}.`);
                    isValid = false;
                }
            }
        });
        if (!isValid) {
            return;
        }
        var prescription_Date = $('input[name="prescription_date"]').val();
        let dateObj = new Date(prescription_Date);
        let formattedDate = dateObj.toISOString().split('T')[0];
        console.log(formattedDate);
        var formData = {
            prescriptionTemplate: $('#prescriptionTemplate').val(),
            drugs: [],
            pharmacy: $('select[name="fav_pharmacy"]').val(),
            shareWithPatient: $('input[name="share_patient"]:checked').length > 0 ? 1 : 0,
            sendMailToPatient: $('input[name="share_patient"]:checked').length > 0 ? 1 : 0,
            doctorNotes: $('textarea[name="doctorNotes"]').val(),
            patientNotes: $('textarea[name="patientNotes"]').val(),
            user_id: $('input[name="emp_id"]').val(),
            prescription_date: formattedDate,
            ohc: 1,
            op_registry_id: opRegistryId,
            test: 1
        };
        $('.prescription-row').each(function (index, row) {
            var rowData = {
                drugTemplateId: $(row).find('select[name="drug_template_id[]"]').val(),
                drugName: 0,
                duration: $(row).find('input[name="duration[]"]').val(),
                morning: $(row).find('input[name="morning[]"]').val(),
                afternoon: $(row).find('input[name="afternoon[]"]').val(),
                evening: $(row).find('input[name="evening[]"]').val(),
                night: $(row).find('input[name="night[]"]').val(),
                drugIntakeCondition: $(row).find('select[name="drugintakecondition[]"]').val(),
                remarks: $(row).find('input[name="remarks[]"]').val(),
                ohc: 1,
                prescription_type: 'Type1'
            };
            formData.drugs.push(rowData);
        });
        let drugNameAvailable = false;
        $('.prescription-row-output').each(function (index, row) {
            var drugName = $(row).find('input[name="drugname[]"]').val();
            if (drugName) {
                drugNameAvailable = true;
            }
            var rowOutsideData = {
                drugTemplateId: 0,
                drugName: drugName,
                duration: $(row).find('input[name="duration[]"]').val(),
                morning: $(row).find('input[name="morning[]"]').val(),
                afternoon: $(row).find('input[name="afternoon[]"]').val(),
                evening: $(row).find('input[name="evening[]"]').val(),
                night: $(row).find('input[name="night[]"]').val(),
                drugIntakeCondition: $(row).find('select[name="drugintakecondition[]"]').val(),
                remarks: $(row).find('input[name="remarks[]"]').val(),
                ohc: 2,
                prescription_type: 'Type2',
                op_registry_id: opRegistryId
            };
            if (drugNameAvailable) {
                formData.drugs.push(rowOutsideData);
            }
        });
        apiRequest({
            url: "/prescription/store_EmployeePrescription",
            method: 'POST',
            data: formData,
            onSuccess: function (response) {
                if (response.message) {
                    console.log(response);
                    toastr.success('Prescription saved successfully!', 'Success');
                    const redirectUrl = `https://login-users.hygeiaes.com/ohc/health-registry/add-test/${response.employee_id}/prescription/${response.prescription_id}`;
                    setTimeout(function () {
                        window.location.href = redirectUrl;
                    }, 2000);
                } else {
                    toastr.error('An error occurred while saving the prescription.', 'Error');
                }
            },
            onError: function (error) {
                console.error('API Request Error:', error);
                alert('An error occurred while saving the prescription.');
            }
        });
    });
});
$(document).ready(function () {
    apiRequest({
        url: "/PharmacyStock/getDrugTemplateDetails",
        method: 'GET',
        onSuccess: function (response) {
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
            $(document).on('change', '#prescriptionTemplate', function () {
                var prescriptionTemplateId = $(this).val();
                if (prescriptionTemplateId) {
                    apiRequest({
                        url: `/prescription/prescription-editById/${prescriptionTemplateId}`,
                        method: 'GET',
                        onSuccess: function (data) {
                            console.log("Prefilling prescription data:", data);
                            if (data.length > 0) {
                                $('.prescription-inputs').empty();
                                const headerRow = `
                                <div class="prescription-header" style="display: flex; justify-content: space-between;">
                                    <div style="width: 23%;">Drug Name - Type - Strength</div>
                                    <div style="width: 8%;">Available</div>
                                    <div style="width: 5%;">Days</div>
                                    <div style="width: 30%;">
                                        <div style="display: inline-block; text-align: center; width: 50px; margin: 0 5px;">
                                            <img src="">
                                        </div>
                                        <div style="display: inline-block; text-align: center; width: 50px; margin: 0 5px;">
                                            <img src="/assets/img/prescription-icons/noon.png">
                                        </div>
                                        <div style="display: inline-block; text-align: center; width: 50px; margin: 0 5px;">
                                            <img src="https://www.hygeiaes.co/img/Evening.png">
                                        </div>
                                        <div style="display: inline-block; text-align: center; width: 50px; margin: 0 5px;">
                                            <img src="https://www.hygeiaes.co/img/Night.png">
                                        </div>
                                    </div>
                                    <div style="width: 15%;">AF/BF</div>
                                    <div style="width: 15%;">Remarks</div>
                                </div>
                            `;
                                $('.prescription-inputs').append(headerRow);
                                data.forEach((prescriptionData, index) => {
                                    const rowCount = index + 1;
                                    const isFirstRow = rowCount === 1;
                                    const addButtonIcon = isFirstRow ? 'fa-square-plus' : 'fa-minus';
                                    const newRow = `
                                    <div class="prescription-row" style="display: flex; align-items: center; gap: 10px;">
                                        <input type="hidden" name="rowid[]" value="${rowCount}">
                                        <div style="width: 31%;">
                                            <div class="drug_name" title="drug_name">
                                                <select class="hiddendrugname select2" name="drug_template_id[]" id="drug_template_${rowCount}" style="height:25px;width:85%;font-weight:normal;">
                                                    <option value="">Select a Drug</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div style="width: 5%;"><span id="result_${rowCount}"></span></div>
                                        <div style="width: 5%;">
                                            <input type="text" class="form-control" maxlength="3" name="duration[]" placeholder="Days" onkeypress="return ValidNumber(event)" style="width:65px;" value="${prescriptionData.intake_days}">
                                        </div>
                                        <div style="width: 30%;margin-left:20px;">
                                            <div style="float:left;width: 60px;">
                                                <input type="text" maxlength="2" name="morning[]" class="form-control" placeholder="0" onkeypress="return ValidNumber(event)" style="width:50px; text-align:center;margin-right: 8px;height:35px;" value="${prescriptionData.morning}">
                                            </div>
                                            <div style="float:left;width: 60px;">
                                                <input type="text" maxlength="2" name="afternoon[]" class="form-control" placeholder="0" onkeypress="return ValidNumber(event)" style="width:50px; text-align:center;margin-right: 8px;height:35px;" value="${prescriptionData.afternoon}">
                                            </div>
                                            <div style="float:left;width: 60px;">
                                                <input type="text" maxlength="2" name="evening[]" class="form-control" placeholder="0" onkeypress="return ValidNumber(event)" style="width:50px; text-align:center;margin-right: 8px;height:35px;" value="${prescriptionData.evening}">
                                            </div>
                                            <div style="float:left;width: 60px;">
                                                <input type="text" maxlength="2" name="night[]" class="form-control" placeholder="0" onkeypress="return ValidNumber(event)" style="width:50px; text-align:center;margin-right: 8px;height:35px;" value="${prescriptionData.night}">
                                            </div>
                                        </div>
                                        <div style="width: 15%;text-align:center;">
                                            <select name="drugintakecondition[]" class="form-select">
                                                <option value="">-Select-</option>
                                                <option value="1" ${prescriptionData.intake_condition == 1 ? 'selected' : ''}>Before Food</option>
                                                <option value="2" ${prescriptionData.intake_condition == 2 ? 'selected' : ''}>After Food</option>
                                                <option value="3" ${prescriptionData.intake_condition == 3 ? 'selected' : ''}>With Food</option>
                                                <option value="4" ${prescriptionData.intake_condition == 4 ? 'selected' : ''}>SOS</option>
                                                <option value="5" ${prescriptionData.intake_condition == 5 ? 'selected' : ''}>Stat</option>
                                            </select>
                                        </div>
                                        <div style="width: 15%;">
                                            <input type="text" class="form-control" name="remarks[]" placeholder="Remarks" style="width:90%; height:36px!important;" value="${prescriptionData.remarks}">
                                        </div>
                                        <div style="width: 5%; text-align: center;">
                                            <div style="cursor: pointer;" class="margin-t-8" onclick="addRow1()">
                                                <i class="fa-sharp fa-solid ${addButtonIcon}"></i>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                    $('.prescription-inputs').append(newRow);
                                    const drugSelect = $(`#drug_template_${rowCount}`);
                                    drugSelect.select2();
                                    const drugTemplateList = data.drugTemplate;
                                    drugTemplateList.forEach(drug => {
                                        const drugName = drug.drug_name || 'Unknown Drug';
                                        const drugStrength = drug.drug_strength || 'Unknown Strength';
                                        const drugType = drug.drug_type || 0;
                                        const drugTypeName = drugTypeMapping[drugType] || 'Unknown Type';
                                        const drugId = drug.drug_template_id;
                                        const formattedDrug = `${drugName} - ${drugStrength} (${drugTypeName})`;
                                        drugSelect.append(new Option(formattedDrug, drugId));
                                    });
                                    drugSelect.val(prescriptionData.drug_template_id).trigger('change');
                                });
                            } else {
                                console.error('No prescription data available for this template');
                                alert('No data found for this template');
                            }
                        },
                        onError: function (error) {
                            console.error('Error fetching prescription data: ' + error);
                        }
                    });
                }
            });
        },
        onError: function (error) {
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
            apiRequest({
                url: `/prescription/getStockByDrugId/${drugTemplateId}`,
                method: 'GET',
                onSuccess: function (data) {
                    console.log("Response Data:", data);
                    if (data && data.data && typeof data.data.total_current_availability !== 'undefined') {
                        $('#result_' + index).text(data.data.total_current_availability);
                    } else {
                        console.log("Availability is undefined or missing.");
                        $('#result_' + index).text('Not Available');
                    }
                },
                onError: function (error) {
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
function resetForm() {
    location.reload();
}
let prescriptionFilesBase64 = [];
document.getElementById('prescriptionAttachment').addEventListener('change', async function () {
    const container = document.getElementById('prescriptionThumbnails');
    container.innerHTML = '';
    prescriptionFilesBase64 = [];
    const files = Array.from(this.files);
    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    const maxSize = 1024 * 1024;
    for (const file of files) {
        if (!allowedTypes.includes(file.type)) {
            showToast('error', 'Invalid File', `${file.name} is not a valid image.`);
            continue;
        }
        if (file.size > maxSize) {
            showToast('error', 'File Too Large', `${file.name} exceeds 1MB.`);
            continue;
        }
        const base64 = await fileToBase64(file);
        prescriptionFilesBase64.push(base64);
        const img = document.createElement('img');
        img.src = base64;
        img.className = 'img-thumbnail';
        img.style.width = '100px';
        img.style.height = '100px';
        img.style.objectFit = 'cover';
        container.appendChild(img);
    }
    function fileToBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve(reader.result);
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }
});

