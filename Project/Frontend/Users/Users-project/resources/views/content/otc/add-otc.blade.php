@extends('layouts/layoutMaster')
@section('title', 'OTC Details')
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/typeahead-js/typeahead.scss',
'resources/assets/vendor/libs/tagify/tagify.scss',
'resources/assets/vendor/libs/spinkit/spinkit.scss',
'resources/assets/vendor/libs/animate-css/animate.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
])
@endsection
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/typeahead-js/typeahead.js',
'resources/assets/vendor/libs/tagify/tagify.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js',
'resources/assets/vendor/libs/bloodhound/bloodhound.js'
])
@endsection
@section('page-script')
@vite([
'resources/assets/js/form-validation.js',
'resources/assets/js/forms-selects.js',
'resources/assets/js/extended-ui-sweetalert2.js',
'resources/assets/js/forms-tagify.js',
'resources/assets/js/forms-typeahead.js'
])
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    .non-otc-drug .drug-row {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .drug-name {
        flex: 0 0 51%;
        font-weight: bold;
    }

    .drug-availability {
        flex: 0 0 28%;
        color: green;
        padding-left: 10px;
    }

    .drug-issue {
        flex: 0 0 15%;
    }

    .drug-issue input {
        width: 90%;
        border: 1px solid #dfdfe3;
        border-radius: 6px;
        height: 35px;
    }

    .prescription-header {
        display: flex;
        justify-content: space-between;
        border-top: 1px dashed #4444e5;
        border-bottom: 1px dashed #4444e5;
        color: #4444e5;
        padding: 8px 0;
        font-weight: bold;
    }

    .prescription-header>div:nth-child(1) {
        width: 23%;
    }

    .prescription-header>div:nth-child(2) {
        width: 8%;
    }

    .prescription-header>div:nth-child(3) {
        width: 15%;
    }
.text-danger {
    color: #dc3545;
    font-size: 13px;
    margin-top: 2px;
}

    /* Primary color for First-Aid */
    .first-aid-radio:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    /* Warning color for Moderate */
    .moderate-radio:checked {
        background-color: #ffc107;
        border-color: #ffc107;
    }

    /* Danger color for Severe */
    .severe-radio:checked {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .hidden {
        display: none;
    }

    /* Centering the Spinner Container */
    .add-registry-spinner {
        display: flex;
        justify-content: center;
        align-items: center;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1050;
    }

    /* Spinner Card Background */
    .spinner-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-width: 180px;
        text-align: center;
    }

    /* Existing Spinner Styles */
    .spinner-container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .sk-bounce {
        display: flex;
        justify-content: space-between;
        width: 50px;
    }

    .sk-bounce-dot {
        width: 30px;
        height: 30px;
        margin: 0 5px;
        background-color: #007bff;
        border-radius: 50%;
        animation: sk-bounce 1.4s infinite ease-in-out both;
    }

    @keyframes sk-bounce {

        0%,
        80%,
        100% {
            transform: scale(0);
            opacity: 0.3;
        }

        40% {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>
<script>
    var employeeData = <?php echo json_encode($employeeData); ?>;
</script>
<?php
// print_r($employeeData);
?>
<div class="add-registry-spinner" id="add-registry-spinner" style="display: block;">
    <div class="spinner-card">
        <div class="spinner-container">
            <div class="sk-bounce sk-primary">
                <div class="sk-bounce-dot"></div>
                <div class="sk-bounce-dot"></div>
            </div>
            <label id="spinnerLabeltext">Retrieving data...</label>
        </div>
    </div>
</div>
<div style="display: none;" id="add-registry-card">
    <div class="card mb-4">
        <div class="card-header text-white p-2 border-0 rounded-top" style="background-color: #6B1BC7;">
            <div class="row">
                <div class="col-md-6 d-flex position-relative">
                    <div class="me-3 d-flex align-items-center">
                        <img src="https://t3.ftcdn.net/jpg/01/65/63/94/360_F_165639425_kRh61s497pV7IOPAjwjme1btB8ICkV0L.jpg"
                            alt="Profile" class="rounded" width="60">
                    </div>
                    <div class="d-flex flex-column justify-content-center">
                        <h6 class="text-warning mb-1" style="color:#ffff00 !important;">
                            <input type="hidden" name="employeeId" id="employeeId"
                                value="{{ $employeeData['employee_id'] ?? '' }}">
                            <input type="hidden" name="isOutPatientAdded" id="isOutPatientAdded"
                                value="{{ $employeeData['employee_is_outpatient_added'] ?? '' }}">
                            <input type="hidden" name="isOutPatientAddedAndOpen" id="isOutPatientAddedAndOpen"
                                value="{{ $employeeData['employee_is_outpatient_open'] ?? '' }}">
                            {{ strtoupper($employeeData['employee_firstname'] ??
                            '')
                            }}
                            {{ strtoupper($employeeData['employee_lastname'] ??
                            '')
                            }} -
                            {{ $employeeData['employee_id'] ?? '' }}
                        </h6>
                        <p class="mb-1">
                            {{ $employeeData['employee_age'] ?? 'N/A' }} / {{
                            $employeeData['employee_gender'] ?? 'N/A' }}
                        </p>
                        <p class="mb-0">
                            {{
                            ucwords(strtolower($employeeData['employee_designation']
                            ?? 'N/A')) }},
                            {{
                            ucwords(strtolower($employeeData['employee_department']
                            ?? 'N/A')) }}
                        </p>
                    </div>
                    <div
                        class="position-absolute end-0 top-0 bottom-0 d-flex flex-column justify-content-between me-4 py-2">
                        <button type="button" class="btn btn-sm"
                            style="background-color: #ffffff; color: #6a0dad; border: none; padding: 8px 16px; border-radius: 8px; font-weight: bold;">
                            Edit Profile
                        </button>
                        <div>
                            <p class="mb-0 text-end">
                                {{ $employeeData['employee_corporate_name'] ??
                                'N/A'
                                }},
                                {{ $employeeData['employee_location_name'] ??
                                'N/A'
                                }}
                            </p>
                        </div>
                    </div>
                    <div class="position-absolute end-0 top-0 bottom-0 border-end border-light"></div>
                </div>
                <div class="col-md-6">
                    <div class="ps-md-4 d-flex flex-column justify-content-center h-100">
                        <p class="mb-2">
                            <strong>Conditions:</strong>
                            {{ !empty($employeeData['healthParameters']['Published Conditions']) 
                                ? implode(', ', $employeeData['healthParameters']['Published Conditions']) 
                                : 'N/A' }}
                        </p>
                        <p class="mb-2">
                            <strong>Allergy Ingredients:</strong>
                            {{ !empty($employeeData['healthParameters']['Allergic Ingredients']) 
                                ? implode(', ', $employeeData['healthParameters']['Allergic Ingredients']) 
                                : 'N/A' }}
                        </p>
                        <p class="mb-0">
                            <strong>Food Allergy:</strong>
                            {{ !empty($employeeData['healthParameters']['Allergic Foods']) 
                                ? implode(', ', $employeeData['healthParameters']['Allergic Foods']) 
                                : 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body pt-3">
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label">Reporting Date & Time</label>
                    <input class="form-control form-control-sm" type="datetime-local" id="reporting-datetime" />
                </div>

                <div class="col-md-3">
                    <label class="form-label">Work Shift</label>
                    <select class="form-select form-select-sm" id="workShift">
                        <option value="4" selected>General Shift</option>
                        <option value="1">First Shift</option>
                        <option value="2">Second Shift</option>
                        <option value="3">Third Shift</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">First Aid by</label>
                    <input type="text" class="form-control form-control-sm" placeholder="John Doe"
                        value="{{ $employeeData['employee_firstname'] . ' ' . $employeeData['employee_lastname'] }}"
                        id="firstAidBy" />
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">Medical Details</h5>
                <button class="btn btn-outline-primary" id="viewPastData">View Past Data</button>
            </div>
        </div>
    </div>
<input type="hidden" name="user_id" id="user_id" value="{{ $employeeData['employee_id'] ?? '' }}">

</div>
<div class="col-md-12">

<div class="col-md-2" style="line-height:10px;">
                <p style="color: #7367f0 !important; font-weight: bold; margin-left: 23px;">Pharmacy
                    to issue</p>
                <select name="fav_pharmacy" id="fav_pharmacy" class="form-select" style="margin-left: 22px;    width: 180px;
    height: 35px;"> 
                    @foreach($pharmacyData['data'] as $pharmacy)
                    @if($pharmacy['active_status'] == 1 &&
                    $pharmacy['main_pharmacy'] != 1)
                    <!-- Exclude main pharmacy -->
                    <option value="{{ $pharmacy['ohc_pharmacy_id'] }}">{{
                        $pharmacy['pharmacy_name'] }}</option>
                    @endif
                    @endforeach
                </select>
            </div>
    <div class="card shadow-sm" id="medicalFields">
        <div class="card-body">
            <div class="row">
                <!-- Symptoms -->
                <div class="col-md-4">
                    <label class="form-label text-primary mb-2" style="font-size: 15px;">
                        <strong>Symptoms</strong>
                    </label>
                    <div class="select2-primary symptoms">
                        <select id="select2Primary_symptoms" class="select2 form-select" multiple></select>
                        <div class="text-danger" id="symptomsError" style="font-size: 13px;"></div>
                    </div>
                    
                </div>

                <!-- Medical System -->
                <div class="col-md-4">
                    <label class="form-label text-primary mb-2" style="font-size: 15px;">
                        <strong>Medical System</strong>
                    </label>
                    <div class="select2-primary medical-system">
                        <select id="select2Primary_medical_system" class="select2 form-select" multiple></select>
                        <div class="text-danger" id="systemError" style="font-size: 13px;"></div>
                    </div>
                </div>

                <!-- Doctor Notes -->
                <div class="col-md-4">
                    <label class="form-label text-primary mb-2" style="font-size: 15px;">
                        <strong>Remarks</strong>
                    </label>
                    <textarea class="form-control" rows="1" id="doctorNotes"></textarea>
                    <div class="text-danger" id="remarksError" style="font-size: 13px;"></div>
                </div>



            </div>   <div class="prescription-inputs"
                style="display: flex; flex-direction: column; gap: 15px; padding: 16px 0; border-bottom: 2px #d1d0d4 dashed; font-weight: bold;">
                <div class="prescription-header" style="display: flex; justify-content: space-between;
                    border-top: 1px dashed #4444e5;
                    border-bottom: 1px dashed #4444e5;
                    color: #4444e5;
                    padding: 8px 0;
                    font-weight: bold;
                ">
                                    <div style="width: 23%;">Drug Name - Type - Strength</div>
                    <div style="width: 8%;">Available</div>
                    <div style="width: 15%;">Issue</div>
                </div>
                <div id="non_otc_drugs_div" style="line-height:35px;border-bottom:1px solid;">
                    <h4>Non-OTC Drugs</h4>
                    <!-- Non-OTC drugs will be appended here -->
                    <span id="result_0" style="text-align: center; width: 100%;">0</span>

                </div>
                <!-- First Prescription Row -->
                <div class="prescription-row" style="display: flex; align-items: center; gap: 10px;">
                    <!-- Drug Name Input -->
                    <input type="hidden" name="rowid[]" id="rowid" value="0">
                    <div style="width: 35%;">
                        <div class="drug_name" title="drug_name">
                            <select class="hiddendrugname select2" name="drug_template_id[]" id="drug_template_0"
                                style="height:25px;width:85%;font-weight:normal;color:#acaab1 !important;">
                                <option value>Select a Drug</option>
                                <!-- Add drug options dynamically -->
                            </select>
                        </div>
                    </div>
                    <div style="width: 26%;margin-left:160px;">

                        <span id="result_0"></span>
                    </div>
                    <!-- Remarks Input -->
                    <div style="width: 15%;">
                        <input type="text" class="form-control" name="issue[]" placeholder="Issue"
                            style="width:90%; height:36px!important; flex: 0 0 15%;">
                  <div class="text-danger prescription-error" style="font-size: 13px;"></div>          
                    </div>

                    <!-- Buttons for Add/Remove Rows -->
                    <div style="width: 5%; text-align: center;">
                        <div style="cursor: pointer;" class="margin-t-8 addjs" onclick="addRow1()">
                            <i class="fa-sharp fa-solid fa-square-plus"></i>
                            <!-- Only plus in the first row -->
                        </div>
                    </div> 
   
                </div>


            </div>




        </div><div class="btn btn-secondary add-new btn-primary waves-effect waves-light ms-auto" style="text-align: right; margin-top: 10px;height: 43px;
    width: 114px;margin-right:20px;">
    <input class="btn btn-secondary add-new btn-primary waves-effect waves-light ms-auto" type="button" name="submit_otc" value="Save OTC">
</div><br/>

    </div>

   
   
</div>

</div>

</div>

</div>
</div>


<script>
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

</script>
@endsection