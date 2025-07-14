@extends('layouts/layoutMaster')

@section('title', 'Prescription Template - Forms')

<!-- Vendor Styles -->
@section('vendor-style')
@vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite(['resources/assets/vendor/libs/bs-stepper/bs-stepper.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="col-12 mb-6">
    <div id="wizard-validation" class="bs-stepper mt-2">       
        <div class="bs-stepper-content">
        <div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
                <button type="button" class="btn btn-primary" id="back-to-list" onclick="window.location.href='/prescription/prescription-template'" style="margin-right: 20px;">Back to List</button>
            </div>
            <!-- Form for Template Name -->
            <div class="template-name-field" style="padding: 10px 0;">
                <label for="template-name" >Template Name:</label>
                <input type="text" id="template-name" name="template-name" class="form-control" placeholder="Enter Template Name" style="width: 100%; max-width: 308px;">
            </div>

            <!-- Prescription Input Section using div layout -->
            <div class="prescription-inputs" style="display: flex; flex-direction: column; gap: 15px; padding: 5px 0; border-bottom: 1px #333 solid; font-weight: bold;">
                <div class="prescription-header" style="display: flex; justify-content: space-between;">
                    <div style="width: 35%;">Drug Name - Type - Strength</div>
                    <div style="width: 5%;">Days</div>
                    <div style="width: 30%;">
                        <div style="display: inline-block; text-align: center; width: 50px; margin: 0 5px;">
                            <img src="https://www.hygeiaes.co/img/Morning.png">
                        </div>
                        <div style="display: inline-block; text-align: center; width: 50px; margin: 0 5px;">
                            <img src="https://www.hygeiaes.co/img/Noon.png">
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

                <!-- First Prescription Row -->
                <div class="prescription-row" style="display: flex; align-items: center; gap: 10px;">
    <!-- Drug Name Input -->
    <input type="hidden" name="rowid[]" id="rowid" value="0">
    <div style="width: 35%;">      
        <div class="drug_name" title="drug_name">           
          <select class="hiddendrugname select2" name="drugname[]" id="drug_template_0" style="height:25px;width:85%;">    
                <option value="">Select a Drug</option>
                            <!-- Add drug options dynamically -->
            </select>
        </div>
    </div>

    <!-- Days Input -->
    <div style="width: 5%;">
        <input type="text" class="form-control" maxlength="3" name="duration[]" id="duration" placeholder="Days" onkeypress="return ValidNumber(event)" style="width:65px;">
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
        <div style="cursor: pointer;" class="margin-t-8 addjs" onclick="addRow1()">
            <i class="fa-sharp fa-solid fa-square-plus"></i> <!-- Only plus in the first row -->
        </div>
    </div>
</div>

            </div>
        </div>
        <div class="col-12 mt-3 text-end" style="margin-left:-20px;">
    <button type="button" id="submitBtn" class="btn btn-primary">Submit Prescription</button>
</div><br/>
    </div>
</div>
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
function addRow1() {
    var container = document.querySelector('.prescription-inputs');
    
    // Calculate the next row id
    var rowCount = container.querySelectorAll('.prescription-row').length;

    // Create a new row as a string
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

    // Append the new row to the container
    container.insertAdjacentHTML('beforeend', newRow);

    // Fetch the dynamic options for the drug select dropdown after appending
    var newSelectElement = document.querySelector(`#drug_template_${rowCount}`);
    
    // Apply Select2 to the new select element
    $(newSelectElement).select2();

    // Fetch and add drug options to the new select dropdown
    fetchDrugOptions(newSelectElement);
}

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
                1: "Capsule", 2: "Cream", 3: "Drops", 4: "Foam", 5: "Gel", 6: "Inhaler",
                7: "Injection", 8: "Lotion", 9: "Ointment", 10: "Powder", 11: "Shampoo",
                12: "Syringe", 13: "Syrup", 14: "Tablet", 15: "Toothpaste", 16: "Suspension",
                17: "Spray", 18: "Test"
            };

            if (response && response.drugTemplate && Array.isArray(response.drugTemplate) && response.drugTemplate.length > 0) {
                var defaultOption = document.createElement('option');
                defaultOption.text = 'Select Drug Type';
                defaultOption.value = '';
                defaultOption.selected = true;
                selectElement.appendChild(defaultOption);

                response.drugTemplate.forEach(function(drug) {
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
        
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        // Fetch drug types and ingredients on page load
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

                if (response && response.drugTemplate && Array.isArray(response.drugTemplate) && response.drugTemplate.length > 0) {
                    var drugSelect = $('#drug_template_0');
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

        
        $('#submitBtn').click(function(e) {
        e.preventDefault(); // Prevent the form from submitting normally

        // Validate template name
        var templateName = $('#template-name').val().trim();
        if (templateName === '') {
            alert('Template Name is required!');
            return;
        }

        // Validate prescription rows
        var valid = true;
        $('.prescription-row').each(function() {
            var drugName = $(this).find('select[name="drugname[]"]').val();
            var duration = $(this).find('input[name="duration[]"]').val();
            var morning = $(this).find('input[name="morning[]"]').val();
            var afternoon = $(this).find('input[name="afternoon[]"]').val();
            var evening = $(this).find('input[name="evening[]"]').val();
            var night = $(this).find('input[name="night[]"]').val();
            var afbf = $(this).find('select[name="drugintakecondition[]"]').val();

            if (!drugName || !duration || !afbf || morning === "" || afternoon === "" || evening === "" || night === "") {
                valid = false;
                return false; // Exit loop on invalid row
            }
        });

        if (!valid) {
            alert('Please fill out all prescription fields.');
            return;
        }

        // Collect the form data
        var formData = {
            _token: csrfToken,
            template_name: templateName,
            prescriptions: []
        };

        $('.prescription-row').each(function() {
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

        // Submit data via AJAX
        $.ajax({
            url: "{{ route('prescription.store') }}", // Backend route to store prescription
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success('Prescription submitted successfully!', 'Success');
                    window.location.href = '/prescription/prescription-template';
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error submitting prescription: ' + error);
                alert('Something went wrong. Please try again.');
            }
        });
    });
        
    });

  

</script>
