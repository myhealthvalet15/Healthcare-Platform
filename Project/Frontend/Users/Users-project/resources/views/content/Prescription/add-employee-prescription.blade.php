@extends('layouts.layoutMaster')

@section('title', 'Add Employee Prescription')

@section('vendor-style')
@vite([
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/typeahead-js/typeahead.scss',
'resources/assets/vendor/libs/tagify/tagify.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
])
@endsection

@section('vendor-script')
@vite([
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/typeahead-js/typeahead.js',
'resources/assets/vendor/libs/tagify/tagify.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js',
'resources/assets/vendor/libs/bloodhound/bloodhound.js',
])
@endsection

@section('page-script')
@vite([
'resources/assets/js/form-validation.js',
'resources/assets/js/forms-selects.js',
'resources/assets/js/forms-tagify.js',
'resources/assets/js/forms-typeahead.js',
])
@endsection

@section('content')
@php
$opRegistryData = $employeeData['op_registry_datas'] ?? [];
$body_part = $opRegistryData['body_parts'] ?? [];
$symptoms = $opRegistryData['symptoms'] ?? [];
$diagnosis = $opRegistryData['diagnosis'] ?? [];
$medical_systems = $opRegistryData['medical_systems'] ?? [];
$mechanism_injuries = $opRegistryData['mechanism_injuries'] ?? [];
$body_side = $opRegistryData['op_registry']['body_side'] ?? [];
$site_of_injury = $opRegistryData['op_registry']['site_of_injury'] ?? [];
$nature_injuries = $opRegistryData['nature_injuries'] ?? [];

function safeJsonDecode($data) {
if (is_string($data)) {
$decoded = json_decode($data, true);
return $decoded !== null ? $decoded : $data;
}
return $data;
}
$body_part = safeJsonDecode($body_part);
$symptoms = safeJsonDecode($symptoms);
$diagnosis = safeJsonDecode($diagnosis);
$medical_systems = safeJsonDecode($medical_systems);
$mechanism_injuries = safeJsonDecode($mechanism_injuries);
$body_side = safeJsonDecode($body_side);
$site_of_injury = safeJsonDecode($site_of_injury);
$nature_injuries = safeJsonDecode($nature_injuries);
$body_part = is_array($body_part) ? $body_part : [$body_part];
$symptoms = is_array($symptoms) ? $symptoms : [$symptoms];
$diagnosis = is_array($diagnosis) ? $diagnosis : [$diagnosis];
$medical_systems = is_array($medical_systems) ? $medical_systems :
[$medical_systems];
$mechanism_injuries = is_array($mechanism_injuries) ? $mechanism_injuries :
[$mechanism_injuries];
$site_of_injury = safeJsonDecode($site_of_injury) ? $site_of_injury :
[$site_of_injury];
$nature_injuries = is_array($nature_injuries) ? $nature_injuries :
[$nature_injuries];
$referal_type = $opRegistryData['op_registry']['type_of_incident'] ?? [];
@endphp
<style>
    .medical-info-row {
        display: flex;
        width: 100%;
        padding: 0;
        margin: 0;
    }

    .medical-info-column {
        flex: 1;
        padding: 0 15px;
        box-sizing: border-box;
    }

    .medical-info-column:first-child {
        padding-left: 35px;
    }

    .info-title {
        color: #6B1BC7;
        margin-bottom: 5px;
    }

    .info-content {
        margin-top: 0;
    }

    .medical-info-wrapper {
        position: relative;
        background: #fff;
        margin-top: -20px;
        border-radius: 12px;
        display: flex;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        padding: 15px;
        align-items: center;
    }

    .severity-indicator-container {
        width: 50px;
        display: flex;
        justify-content: center;
        margin-right: 10px;
    }

    .severity-indicator {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        font-weight: bold;
        color: #000;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
    }

    .medical-info-row {
        display: flex;
        flex-wrap: nowrap;
        flex: 1;
    }

    .medical-info-column {
        flex: 1;
        padding-right: 10px;
    }

    .info-title {
        color: #6946C6;
        font-size: 14px;
        margin-bottom: 4px;
        margin-top: 0;
    }

    .info-content {
        font-size: 14px;
        margin-top: 0;
        margin-bottom: 0;
    }
