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
        url: '/PharmacyStock/getDrugTemplateDetails',
        method: 'GET',
        onSuccess: function (response) {
            console.log('Drug Options Response:', response);
            const drugTypeMapping = {
                1: 'Capsule',
                2: 'Cream',
                3: 'Drops',
                4: 'Foam',
                5: 'Gel',
                6: 'Inhaler',
                7: 'Injection',
                8: 'Lotion',
                9: 'Ointment',
                10: 'Powder',
                11: 'Shampoo',
                12: 'Syringe',
                13: 'Syrup',
                14: 'Tablet',
                15: 'Toothpaste',
                16: 'Suspension',
                17: 'Spray',
                18: 'Test'
            };
            $(selectElement).empty();
            $(selectElement).append(new Option('Select a Drug', '', true, true));
            if (
                response &&
                response.drugTemplate &&
                Array.isArray(response.drugTemplate) &&
                response.drugTemplate.length > 0
            ) {
                response.drugTemplate.forEach(function (drug) {
                    const drugName = drug.drug_name || 'Unknown Drug';
                    const drugStrength = drug.drug_strength || 'Unknown Strength';
                    const drugType = drug.drug_type || 0;
                    const drugTypeName = drugTypeMapping[drugType] || 'Unknown Type';
                    const drugId = drug.drug_template_id;
                    const formattedDrug = `${drugName} - ${drugStrength} (${drugTypeName})`;
                    $(selectElement).append(new Option(formattedDrug, drugId));
                });
            } else {
                console.error('No drug types or ingredients found');
                $(selectElement).append(new Option('No drug types available', '', true, true));
            }
            $(selectElement).select2();
        },
        onError: function (errorMessage) {
            console.error('Error fetching drug details: ' + errorMessage);
        }
    });
}
$(document).ready(function () {
    apiRequest({
        url: '/PharmacyStock/getDrugTemplateDetails',
        method: 'GET',
        onSuccess: function (response) {
            console.log('Full Response:', response);
            var drugTypeMapping = {
                1: 'Capsule',
                2: 'Cream',
                3: 'Drops',
                4: 'Foam',
                5: 'Gel',
                6: 'Inhaler',
                7: 'Injection',
                8: 'Lotion',
                9: 'Ointment',
                10: 'Powder',
                11: 'Shampoo',
                12: 'Syringe',
                13: 'Syrup',
                14: 'Tablet',
                15: 'Toothpaste',
                16: 'Suspension',
                17: 'Spray',
                18: 'Test'
            };
            var drugSelect = $('#drug_template_0');
            drugSelect.empty();
            drugSelect.append(new Option('Select Drug Type', '', true, true));
            if (
                response &&
                response.drugTemplate &&
                Array.isArray(response.drugTemplate) &&
                response.drugTemplate.length > 0
            ) {
                response.drugTemplate.forEach(function (drug) {
                    var drugName = drug.drug_name || 'Unknown Drug';
                    var drugStrength = drug.drug_strength || 'Unknown Strength';
                    var drugType = drug.drug_type || 0;
                    var drugTypeName = drugTypeMapping[drugType] || 'Unknown Type';
                    var drugId = drug.drug_template_id;
                    var formattedDrug = `${drugName} - ${drugStrength} (${drugTypeName})`;
                    drugSelect.append(new Option(formattedDrug, drugId));
                });
            } else {
                drugSelect.append(new Option('No drug types available', '', true, true));
                console.error('No drug types or ingredients found');
            }
            drugSelect.select2();
        },
        onError: function (errorMessage) {
            console.error('Error fetching drug details: ' + errorMessage);
        }
    });
    const url = window.location.href;
    const prescriptionTemplateId = url.split('/').pop();
    apiRequest({
        url: `/prescription/prescription-editById/${prescriptionTemplateId}`,
        method: 'GET',
        onSuccess: function (data) {
            console.log('Prefilling prescription data:', data);
            if (data.length > 0) {
                $('#template-name').val(data[0].template_name);
                $('.prescription-row:not(:first)').remove();
                data.forEach((prescriptionData, index) => {
                    let row;
                    if (index === 0) {
                        row = $('.prescription-row').first();
                    } else {
                        row = $('.prescription-row').first().clone();
                        row.find('input, select').val('');
                        row.find('.select2').attr('id', 'drug_template_' + index);
                        $('.prescription-inputs').append(row);
                    }
                    let selectElement = row.find('.select2');
                    selectElement.val(prescriptionData.drug_template_id).trigger('change');
                    row.find('input[name="duration[]"]').val(prescriptionData.intake_days);
                    row.find('input[name="morning[]"]').val(prescriptionData.morning);
                    row.find('input[name="afternoon[]"]').val(prescriptionData.afternoon);
                    row.find('input[name="evening[]"]').val(prescriptionData.evening);
                    row.find('input[name="night[]"]').val(prescriptionData.night);
                    row.find('select[name="drugintakecondition[]"]').val(prescriptionData.intake_condition);
                    row.find('input[name="remarks[]"]').val(prescriptionData.remarks);
                });
                $('.select2').each(function () {
                    $(this).select2();
                });
            } else {
                console.error('No prescription data available for this ID');
            }
        },
        onError: function (errorMessage) {
            console.error('Error fetching prescription data: ' + errorMessage);
        }
    });
    $('#submitBtn').on('click', function () {
        const url = window.location.href;
        const prescriptionTemplateId = url.split('/').pop();
        let prescriptionData = [];
        $('.prescription-row').each(function () {
            const rowData = {
                rowid: $(this).find('input[name="rowid[]"]').val(),
                drugname: $(this).find('select[name="hiddendrugname[]"]').val(),
                duration: $(this).find('input[name="duration[]"]').val(),
                morning: $(this).find('input[name="morning[]"]').val(),
                afternoon: $(this).find('input[name="afternoon[]"]').val(),
                evening: $(this).find('input[name="evening[]"]').val(),
                night: $(this).find('input[name="night[]"]').val(),
                drugintakecondition: $(this).find('select[name="drugintakecondition[]"]').val(),
                remarks: $(this).find('input[name="remarks[]"]').val()
            };
            prescriptionData.push(rowData);
        });
        apiRequest({
            url: `/prescription/prescription-update/${prescriptionTemplateId}`,
            method: 'POST',
            data: {
                prescription_data: prescriptionData
            },
            onSuccess: function (response) {
                console.log(response);
                window.location.href = '/prescription/prescription-template';
            },
            onError: function (errorMessage) {
                console.error('Error submitting the form:', errorMessage);
                alert('An error occurred while updating the prescription template.');
            }
        });
    });
});
