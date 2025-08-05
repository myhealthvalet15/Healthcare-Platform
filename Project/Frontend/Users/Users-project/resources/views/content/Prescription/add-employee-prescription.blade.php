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
<link rel="stylesheet" href="/lib/css/page-styles/add-employee-prescription.css?v=time()">
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
                                <img src="/assets/img/prescription-icons/morning.png">
                            </div>
                            <div style="display: inline-block; text-align: center; width: 50px; margin: 0 5px;">
                                <img src="/assets/img/prescription-icons/noon.png">
                            </div>
                            <div style="display: inline-block; text-align: center; width: 50px; margin: 0 5px;">
                                <img src="/assets/img/prescription-icons/evening.png">
                            </div>
                            <div style="display: inline-block; text-align: center; width: 50px; margin: 0 5px;">
                                <img src="/assets/img/prescription-icons/night.png">
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
                        <div style="width: 35%;color:#7367f0 !important;"> @if (session('user_type') != 'MasterUser')
                            Outside
                            Prescription @endif @if (session('user_type') == 'MasterUser') Drug Name - Type - Strength
                            @endif </div>
                        <div style="width: 5%;">Days</div>
                        <div style="width: 30%;">
                            <div style="display: inline-block; text-align: center; width: 50px; margin: 0 5px;">
                                <img src="/assets/img/prescription-icons/morning.png">
                            </div>
                            <div style="display: inline-block; text-align: center; width: 50px; margin: 0 5px;">
                                <img src="/assets/img/prescription-icons/noon.png">
                            </div>
                            <div style="display: inline-block; text-align: center; width: 50px; margin: 0 5px;">
                                <img src="/assets/img/prescription-icons/evening.png">
                            </div>
                            <div style="display: inline-block; text-align: center; width: 50px; margin: 0 5px;">
                                <img src="/assets/img/prescription-icons/night.png">
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

                @if (session('user_type') != 'MasterUser')
                <div class="col-md-2" style="margin-left: 50px;">


                    <p> <input type="checkbox" name="share_patient">&nbsp;Share with
                        Patient</p>
                    <p style="margin-bottom: 10px;"> <input type="checkbox" name="share_patient">&nbsp;Send Mail To
                        Patient
                    </p>

                </div> @endif
                @if (session('user_type') == 'MasterUser')

                <div class="col-md-3" style="margin-left:20px;">

                    <!-- 🔗 Attach Prescription -->

                    <label for="prescriptionAttachment" class="form-label fw-semibold">Attach Prescription</label>
                    <input type="file" id="prescriptionAttachment" accept="image/*" multiple class="form-control" />
                    <!-- 📷 Thumbnails -->
                    <div id="prescriptionThumbnails" class="d-flex flex-wrap gap-2 mb-2" style="margin-top: 25px;">
                        <!-- Thumbnails will appear here -->
                    </div>
                </div>
                @endif
                <div class="mb-2">


                    <!-- 📝 Doctor Notes -->
                    <textarea name="doctorNotes"
                        style="padding: 7px; width: 270px; border: 1px solid #d1d0d4; margin-left: -25px;"
                        class="form-control">Doctor Notes</textarea><br />
                    <button class="btn btn-primary me-2" id="addTest">Add Test</button>
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
                        @if (session('user_type') === 'MasterUser')
                        <span><span style="color:#fff;">Save Prescription</span></span>
                        @else
                        <span><span style="color:#fff;">Add Prescription</span></span>

                        @endif
                    </a>
                </div>

            </div>
            <br />
            <br />
        </div>
    </div>

    <!-- 📌 Prescription Image Modal -->
    <div class="modal fade" id="prescriptionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-0">
                    <img id="prescriptionModalImage" src="" class="img-fluid w-100" />
                </div>
            </div>
        </div>
    </div>
    
    <script src="/lib/js/page-scripts/add-employee-prescription.js?v=time()"></script>

    @endsection