</style>



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
                        {{ strtoupper($employeeData['employee_firstname'] ?? '')
                        }}
                        {{ strtoupper($employeeData['employee_lastname'] ?? '')
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
                    <div>

                        <input type="text" id="prescription_date" name="prescription_date"
                            class="form-control form-control-sm" placeholder="Select Date"
                            style="width: 129px; margin-left: 74px; background-color: #fff;">

                    </div>
                    <div>
                        <p class="mb-0 text-end">
                            {{ $employeeData['employee_corporate_name'] ?? 'N/A'
                            }},
                            {{ $employeeData['employee_location_name'] ?? 'N/A'
                            }}
                        </p>
                    </div>
                </div>
                <div class="position-absolute end-0 top-0 bottom-0 border-end border-light"></div>
            </div>
            <div class="col-md-6">
                <div class="ps-md-4 d-flex flex-column justify-content-center h-100">
                    <p class="mb-2">Conditions: N/A</p>
                    <p class="mb-2">Drug Allergy: N/A</p>
                    <p class="mb-0">Food Allergy: N/A</p>
                </div>
            </div>
        </div>
    </div>
    <br />
    @php
    $severityData = $employeeData['incidentTypeColorCodesAdded'] ?? '';
    $severityParts = explode('_', $severityData);
    $severityText = !empty($severityParts[0]) ? substr($severityParts[0], 0, 2)
    : 'N/A';
    $colorCode = !empty($severityParts[1]) ? trim($severityParts[1]) :
    '#87CEEB';
    @endphp
    @if($employeeData['showWhiteStrip'] == 1)
    <div class="medical-info-wrapper">
        @if($referal_type == 'industrialAccident' || $referal_type ==
        'outsideAccident')
        <div class="severity-indicator-container">
            <div class="severity-indicator" style="background-color: {{ $colorCode }};">
                {{ strtoupper($severityText) }}
            </div>
        </div>
        @endif
        <div class="medical-info-row">
            <div class="medical-info-column">
                <p class="info-title">Body Part</p>
                <p class="info-content">{{ implode(', ', $body_part) }}</p>
            </div>

            @if($referal_type == 'medicalIllness')
            <div class="medical-info-column">
                <p class="info-title">Symptoms</p>
                <p class="info-content">{{ implode(', ', $symptoms) }}</p>
            </div>
            <div class="medical-info-column">
                <p class="info-title">Medical System</p>
                <p class="info-content">{{ implode(', ', $medical_systems)
                    }}</p>
            </div>
            <div class="medical-info-column">
                <p class="info-title">Diagnosis</p>
                <p class="info-content">{{ implode(', ', $diagnosis) }}</p>
            </div>
            @elseif($referal_type == 'industrialAccident')
            <div class="medical-info-column">
                <p class="info-title">Mechanism Injuries</p>
                <p class="info-content">{{ implode(', ', $mechanism_injuries)
                    }}</p>
            </div>
            <div class="medical-info-column">
                <p class="info-title">Nature Injuries</p>
                <p class="info-content">{{ implode(', ', $nature_injuries)
                    }}</p>
            </div>
            <div class="medical-info-column">
                <p class="info-title">Site of Injury</p>
                <p class="info-content">{{ implode(', ', array_map(fn($s) =>
                    ucwords(str_replace(['shopFloor',
                    'nonShopFloor'], ['Shop Floor', 'Non Shop Floor'], $s)),
                    array_keys(array_filter((array)
                    $site_of_injury)))) }}</p>
            </div>
            <div class="medical-info-column">
                <p class="info-title">Body Side</p>
                <p class="info-content">{{ implode(', ', array_map('ucwords',
                    array_keys(array_filter((array)
                    $body_side)))) }}</p>
            </div>
            @elseif($referal_type == 'outsideAccident')
            <div class="medical-info-column">
                <p class="info-title">Mechanism Injuries</p>
                <p class="info-content">{{ implode(', ', $mechanism_injuries)
                    }}</p>
            </div>
            <div class="medical-info-column">
                <p class="info-title">Nature Injuries</p>
                <p class="info-content">{{ implode(', ', $nature_injuries)
                    }}</p>
            </div>
            <div class="medical-info-column">
                <p class="info-title">Body Side</p>
                <p class="info-content">{{ implode(', ', array_map('ucwords',
                    array_keys(array_filter((array)
                    $body_side)))) }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>


<div class="row mb-4"> 
    @if (session('user_type') != 'MasterUser')
    <div class="col-md-4" style="color:#7367f0 !important;font-weight:bold;margin-left: 14px;width:200px;">
        Prescription details
    </div>
    @endif

    @if (session('user_type') != 'MasterUser')
    <div class="col-md-5 d-flex justify-content">
        <select class="form-select form-select-sm" id="prescriptionTemplate" style="width:50%;">
            <option value selected>Select Drug Template</option>
            @foreach($prescriptionTemplates as $template)
                <option value="{{ $template['prescription_template_id'] ?? '' }}">
                    {{ $template['template_name'] ?? 'N/A' }}
                </option>
            @endforeach
        </select>

        <div style="cursor: pointer; margin-left: 14px;margin-top:4px;" onclick="resetForm()">
            <i class="fa fa-refresh" style="font-size: 20px; color: #007bff;"></i>
        </div>
    </div>
@endif
<input type="hidden" name="emp_id" id="emp_id" value>

<div class="col-md-12">
    <!-- Medical Illness Fields -->
    <div class="card shadow-sm" id="medicalFields">
        <div class="card-body">
             @if (session('user_type') != 'MasterUser')

            <div class="prescription-inputs"
                style="display: flex; flex-direction: column; gap: 15px; padding: 16px 0; border-bottom: 2px #d1d0d4 dashed; font-weight: bold;">
                <div class="prescription-header" style="display: flex; justify-content: space-between;">
                    <div style="width: 23%;">Drug Name - Type - Strength</div>
                    <div style="width: 8%;">Available</div>
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
                    <div style="width: 31%;">
                        <div class="drug_name" title="drug_name">
                            <select class="hiddendrugname select2" name="drug_template_id[]" id="drug_template_0"
                                style="height:25px;width:85%;font-weight:normal;color:#acaab1 !important;">
                                <option value>Select a Drug</option>
                                <!-- Add drug options dynamically -->
                            </select>
                        </div>
                    </div>
                    <div style="width: 5%;">

                        <span id="result_0"></span>
                    </div>
                    <!-- Days Input -->
                    <div style="width: 5%;">
                        <input type="text" class="form-control" maxlength="3" name="duration[]" id="duration"
                            placeholder="Days" onkeypress="return ValidNumber(event)" style="width:65px;">
                    </div>

                    <!-- Morning, Noon, Evening, Night Inputs -->
                    <div style="width: 30%;margin-left:20px;">
                        <div style="float:left;width: 60px;">
                            <input type="text" maxlength="2" name="morning[]" class="form-control" placeholder="0"
                                onkeypress="return ValidNumber(event)"
                                style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                        </div>
                        <div style="float:left;width: 60px;">
                            <input type="text" maxlength="2" name="afternoon[]" class="form-control" placeholder="0"
                                onkeypress="return ValidNumber(event)"
                                style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                        </div>
                        <div style="float:left;width: 60px;">
                            <input type="text" maxlength="2" name="evening[]" class="form-control" placeholder="0"
                                onkeypress="return ValidNumber(event)"
                                style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                        </div>
                        <div style="float:left;width: 60px;">
                            <input type="text" maxlength="2" name="night[]" class="form-control" placeholder="0"
                                onkeypress="return ValidNumber(event)"
                                style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                        </div>
                    </div>

                    <!-- AF/BF Select -->
                    <div style="width: 15%;text-align:center;">
                        <select name="drugintakecondition[]" class="form-select">
                            <option value>-Select-</option>
                            <option value="1">Before Food</option>
                            <option value="2">After Food</option>
                            <option value="3">With Food</option>
                            <option value="4">SOS</option>
                            <option value="5">Stat</option>
                        </select>
                    </div>

                    <!-- Remarks Input -->
                    <div style="width: 15%;">
                        <input type="text" class="form-control" name="remarks[]" placeholder="Remarks"
                            style="width:90%; height:36px!important;">
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
            @endif
            <br />
            <div class="prescription-output"
                style="display: flex; flex-direction: column; gap: 15px; padding: 16px 0; border-bottom: 1px #6B1BC7 solid; font-weight: bold;">
              
                <div class="prescription-header" style="display: flex; justify-content: space-between;">
                    <div style="width: 35%;color:#7367f0 !important;">Outside
                        Prescription</div>
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
                <div class="prescription-row-output" style="display: flex; align-items: center; gap: 10px;">
                    <!-- Drug Name Input -->
                    <input type="hidden" name="rowid[]" id="rowid" value="0">
                    <div style="width: 27%;">
                        <div class="drug_name" title="drug_name">
                            <input type="text" name="drugname[]" placeholder="Drug Name - Type - Strength"
                                style="padding:5px;width:290px;height:37px;border:1px solid #d1d0d4;">
                        </div>
                    </div>
                    <div style="width: 5%;">
                    </div>
                    <!-- Days Input -->
                    <div style="width: 5%;">
                        <input type="text" class="form-control" maxlength="3" name="duration[]" id="duration"
                            placeholder="Days" onkeypress="return ValidNumber(event)" style="width:65px;">
                    </div>

                    <!-- Morning, Noon, Evening, Night Inputs -->
                    <div style="width: 30%;margin-left:20px;">
                        <div style="float:left;width: 60px;">
                            <input type="text" maxlength="2" name="morning[]" class="form-control" placeholder="0"
                                style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                        </div>
                        <div style="float:left;width: 60px;">
                            <input type="text" maxlength="2" name="afternoon[]" class="form-control" placeholder="0"
                                style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                        </div>
                        <div style="float:left;width: 60px;">
                            <input type="text" maxlength="2" name="evening[]" class="form-control" placeholder="0"
                                style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                        </div>
                        <div style="float:left;width: 60px;">
                            <input type="text" maxlength="2" name="night[]" class="form-control" placeholder="0"
                                style="width:50px; text-align:center;margin-right: 8px;height:35px;">
                        </div>
                    </div>

                    <!-- AF/BF Select -->
                    <div style="width: 15%;text-align:center;">
                        <select name="drugintakecondition[]" class="form-select">
                            <option value>-Select-</option>
                            <option value="1">Before Food</option>
                            <option value="2">After Food</option>
                            <option value="3">With Food</option>
                            <option value="4">SOS</option>
                            <option value="5">Stat</option>
                        </select>
                    </div>

                    <!-- Remarks Input -->
                    <div style="width: 15%;">
                        <input type="text" class="form-control" name="remarks[]" placeholder="Remarks"
                            style="width:90%; height:36px!important;">
                    </div>

                    <!-- Buttons for Add/Remove Rows -->
                    <div style="width: 5%; text-align: center;">
                        <div style="cursor: pointer;" class="margin-t-8 addjs" onclick="addOutside()">
                            <i class="fa-sharp fa-solid fa-square-plus"></i>
                            <!-- Only plus in the first row -->
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <div class="col-md-12"
            style="display: flex;margin-top: -14px; align-items: center; justify-content: space-between;">
             @if (session('user_type') != 'MasterUser')        
            <div class="col-md-2" style="line-height:10px;">
                <p style="color: #7367f0 !important; font-weight: bold; margin-left: 23px;">Pharmacy
                    to issue</p>
                <select name="fav_pharmacy" class="form-select" style="margin-left: 22px;    width: 180px;
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
            @endif
            
            
            <div class="col-md-2" style="margin-left: 50px;">
                 @if (session('user_type') != 'MasterUser')

                <p> <input type="checkbox" name="share_patient">&nbsp;Share with
                    Patient</p>
                <p style="margin-bottom: 10px;"> <input type="checkbox" name="share_patient">&nbsp;Send Mail To Patient
                </p>
                @endif
            </div>
            <div class="col-md-3" style="margin-left:-10px;">
                <textarea name="doctorNotes" style="padding: 7px;
   
    width: 270px;
    border: 1px solid #d1d0d4;margin-left: -25px;" class="form-control">Doctor Notes</textarea><br />
                <button class="btn btn-primary me-2" id="addTest">Add
                    Test</button>
            </div>
            <script>
                var opRegistryId = "{{ $employeeData['op_registry_datas']['op_registry']['op_registry_id'] ?? 0 }}";
            </script>

            <script>
                var employeeData = <? php echo json_encode($employeeData); ?>;
                var isTestAdded = employeeData.isTestAdded;
                if (isTestAdded) {
                    $('#addTest').text('View Test');
                    $('#addTest').removeClass('btn-warning');
                    $('#addTest').addClass('btn-secondary');
                }
            </script>
            <div class="col-md-3">
                <textarea class="form-control" name="patientNotes" style="padding: 7px;
    
    width: 270px;
    border: 1px solid #d1d0d4;margin-left:-33px;">Patient Notes</textarea><br />
                <a class="btn btn-secondary add-new btn-primary waves-effect waves-light" id="add_prescription"
                    style="margin-left:-30px;">
                    <span><span style="color:#fff;">Add
                            Prescription</span></span>
                </a>
            </div>

        </div>
        <br />
        <br />
    </div>
</div>
<script>
    function addRow1(isPrefilling = false, lastRowId = 0) {
        var container = document.querySelector('.prescription-inputs');

        // Determine the next row id based on whether we are pre-filling or not
        var rowCount = isPrefilling ? lastRowId + 1 : container.querySelectorAll('.prescription-row').length + 1;

        // Create a new row with a unique ID for the select dropdown
        var newRow = `
        <div class="prescription-row" style="display: flex; align-items: center; gap: 10px;">
            <!-- Drug Name Input -->
            <input type="hidden" name="rowid[]" value="${rowCount}">
            
            <div style="width: 31%;">      
                <div class="drug_name" title="drug_name">           
                    <select class="hiddendrugname select2" name="drug_template_id[]" id="drug_template_${rowCount}" style="height:25px;width:85%;font-weight:normal;">    
                        <option value="">Select a Drug</option>
                        <!-- Add drug options dynamically -->
                    </select>
                </div>
            </div>
            <div style="width: 5%;"><span id="result_${rowCount}"></span></div>
            <!-- Days Input -->
            <div style="width: 5%;">
                <input type="text" class="form-control" maxlength="3" name="duration[]" placeholder="Days" onkeypress="return ValidNumber(event)" style="width:65px;">
            </div>

            <!-- Morning, Noon, Evening, Night Inputs -->
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
        Array.from(selects).forEach(function (select) {
            var selectedValue = select.value;

            // Check if a valid drug is selected and if it has already been selected
            if (selectedValue && selectedDrugs.includes(selectedValue)) {
                // If the drug has already been selected, alert the user
                alert('This drug has already been selected. Please choose another drug.');
                select.value = '';  // Clear the selection
                $(select).trigger('change');  // Trigger change to refresh the select2 UI
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
            success: function (response) {
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

                    response.drugTemplate.forEach(function (drug) {
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
            error: function (xhr, status, error) {
                console.error('Error fetching drug details: ' + error);
            }
        });
    }

    function addOutside() {
        var container = document.querySelector('.prescription-output');

        // Calculate the next row id
        var rowCount = container.querySelectorAll('.prescription-row-output').length;

        // Create a new row as a string
        var newRow = `
        <div class="prescription-row-output" style="display: flex; align-items: center; gap: 10px;">
            <!-- Drug Name Input -->
            <input type="hidden" name="rowid[]" value="${rowCount}">
            <div style="width: 27%;">      
                <div class="drug_name" title="drug_name">
                           <input type="text" name="drugname[]" placeholder="Drug Name - Type - Strength" style="padding:5px;width:290px;height:37px;border:1px solid #d1d0d4;">
                        </div>
            </div>
            <div style="width: 5%;">
           </div>
            <!-- Days Input -->
            <div style="width: 5%;">
                <input type="text" class="form-control" maxlength="3" name="duration[]" placeholder="Days"  style="width:65px;">
            </div>

            <!-- Morning, Noon, Evening, Night Inputs -->
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
                <div style="cursor: pointer;" class="margin-t-8" onclick="deleteRowoutpatient(this)">
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

    function deleteRowoutpatient(btn) {
        var row = btn.closest('.prescription-row-output');
        row.remove();
    }

    $(document).ready(function () {


        flatpickr("#prescription_date", {
            dateFormat: "d-m-Y",
            maxDate: "today", // disables future dates
            defaultDate: "today", // sets default to current date
        });

        function ValidNumber(event) {
            // Allow only numbers and control keys
            const key = event.key;
            return /\d/.test(key) || event.keyCode === 8 || event.keyCode === 9;
        }

        const currentUrl = window.location.href;

        // Use a regular expression to extract the emp_id (e.g., emp00003)
        const empIdMatch = currentUrl.match(/emp(\d{5})/);  // Adjust the regex if needed for different patterns

        // If a match is found, set it in the hidden field
        if (empIdMatch) {
            document.getElementById('emp_id').value = empIdMatch[0];
        } else {
            console.error('Employee ID not found in the URL.');
        }

        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        // Fetch drug types and ingredients on page load
        $.ajax({
            url: "{{ route('getDrugTemplateDetails') }}",
            method: 'GET',
            dataType: 'json', // Ensure response is treated as JSON
            success: function (response) {
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

                    response.drugTemplate.forEach(function (drug) {
                        var drugName = drug.drug_name || 'Unknown Drug';
                        var drugStrength = drug.drug_strength || 'Unknown Strength';
                        var drugType = drug.drug_type || 0;
                        var drugTypeName = drugTypeMapping[drugType] || 'Unknown Type';
                        var drugId = drug.drug_template_id;

                        var formattedDrug = `${drugName} - ${drugStrength} (${drugTypeName})`;
                        drugSelect.append(new Option(formattedDrug, drugId));
                    });

                    drugSelect.change(function () {
                        var selectedDrugId = $(this).val();
                        if (!selectedDrugId) {
                            $('#drug_details_section').hide();
                            return;
                        }

                        var selectedDrug = response.drugTemplate.find(function (drug) {
                            return drug.drug_template_id == selectedDrugId;
                        });

                    });
                } else {
                    console.error('No drug types or ingredients found');
                    $('#drug_template_0').append(new Option('No drug types available', '', true, true));
                }
            },
            error: function (xhr, status, error) {
                console.error('Error fetching drug details: ' + error);
            }
        });

        $('#add_prescription').on('click', function (e) {
            e.preventDefault(); // Prevent the default form submission immediately
            const prescriptionDate = document.getElementById('prescription_date').value;
            if (!prescriptionDate) {
                alert("Please select a prescription date.");
                return; // Prevent further execution if validation fails
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

                // If drug_template_id is selected, validate duration and intake condition
                if (drugTemplate.value === "" && prescriptionTemplate.value === "") {
                    alert("Please select a Drug Template.");
                    isValid = false;
                    return; // Stop further execution if prescriptionTemplate is empty and drugTemplate is not selected
                }

                // If drugTemplate has a value, validate duration and intakeCondition
                if (drugTemplate.value !== "") {
                    // Check if duration is empty or invalid (must be a number)
                    if (!duration.value || isNaN(duration.value) || duration.value <= 0) {
                        alert(`Please enter a valid duration for drug template at row ${index + 1}.`);
                        isValid = false;
                    }

                    // Check if intakeCondition is selected
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

            // If validation fails, stop the process
            if (!isValid) {
                return;
            }
            // Get the value of the input field
            let prescription_Date = $('input[name="prescription_date"]').val();
let formattedDate = '';

if (prescription_Date) {
    let parts = prescription_Date.split('-'); // assuming input is in yyyy-mm-dd format
    if (parts.length === 3) {
        // Normalize to yyyy-mm-dd
        let yyyy = parts[0];
        let mm = parts[1].padStart(2, '0');
        let dd = parts[2].padStart(2, '0');
        formattedDate = `${yyyy}-${mm}-${dd}`;
    }
}

            var formData = {
                _token: csrfToken,
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
                op_registry_id: opRegistryId
            };

            // Loop through all prescription rows to get drug data
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

                // Push each drug row data to the drugs array
                formData.drugs.push(rowData);
            });

            // Loop through prescription rows outside to get additional drug data


            let drugNameAvailable = false; // Flag to check if at least one drug name is available

            $('.prescription-row-output').each(function (index, row) {
                var drugName = $(row).find('input[name="drugname[]"]').val();

                // Check if drug name is available
                if (drugName) {
                    drugNameAvailable = true; // Set the flag to true if drug name is found
                }

                var rowOutsideData = {
                    drugTemplateId: 0,
                    drugName: drugName, // Store the drug name
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

                // Only push the row data to drugs array if drugName is available
                if (drugNameAvailable) {
                    formData.drugs.push(rowOutsideData);
                }
            });
            // Submit the form via AJAX 
            const userType = "{{ session('user_type') }}";

            $.ajax({
                url: "{{ route('store_EmployeePrescription') }}", // Route for storing prescription
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
              
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

                error: function (xhr, status, error) {
                    console.error('AJAX Error: ' + status + ': ' + error);
                    alert('An error occurred while saving the prescription.');
                }
            });
        });

        $('#addTest').on('click', function (e) {
            e.preventDefault(); // Prevent the default form submission immediately
            const prescriptionDate = document.getElementById('prescription_date').value;
            if (!prescriptionDate) {
                alert("Please select a prescription date.");
                return; // Prevent further execution if validation fails
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

                // If drug_template_id is selected, validate duration and intake condition
                if (drugTemplate.value === "" && prescriptionTemplate.value === "") {
                    alert("Please select a Drug Template.");
                    isValid = false;
                    return; // Stop further execution if prescriptionTemplate is empty and drugTemplate is not selected
                }

                // If drugTemplate has a value, validate duration and intakeCondition
                if (drugTemplate.value !== "") {
                    // Check if duration is empty or invalid (must be a number)
                    if (!duration.value || isNaN(duration.value) || duration.value <= 0) {
                        alert(`Please enter a valid duration for drug template at row ${index + 1}.`);
                        isValid = false;
                    }

                    // Check if intakeCondition is selected
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

            // If validation fails, stop the process
            if (!isValid) {
                return;
            }
            // Get the value of the input field
            var prescription_Date = $('input[name="prescription_date"]').val();

            // Convert it to a Date object (assuming the input is in a standard date format, like mm/dd/yyyy)
            let dateObj = new Date(prescription_Date);

            // Format the date as yyyy-mm-dd
            let formattedDate = dateObj.toISOString().split('T')[0];

            // Log the formatted date or use it in your form submission
            console.log(formattedDate);

            var formData = {
                _token: csrfToken,
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

            // Loop through all prescription rows to get drug data
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

                // Push each drug row data to the drugs array
                formData.drugs.push(rowData);
            });

            // Loop through prescription rows outside to get additional drug data


            let drugNameAvailable = false; // Flag to check if at least one drug name is available

            $('.prescription-row-output').each(function (index, row) {
                var drugName = $(row).find('input[name="drugname[]"]').val();

                // Check if drug name is available
                if (drugName) {
                    drugNameAvailable = true; // Set the flag to true if drug name is found
                }

                var rowOutsideData = {
                    drugTemplateId: 0,
                    drugName: drugName, // Store the drug name
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

                // Only push the row data to drugs array if drugName is available
                if (drugNameAvailable) {
                    formData.drugs.push(rowOutsideData);
                }
            });
            // Submit the form via AJAX
            $.ajax({
                url: "{{ route('store_EmployeePrescription') }}", // Route for storing prescription
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    if (response.message) {
                        console.log(response);
                        toastr.success('Prescription saved successfully!', 'Success');

                        // Build the dynamic URL using the returned prescription_id and employee_id
                        var redirectUrl = 'https://login-users.hygeiaes.com/ohc/health-registry/add-test/' + response.employee_id + '/prescription/' +
                            response.prescription_id

                        // Redirect after a short delay to show the toastr notification
                        setTimeout(function () {
                            window.location.href = redirectUrl;
                        }, 2000);
                    } else {
                        toastr.error('An error occurred while saving the prescription.', 'Error');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error: ' + status + ': ' + error);
                    alert('An error occurred while saving the prescription.');
                }
            });

        });
    });
    $(document).ready(function () {
        // Fetch drug template details on page load
        $.ajax({
            url: "{{ route('getDrugTemplateDetails') }}",
            method: 'GET',
            dataType: 'json', // Ensure response is treated as JSON
            success: function (response) {
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

                // Fetch prescription data when template is selected
                $(document).on('change', '#prescriptionTemplate', function () {
                    var prescriptionTemplateId = $(this).val();

                    if (prescriptionTemplateId) {
                        $.ajax({
                            url: `/prescription/prescription-editById/${prescriptionTemplateId}`,
                            method: 'GET',
                            dataType: 'json',
                            success: function (data) {
                                console.log("Prefilling prescription data:", data);

                                if (data.length > 0) {
                                    // Clear existing rows
                                    $('.prescription-inputs').empty();
                                    var headerRow = `
                        <div class="prescription-header" style="display: flex; justify-content: space-between;">
                            <div style="width: 23%;">Drug Name - Type - Strength</div>
                            <div style="width: 8%;">Available</div>
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
                    `;
                                    $('.prescription-inputs').append(headerRow);
                                    // Loop through each prescription data and create dynamic rows
                                    data.forEach((prescriptionData, index) => {
                                        var rowCount = index + 1;
                                        var isFirstRow = (rowCount === 1); // Check if this is the first row
                                        var addButtonIcon = isFirstRow ? 'fa-square-plus' : 'fa-minus';  // Show "+" for the first row and "-" for others

                                        var newRow = `
                                    
                                        <div class="prescription-row" style="display: flex; align-items: center; gap: 10px;">
                                            <!-- Drug Name Input -->
                                            <input type="hidden" name="rowid[]" value="${rowCount}">
                                            <div style="width: 31%;">      
                                                <div class="drug_name" title="drug_name">           
                                                    <select class="hiddendrugname select2" name="drug_template_id[]" id="drug_template_${rowCount}" style="height:25px;width:85%;font-weight:normal;">    
                                                        <option value="">Select a Drug</option>
                                                        <!-- Drug options will be populated here -->
                                                    </select>
                                                </div>
                                            </div>
                                            <div style="width: 5%;"><span id="result_${rowCount}"></div>
                                            <!-- Days Input -->
                                            <div style="width: 5%;">
                                                <input type="text" class="form-control" maxlength="3" name="duration[]" placeholder="Days" onkeypress="return ValidNumber(event)" style="width:65px;" value="${prescriptionData.intake_days}">
                                            </div>

                                            <!-- Morning, Noon, Evening, Night Inputs -->
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

                                            <!-- AF/BF Select -->
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

                                            <!-- Remarks Input -->
                                            <div style="width: 15%;">
                                                <input type="text" class="form-control" name="remarks[]" placeholder="Remarks" style="width:90%; height:36px!important;" value="${prescriptionData.remarks}">
                                            </div>

                                            <!-- Buttons for Add/Remove Rows -->
                                            <div style="width: 5%; text-align: center;">
                                                <div style="cursor: pointer;" class="margin-t-8" onclick="addRow1()">
                                                    <i class="fa-sharp fa-solid ${addButtonIcon}"></i>
                                                </div>
                                            </div>
                                        </div>
                                    `;

                                        // Append the new row
                                        $('.prescription-inputs').append(newRow);

                                        // Initialize select2 for the newly added drug dropdown
                                        var drugSelect = $('#drug_template_' + rowCount);
                                        drugSelect.select2();

                                        // Prefill the drug name dropdown based on drug template list
                                        var drugTemplateList = response.drugTemplate; // Assuming this is the list fetched via AJAX

                                        drugTemplateList.forEach(function (drug) {
                                            var drugName = drug.drug_name || 'Unknown Drug';
                                            var drugStrength = drug.drug_strength || 'Unknown Strength';
                                            var drugType = drug.drug_type || 0;
                                            var drugTypeName = drugTypeMapping[drugType] || 'Unknown Type';
                                            var drugId = drug.drug_template_id;

                                            var formattedDrug = `${drugName} - ${drugStrength} (${drugTypeName})`;
                                            drugSelect.append(new Option(formattedDrug, drugId));
                                        });

                                        // After appending the options, set the correct value
                                        drugSelect.val(prescriptionData.drug_template_id).trigger('change');
                                    });
                                } else {
                                    console.error('No prescription data available for this template');
                                    alert('No data found for this template');
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error('Error fetching prescription data: ' + error);
                            }
                        });
                    }
                });
            },
            error: function (xhr, status, error) {
                console.error('Error fetching drug details: ' + error);
            }
        });
        $(document).on('change', '.hiddendrugname', function () {
            var drugTemplateId = $(this).val();
            var index = $(this).attr('id').split('_')[2];  // Get the index number from the id (e.g., 0, 1, 2)

            console.log("Drug Template ID:", drugTemplateId);
            console.log("Index:", index);

            if (drugTemplateId) {
                var isDuplicate = false;

                // Check if the selected drug is already chosen in another select element
                $('.hiddendrugname').each(function () {
                    var selectedDrug = $(this).val();
                    var currentIndex = $(this).attr('id').split('_')[2];  // Get the index number of this select element

                    // Compare selected drugs, skip comparison if it's the same element (the one that triggered the change event)
                    if (selectedDrug && selectedDrug === drugTemplateId && currentIndex !== index) {
                        isDuplicate = true;  // Mark as duplicate if found
                    }
                });

                if (isDuplicate) {
                    // Alert the user that the drug has already been selected
                    alert('This drug has already been selected. Please choose another drug.');
                    $(this).val('');  // Clear the current selection
                    $(this).trigger('change');  // Trigger change to refresh the select2 UI
                    $('#result_' + index).text('');  // Clear availability result
                    return;  // Exit the function early
                }

                // If no duplicates, proceed to fetch stock availability
                $.ajax({
                    url: `/prescription/getStockByDrugId/${drugTemplateId}`,
                    method: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        console.log("Response Data:", data); // Log the full response

                        if (data && data.data && typeof data.data.total_current_availability !== 'undefined') {
                            $('#result_' + index).text(data.data.total_current_availability); // Set the availability
                        } else {
                            console.log("Availability is undefined or missing.");
                            $('#result_' + index).text('Not Available'); // Show 'Not Available' if the data is missing
                        }
                    },
                    error: function (xhr, status, error) {
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

    function resetForm() {
        location.reload(); // Reloads the page
    }

</script>
@endsection