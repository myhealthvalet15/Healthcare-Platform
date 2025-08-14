function addRow1() {
    var container = document.querySelector('.prescription-inputs');
    var rowCount = container.querySelectorAll('.prescription-row').length;
    var newRow = `
        <div class="prescription-row" style="display: flex; align-items: center; gap: 10px;">
            <!-- Drug Name Input -->
            <input type="hidden" name="rowid[]" value="${rowCount}">
            <div style="width: 35%;">      
                <div class="drug_name" title="drug_name">           
                    <select class="hiddendrugname select2" name="drugname[]" id="drug_template_${rowCount}" style="height:25px;width:85%;">    
                        <option value="">Select a Drug</option>
                        <!-- Add drug options dynamically -->
                    </select>
                </div>
            </div>
            <!-- Days Input -->
            <div style="width: 5%;">
                <input type="text" class="form-control" maxlength="3" name="duration[]" placeholder="Days" onkeypress="return ValidNumber(event)" style="width:65px;">
            </div>
            <!-- Morning, Noon, Evening, Night Inputs -->
            <div style="width: 30%;margin-left:20px;">
                <div style="float:left;width: 60px;">
                    <input type="text" maxlength="2" name="morning[]" class="morning input-minix" placeholder="0" onkeypress="return ValidNumber(event)" style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                </div>
                <div style="float:left;width: 60px;">
                    <input type="text" maxlength="2" name="afternoon[]" class="afternoon input-minix" placeholder="0" onkeypress="return ValidNumber(event)" style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                </div>
                <div style="float:left;width: 60px;">
                    <input type="text" maxlength="2" name="evening[]" class="evening input-minix" placeholder="0" onkeypress="return ValidNumber(event)" style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                </div>
                <div style="float:left;width: 60px;">
                    <input type="text" maxlength="2" name="night[]" class="night input-minix" placeholder="0" onkeypress="return ValidNumber(event)" style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                </div>
            </div>
            <!-- AF/BF Select -->
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
            <!-- Remarks Input -->
            <div style="width: 15%;">
                <input type="text" class="form-control" name="remarks[]" placeholder="Remarks" style="width:90%; height:36px!important;">
            </div>
            <!-- Buttons for Add/Remove Rows -->
            <div style="width: 5%; text-align: center;">
                <div style="cursor: pointer;" class="margin-t-8" onclick="deleteRow(this)">
                    <i class="fa-sharp fa-solid fa-minus"></i> <!-- Replace plus with minus for subsequent rows -->
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', newRow);
    var newSelectElement = document.querySelector(`#drug_template_${rowCount}`);
    $(newSelectElement).select2();
    fetchDrugOptions(newSelectElement);
}
function deleteRow(btn) {
    var row = btn.closest('.prescription-row');
    row.remove();
}
function fetchDrugOptions(selectElement) {
    apiRequest({
        url: "/PharmacyStock/getDrugTemplateDetails",
        method: 'GET',
        onSuccess: function (response) {
            const drugTypeMapping = {
                1: "Capsule", 2: "Cream", 3: "Drops", 4: "Foam", 5: "Gel", 6: "Inhaler",
                7: "Injection", 8: "Lotion", 9: "Ointment", 10: "Powder", 11: "Shampoo",
                12: "Syringe", 13: "Syrup", 14: "Tablet", 15: "Toothpaste", 16: "Suspension",
                17: "Spray", 18: "Test"
            };
            if (typeof selectElement === 'undefined') {
                console.error('selectElement is not defined');
                return;
            }
            selectElement.innerHTML = '';
            if (response && response.drugTemplate && Array.isArray(response.drugTemplate) && response.drugTemplate.length > 0) {
                const defaultOption = document.createElement('option');
                defaultOption.text = 'Select Drug Type';
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
            console.error('Error fetching drug details: ' + errorMessage);
        }
    });
}
$(document).ready(function () {
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
                    console.log("Selected Drug:", selectedDrug);
                });
            } else {
                console.error('No drug types or ingredients found');
                $('#drug_template_0').append(new Option('No drug types available', '', true, true));
            }
        },
        onError: function (errorMessage) {
            console.error('Error fetching drug details: ' + errorMessage);
        }
    });
    $('#submitBtn').click(function (e) {
        e.preventDefault();
        var templateName = $('#template-name').val().trim();
        if (templateName === '') {
            alert('Template Name is required!');
            return;
        }
        var valid = true;
        $('.prescription-row').each(function () {
            var drugName = $(this).find('select[name="drugname[]"]').val();
            var duration = $(this).find('input[name="duration[]"]').val();
            var morning = $(this).find('input[name="morning[]"]').val();
            var afternoon = $(this).find('input[name="afternoon[]"]').val();
            var evening = $(this).find('input[name="evening[]"]').val();
            var night = $(this).find('input[name="night[]"]').val();
            var afbf = $(this).find('select[name="drugintakecondition[]"]').val();
            if (!drugName || !duration || !afbf || morning === "" || afternoon === "" || evening === "" || night === "") {
                valid = false;
                return false;
            }
        });
        if (!valid) {
            alert('Please fill out all prescription fields.');
            return;
        }
        var formData = {
            template_name: templateName,
            prescriptions: []
        };
        $('.prescription-row').each(function () {
            var row = {
                drugname: $(this).find('select[name="drugname[]"]').val(),
                duration: $(this).find('input[name="duration[]"]').val(),
                morning: $(this).find('input[name="morning[]"]').val(),
                afternoon: $(this).find('input[name="afternoon[]"]').val(),
                evening: $(this).find('input[name="evening[]"]').val(),
                night: $(this).find('input[name="night[]"]').val(),
                afbf: $(this).find('select[name="drugintakecondition[]"]').val(),
                remarks: $(this).find('input[name="remarks[]"]').val()
            };
            formData.prescriptions.push(row);
        });
        apiRequest({
            url: "/prescription/store",
            method: 'POST',
            data: formData,
            onSuccess: function (response) {
    if (response.result === true) {
        toastr.success('Prescription submitted successfully!', 'Success');
        window.location.href = '/prescription/prescription-template';
    } else {
        alert('Error: ' + response.message);
    }
},
            onError: function (errorMessage) {
                console.error('Error submitting prescription: ' + errorMessage);
                alert('Something went wrong. Please try again.');
            }
        });
    });
});
