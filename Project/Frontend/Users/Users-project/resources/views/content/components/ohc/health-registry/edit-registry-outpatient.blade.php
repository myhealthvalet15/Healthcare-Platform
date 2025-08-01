@extends('layouts/layoutMaster')
@section('title', 'Incident Report Form')
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
<style>
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
    var prescribedTestData = <?php echo json_encode($employeeData['op_registry_datas']['prescribed_test_data'] ?? []); ?>;
    var employeeData = <?php echo json_encode($employeeData); ?>;
</script>
<?php
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
                            {{ strtoupper($employeeData['employee_firstname'] ?? '') }}
                            {{ strtoupper($employeeData['employee_lastname'] ?? '') }} -
                            {{ $employeeData['employee_id'] ?? '' }} -
                            {{ ($employeeData['followUpCount'] ?? 0) > 0 ? 'Follow Up ' . ($employeeData['followUpCount'] ?? 0) : 'Main Registry' }}
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
                    <div class="position-absolute end-0 top-0 bottom-0 d-flex flex-column justify-content-end me-4 py-2">
                        <button type="button" 
                            class="btn btn-sm"
                            style="background-color: #ffffff; color: #6a0dad; border: none; padding: 6px 12px; border-radius: 8px; font-weight: bold; width: auto;">
                            Edit Profile
                        </button>
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
        <div class="card-body pt-3">
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label">Reporting Date & Time</label>
                    <input class="form-control form-control-sm" type="datetime-local" id="reporting-datetime" />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Incident Date & Time</label>
                    <input class="form-control form-control-sm" type="datetime-local" id="incident-datetime" />
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
        <div class="col-md-4">
            <h5 class="mb-3 text-primary">Vital Parameters</h5>
        </div>
        <div class="col-md-4">
            <h5 class="mb-3 text-primary">Types of incident</h5>
        </div>
        <div class="col-md-4 d-flex justify-content-end">
            <div class="me-2" style="width: 160px;">
                <select class="form-select form-select-sm w-100" id="incidentType" onchange="toggleIncidentFields()">
                    <option value="medicalIllness" selected>Medical
                        Illness</option>
                    <option value="industrialAccident">Industrial
                        Accident</option>
                    <option value="outsideAccident">Outside Accident</option>
                </select>
            </div>
            <div style="width: 160px;">
                <select class="form-select form-select-sm w-100" id="doctorSelect">
                    <option value="0" selected>Select Doctor</option>
                    <option value="1">Doctor 1</option>
                    <option value="2">Doctor 2</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body p-3">
                    <div class="row mb-2 align-items-center">
                        <div class="col-5">
                            <label class="form-label mb-0">Temperature</label>
                        </div>
                        <div class="col-3 px-0">
                            <input type="text" class="form-control form-control-sm" id="vpTemperature_167" />
                        </div>
                        <div class="col-4">
                            <span class="form-text mb-0 ms-2">°F</span>
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <div class="col-5">
                            <label class="form-label mb-0">BP
                                Systolic/Diastolic</label>
                            <small class="d-block">mm/hg</small>
                        </div>
                        <div class="col-7 px-0 d-flex">
                            <input type="text" class="form-control form-control-sm me-1" style="width: 40%;"
                                id="vpSystolic_168" />
                            <input type="text" class="form-control form-control-sm" style="width: 40%;"
                                id="vpDiastolic_169" />
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <div class="col-5">
                            <label class="form-label mb-0">Pulse Rate</label>
                        </div>
                        <div class="col-3 px-0">
                            <input type="text" class="form-control form-control-sm" id="vpPulseRate_170" />
                        </div>
                        <div class="col-4">
                            <span class="form-text mb-0 ms-2">Beats/min</span>
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <div class="col-5">
                            <label class="form-label mb-0">Respiratory</label>
                        </div>
                        <div class="col-3 px-0">
                            <input type="text" class="form-control form-control-sm" id="vpRespiratory_171" />
                        </div>
                        <div class="col-4">
                            <span class="form-text mb-0 ms-2">bpm</span>
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <div class="col-5">
                            <label class="form-label mb-0">SPO2</label>
                        </div>
                        <div class="col-3 px-0">
                            <input type="text" class="form-control form-control-sm" id="vpSPO2_172" />
                        </div>
                        <div class="col-4">
                            <span class="form-text mb-0 ms-2">%</span>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-5">
                            <label class="form-label mb-0">Random
                                Glucose</label>
                        </div>
                        <div class="col-3 px-0">
                            <input type="text" class="form-control form-control-sm" id="vpRandomGlucose_173" />
                        </div>
                        <div class="col-4">
                            <span class="form-text mb-0 ms-2">mg/dl</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm" id="medicalFields">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label text-primary mb-3" style="font-size: 15px;">
                                <strong>Body Part</strong>
                            </label>
                        </div>
                        <div class="col-md-9">
                            <div class="select2-primary body-part">
                                <select id="select2Primary_body_part" class="select2 form-select" multiple>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label text-primary mb-3" style="font-size: 15px;">
                                <strong>Symptoms</strong>
                            </label>
                        </div>
                        <div class="col-md-9">
                            <div class="select2-primary symptoms">
                                <select id="select2Primary_symptoms" class="select2 form-select" multiple>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label text-primary mb-3" style="font-size: 15px;">
                                <strong>Medical System</strong>
                            </label>
                        </div>
                        <div class="col-md-9">
                            <div class="select2-primary medical-system">
                                <select id="select2Primary_medical_system" class="select2 form-select" multiple>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label text-primary mb-3" style="font-size: 15px;">
                                <strong>Diagnosis</strong>
                            </label>
                        </div>
                        <div class="col-md-9">
                            <div class="select2-primary diagnosis">
                                <select id="select2Primary_diagnosis" class="select2 form-select" multiple>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm" id="industrialFields" style="display: none;">
                <div class="card-body">
                    @if (!empty($employeeData['incidentTypeColorCodes']))
                        <style>
                            .custom-radio {
                                position: relative;
                                cursor: pointer;
                            }
                            @foreach ($employeeData['incidentTypeColorCodes'] as $label => $color)
                                @php $id = \Illuminate\Support\Str::slug($label); @endphp
                                #{{ $id }}:checked {
                                    background-color: {{ $color }} !important;
                                    border-color: {{ $color }} !important;
                                }                            
                                #{{ $id }}::before {
                                    background-color: {{ $color }};
                                }
                            @endforeach
                        </style>
                    @endif
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label text-primary mb-3"
                                    style="font-size: 15px; color: #6c5ce7 !important; font-weight: 500;">
                                    <strong>Injury Color</strong>
                                </label>
                                @if (!empty($employeeData['incidentTypeColorCodes']))
                                <div class="d-flex flex-wrap">
                                    @foreach ($employeeData['incidentTypeColorCodes'] as $label => $color)
                                    @php
                                    $id = \Illuminate\Support\Str::slug($label);
                                    $selected = old('injury_color_text') ??
                                    ($employeeData['op_registry_datas']['op_registry']['injury_color_text'] ?? '');
                                    $valueWithColor = $label . '_' . $color;
                                    @endphp
                                    <div class="form-check me-3 mb-2">
                                        <input class="form-check-input custom-radio" type="radio"
                                            name="injury_color_text" id="{{ $id }}" value="{{ $valueWithColor }}"
                                            data-color="{{ $color }}" data-label="{{ $label }}" @if (strpos($selected,
                                            $label)===0) checked @endif>
                                        <label class="form-check-label" for="{{ $id }}">
                                            {{ $label }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            <div class="mb-4">
                                <label class="form-label text-primary mb-2"
                                    style="font-size: 15px; color: #6c5ce7 !important; font-weight: 500;">
                                    <strong>Side of Body</strong>
                                </label>
                                <div>
                                    <div class="d-flex">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" id="leftSide"
                                                style="width: 14px; height: 14px;">
                                            <label class="form-check-label" for="leftSide">Left</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="rightSide"
                                                style="width: 14px; height: 14px;">
                                            <label class="form-check-label" for="rightSide">Right</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="siteOfInjury">
                                <div class="mb-4">
                                    <label class="form-label text-primary mb-2"
                                        style="font-size: 15px; color: #6c5ce7 !important; font-weight: 500;">
                                        <strong>Site of Injury</strong>
                                    </label>
                                    <div>
                                        <div class="d-flex">
                                            <div class="form-check me-3">
                                                <input class="form-check-input site-of-injury" type="checkbox"
                                                    id="shopFloor" style="width: 14px; height: 14px;"
                                                    onclick="toggleCheckbox('shopFloor')">
                                                <label class="form-check-label" for="shopFloor">Shop Floor</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input site-of-injury" type="checkbox"
                                                    id="nonShopFloor" style="width: 14px; height: 14px;"
                                                    onclick="toggleCheckbox('nonShopFloor')">
                                                <label class="form-check-label" for="nonShopFloor">Non-Shop Floor</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label text-primary mb-2"
                                    style="font-size: 15px; color: #6c5ce7 !important; font-weight: 500;">
                                    <strong>Nature of Injury</strong>
                                </label>
                                <div class="select2-primary nature-of-injury">
                                    <select id="select2Primary_nature_of_injury" class="select2 form-select" multiple>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label text-primary mb-2"
                                    style="font-size: 15px; color: #6c5ce7 !important; font-weight: 500;">
                                    <strong>Body Part</strong>
                                </label>
                                <div class="select2-primary body-part-injury">
                                    <select id="select2Primary_body_part_IA" class="select2 form-select" multiple>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label text-primary mb-2"
                                    style="font-size: 15px; color: #6c5ce7 !important; font-weight: 500;">
                                    <strong>Mechanism of Injury</strong>
                                </label>
                                <div class="select2-primary injury-mechanism">
                                    <select id="select2Primary_injury_mechanism" class="select2 form-select" multiple>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Description field spanning both columns -->
                    <div class="row mt-2">
                        <div class="col-12">
                            <label class="form-label text-primary mb-2"
                                style="font-size: 15px; color: #6c5ce7 !important; font-weight: 500;">
                                <strong>Description of Accident Occurrence</strong>
                            </label>
                            <textarea class="form-control" id="injury_description" rows="1"
                                placeholder="Enter detailed description of the accident"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-4">
            <h5 class="text-primary mb-3">Lost Hours & Out Time</h5>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Leave From</label>
                        </div>
                        <div class="col-md-8">
                            <input class="form-control" type="datetime-local" id="leave-from" />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Leave Upto</label>
                        </div>
                        <div class="col-md-8">
                            <input class="form-control" type="datetime-local" id="leave-upto" />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Lost Hours</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" id="lostHours" class="form-control" value="8:00" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Out Time</label>
                        </div>
                        <div class="col-md-8">
                            <input class="form-control" id="outTime" type="datetime-local"
                                value="2021-06-18T12:30:00" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <h5 class="text-primary mb-3">Observations</h5>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Doctor Notes</label>
                        </div>
                        <div class="col-md-8">
                            <textarea class="form-control" rows="2" id="doctorNotes"></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Medical History</label>
                        </div>
                        <div class="col-md-6">
                            <textarea class="form-control" rows="2" id="medicalHistory"></textarea>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary" style="font-size: 12px; padding: 4px 8px;">
                                View Past History
                            </button>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Referral</label>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <select class="form-select" id="referralSelect">
                                        <option value="noOutsideReferral">No
                                            Outside Referral</option>
                                        <option value="OutsideReferral">Outside
                                            Referral</option>
                                        <option value="ama">AMA</option>
                                    </select>
                                </div>
                                <div class="col-md-6" style="display: none;" id="outsideReferralMR">
                                    <div class="d-flex align-items-center">
                                        <!-- <div class="bg-light p-2 rounded me-2"
                                            style="width: 40px; height: 40px; display: flex; justify-content: center; align-items: center;">
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#basicModal">
                                                <i class="fas fa-edit text-white"></i>
                                            </button>
                                        </div> -->
                                        <div>
                                            <!-- <strong>MR # <span id="mrNumber"></span></strong> -->
                                            <strong>
                                                <div id="outsideReferralHospitalName" style="font-weight: bold; cursor: pointer;"></div>
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        document.getElementById("outsideReferralHospitalName").addEventListener("click", function() {
                            const myModal = new bootstrap.Modal(document.getElementById('basicModal'));
                            myModal.show();
                        });
                    </script>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="movementSlip">
                                    <label class="form-check-label" for="movementSlip">Movement Slip</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="fitnessCert">
                                    <label class="form-check-label" for="fitnessCert">Fitness
                                        Certificate</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="physiotherapy">
                                    <label class="form-check-label" for="physiotherapy">Physiotherapy</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between">
                <div>
                    <button class="btn btn-outline-primary me-2">
                        <i class="fas fa-envelope"></i>
                    </button>
                    <button class="btn btn-outline-primary">
                        <i class="fas fa-print"></i>
                    </button>
                </div>
                <div>
                    <button class="btn btn-warning me-2" id="backToList">Back to List</button>
                    <button class="btn btn-warning me-2" id="addPrescription">Add
                        Prescription</button>
                    <button class="btn btn-warning me-2" id="addTest">Add
                        Test</button>
                    <button class="btn btn-primary me-2" id="saveClose">Save &
                        Close</button>
                    <button class="btn btn-secondary" id="saveHR">Save Health
                        Registry</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hospital Admission Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="hospitalName" class="form-label">Enter
                                Hospital Name</label>
                            <input type="text" id="hospitalName" class="form-control" placeholder="Enter Hospital Name"
                                id="hospitalName">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="esiScheme">
                                <label class="form-check-label" for="esiScheme">
                                    Patient covered by E.S.I Scheme
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="vehicleType" class="form-label">Select
                                Vehicle</label>
                            <select id="vehicleType" class="form-select">
                                <option value="own">Own Vehicle</option>
                                <option value="ambulance">Ambulance</option>
                            </select>
                        </div>
                    </div>
                    <div id="ambulanceFields" class="hidden">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="driverName" class="form-label">Name
                                    of
                                    Driver</label>
                                <input type="text" id="driverName" class="form-control" placeholder="Enter Driver Name">
                            </div>
                            <div class="col-md-6">
                                <label for="ambulanceNumber" class="form-label">Ambulance Number</label>
                                <input type="text" id="ambulanceNumber" class="form-control"
                                    placeholder="Enter Ambulance Number">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="accompaniedBy" class="form-label">Accompanied By</label>
                                <input type="text" id="accompaniedBy" class="form-control"
                                    placeholder="Enter Name / Phone Num / Employee Id">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="odometerOut" class="form-label">Odometer Out</label>
                                <input type="number" id="odometerOut" class="form-control" placeholder="Enter Odometer Out">
                            </div>
                            <div class="col-md-6">
                                <label for="odometerIn" class="form-label">Odometer In</label>
                                <input type="number" id="odometerIn" class="form-control" placeholder="Enter Odometer In">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="timeOut" class="form-label">Time Out</label>
                                <input type="time" id="timeOut" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="timeIn" class="form-label">Time In</label>
                                <input type="time" id="timeIn" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveChangesModal">Save
                        Changes</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function formatTime(dateTime) {
        if (!dateTime) return '';
        const date = new Date(dateTime);
        return date.toISOString().substr(11, 5);
    }
    function initializeSiteOfInjuryCheckboxes(opRegistry) {
        let siteOfInjuryObj;
        if (typeof opRegistry['site_of_injury'] === 'string') {
            try {
                siteOfInjuryObj = JSON.parse(opRegistry['site_of_injury']);
            } catch (error) {
                return;
            }
        } else {
            siteOfInjuryObj = opRegistry['site_of_injury'];
        }
        if (siteOfInjuryObj && typeof siteOfInjuryObj === 'object') {
            document.getElementById('shopFloor').checked = siteOfInjuryObj.shopFloor === true;
            document.getElementById('nonShopFloor').checked = siteOfInjuryObj.nonShopFloor === true;
        } else {
            console.error("Could not get valid site_of_injury data");
        }
    }
    function formatDate(dateTimeStr) {
        try {
            if (dateTimeStr.includes('.')) {
                dateTimeStr = dateTimeStr.split('.')[0];
            }
            let date = new Date(dateTimeStr);
            if (isNaN(date.getTime())) {
                return '';
            }
            let year = date.getFullYear();
            let month = (date.getMonth() + 1).toString().padStart(2, '0');
            let day = date.getDate().toString().padStart(2, '0');
            let hours = date.getHours().toString().padStart(2, '0');
            let minutes = date.getMinutes().toString().padStart(2, '0');
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        } catch (e) {
            console.error('Error formatting date:', e);
            return '';
        }
    }
    function toggleIncidentFields() {
        const incidentType = document.getElementById('incidentType').value;
        const medicalFields = document.getElementById('medicalFields');
        const industrialFields = document.getElementById('industrialFields');
        const siteOfInjury = document.getElementById('siteOfInjury');
        if (incidentType === 'medicalIllness') {
            medicalFields.style.display = 'block';
            industrialFields.style.display = 'none';
        } else if (incidentType === 'industrialAccident') {
            medicalFields.style.display = 'none';
            industrialFields.style.display = 'block';
            siteOfInjury.style.display = 'flex';
        } else {
            medicalFields.style.display = 'none';
            industrialFields.style.display = 'block';
            siteOfInjury.style.display = 'none';
        }
    }
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
    function selectValues(selectId, selectedValues = []) {
        const selectElement = document.getElementById(selectId);
        if (!selectElement) return;
        const options = selectElement.options;
        for (let i = 0; i < options.length; i++) {
            if (selectedValues.includes(parseInt(options[i].value))) {
                options[i].selected = true;
            }
        }
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
    function validateFields(fields) {
        for (const [id, label] of Object.entries(fields)) {
            const value = document.getElementById(id)?.value.trim() || '';
            if (!value) {
                showToast('error', `Please fill ${label}`);
                return false;
            }
        }
        return true;
    }
    function toggleCheckbox(selectedId) {
        const checkboxes = document.querySelectorAll('.site-of-injury');
        checkboxes.forEach(checkbox => {
            if (checkbox.id !== selectedId) {
                checkbox.checked = false;
            }
        });
    }
    function handleIncidentType() {
        const incidentType = document.getElementById('incidentType').value;
        const doctorSelect = document.getElementById('doctorSelect').value;
        if (incidentType != "medicalIllness") {
            const injuryColor = document.querySelector('input[name="injury_color_text"]:checked');
            if (!injuryColor) {
                showToast('error', 'Please select Injury Color');
                return false;
            }
        }
        if (!incidentType) {
            showToast('error', 'Please select Incident Type');
            return false;
        }
        const doctor = {
            doctorId: doctorSelect,
            doctorName: $('#doctorSelect option:selected').text()
        };
        return true;
    }
    function handleObservations() {
        const observations = {
            doctorNotes: $('#doctorNotes').val(),
            medicalHistory: $('#medicalHistory').val(),
            referral: $('#referralSelect').val(),
            movementSlip: $('#movementSlip').is(':checked'),
            fitnessCert: $('#fitnessCert').is(':checked'),
            physiotherapy: $('#physiotherapy').is(':checked')
        };
        if ($('#referralSelect').val() === "OutsideReferral") {
            const hospitalName = document.getElementById("hospitalName").value;
            if (!hospitalName) {
                showToast('error', 'Please fill Hospital Name');
                return false;
            }
            const vehicleType = document.getElementById("vehicleType").value;
            if (!vehicleType) {
                showToast('error', 'Please select Vehicle Type');
                return false;
            }
            if (vehicleType === 'ambulance') {
                const driverName = document.getElementById("driverName").value;
                const ambulanceNumber = document.getElementById("ambulanceNumber").value;
                const odometerIn = document.getElementById("odometerIn").value;
                const odometerOut = document.getElementById("odometerOut").value;
                const timeIn = document.getElementById("timeIn").value;
                const timeOut = document.getElementById("timeOut").value;
                if (!driverName) {
                    showToast('error', 'Please fill Driver Name');
                    return false;
                }
                if (!ambulanceNumber) {
                    showToast('error', 'Please fill Ambulance Number');
                    return false;
                }
                if (!odometerIn) {
                    showToast('error', 'Please fill Odometer In');
                    return false;
                }
                if (!odometerOut) {
                    showToast('error', 'Please fill Odometer Out');
                    return false;
                }
                if (!timeIn) {
                    showToast('error', 'Please fill Time In');
                    return false;
                }
                if (!timeOut) {
                    showToast('error', 'Please fill Time Out');
                    return false;
                }
            }
        }
        return true;
    }
    function sendHealthRegistryData(close = false, onSuccessCallback = () => { }) {
        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to submit the health registry data?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, submit it!"
        }).then((result) => {
            if (result.isConfirmed) {
                const healthRegistryData = {
                    employeeId: $('#employeeId').val(),
                    vitalParameters: {
                        vpTemperature_167: $('#vpTemperature_167').val(),
                        vpSystolic_168: $('#vpSystolic_168').val(),
                        vpDiastolic_169: $('#vpDiastolic_169').val(),
                        vpPulseRate_170: $('#vpPulseRate_170').val(),
                        vpRespiratory_171: $('#vpRespiratory_171').val(),
                        vpSPO2_172: $('#vpSPO2_172').val(),
                        vpRandomGlucose_173: $('#vpRandomGlucose_173').val()
                    },
                    incidentType: $('#incidentType').val(),
                    observations: {
                        doctorNotes: $('#doctorNotes').val(),
                        medicalHistory: $('#medicalHistory').val(),
                        referral: $('#referralSelect').val(),
                        movementSlip: $('#movementSlip').is(':checked'),
                        fitnessCert: $('#fitnessCert').is(':checked'),
                        physiotherapy: $('#physiotherapy').is(':checked')
                    },
                    lostHours: {
                        leaveFrom: $('#leave-from').val(),
                        leaveUpto: $('#leave-upto').val(),
                        lostHours: $('#lostHours').val(),
                        outTime: $('#outTime').val()
                    },
                    close: close,
                    workShift: $('#workShift').val(),
                    firstAidBy: $('#firstAidBy').val(),
                    reportingDateTime: $('#reporting-datetime').val(),
                    incidentDateTime: $('#incident-datetime').val(),
                    movementSlip: document.getElementById('movementSlip').checked ? 1 : 0,
                    fitnessCert: document.getElementById('fitnessCert').checked ? 1 : 0,
                    physiotherapy: document.getElementById('physiotherapy').checked ? 1 : 0
                };
                const doctorId = $('#doctorSelect').val();
                const doctorName = $('#doctorSelect option:selected').text();
                if (doctorId && doctorName !== "Select Doctor") {
                    healthRegistryData.doctor = {
                        doctorId: doctorId,
                        doctorName: doctorName
                    };
                }
                if (healthRegistryData.incidentType === "medicalIllness") {
                    healthRegistryData.medicalFields = {
                        bodyPart: $('#select2Primary_body_part').val(),
                        symptoms: $('#select2Primary_symptoms').val(),
                        medicalSystem: $('#select2Primary_medical_system').val(),
                        diagnosis: $('#select2Primary_diagnosis').val()
                    };
                } else if (healthRegistryData.incidentType === "industrialAccident") {
                    healthRegistryData.industrialFields = {
                        injuryColor: $('input[name="injury_color_text"]:checked').val(),
                        sideOfBody: {
                            left: $('#leftSide').is(':checked'),
                            right: $('#rightSide').is(':checked')
                        },
                        siteOfInjury: {
                            shopFloor: $('#shopFloor').is(':checked'),
                            nonShopFloor: $('#nonShopFloor').is(':checked')
                        },
                        natureOfInjury: $('#select2Primary_nature_of_injury').val(),
                        bodyPartIA: $('#select2Primary_body_part_IA').val(),
                        injuryMechanism: $('#select2Primary_injury_mechanism').val(),
                        description: $('#injury_description').val()
                    };
                } else {
                    healthRegistryData.industrialFields = {
                        injuryColor: $('input[name="injury_color_text"]:checked').val(),
                        sideOfBody: {
                            left: $('#leftSide').is(':checked'),
                            right: $('#rightSide').is(':checked')
                        },
                        natureOfInjury: $('#select2Primary_nature_of_injury').val(),
                        bodyPartIA: $('#select2Primary_body_part_IA').val(),
                        injuryMechanism: $('#select2Primary_injury_mechanism').val(),
                        description: $('#injury_description').val()
                    };
                }
                let referral = $('#referralSelect').val();
                healthRegistryData.referral = referral;
                if (referral === 'OutsideReferral') {
                    let hospitalName = document.getElementById("hospitalName").value;
                    let esiScheme = document.getElementById("esiScheme").checked ? 1 : 0;
                    let vehicleType = document.getElementById("vehicleType").value;
                    healthRegistryData.hospitalDetails = {
                        hospitalName: hospitalName,
                        esiScheme: esiScheme,
                        vehicleType: vehicleType
                    };
                    if (vehicleType === 'ambulance') {
                        let driverName = document.getElementById("driverName").value;
                        let ambulanceNumber = document.getElementById("ambulanceNumber").value;
                        let accompaniedBy = document.getElementById("accompaniedBy").value;
                        let odometerIn = document.getElementById("odometerIn").value;
                        let odometerOut = document.getElementById("odometerOut").value;
                        let timeIn = document.getElementById("timeIn").value;
                        let timeOut = document.getElementById("timeOut").value;
                        healthRegistryData.hospitalDetails = {
                            driverName: driverName,
                            hospitalName: hospitalName,
                            esiScheme: esiScheme,
                            vehicleType: vehicleType,
                            ambulanceNumber: ambulanceNumber,
                            accompaniedBy: accompaniedBy,
                            odometerIn: odometerIn,
                            odometerOut: odometerOut,
                            timeIn: timeIn,
                            timeOut: timeOut
                        };
                    }
                }
                const opRegistryId = employeeData.op_registry_datas?.op_registry?.op_registry_id;
                const editExistingOne = 1;
                healthRegistryData.editExistingOne = editExistingOne;
                const isFollowup = 0;
                healthRegistryData.isFollowup = isFollowup;
                apiRequest({
                    url: 'https://login-users.hygeiaes.com/ohc/health-registry/saveHealthRegistry/' + opRegistryId,
                    method: 'POST',
                    data: healthRegistryData,
                    onSuccess: function (response) {
                        showToast('success', 'Success', 'Health registry saved successfully!');
                        onSuccessCallback(response.op_registry_id);
                    },
                    onError: function (error) {
                        console.error('Error saving health registry:', error);
                        showToast('error', 'Error', 'Failed to save health registry');
                    }
                });
            }
        });
    }
    function populateAddedOutpatientData() {
        const fieldMapping = {
            167: 'vpTemperature_167',
            168: 'vpSystolic_168',
            169: 'vpDiastolic_169',
            170: 'vpPulseRate_170',
            171: 'vpRespiratory_171',
            172: 'vpSPO2_172',
            173: 'vpRandomGlucose_173',
        };
        prescribedTestData.forEach((data) => {
            let masterTestId = data.master_test_id;
            let testResult = data.test_results;
            if (fieldMapping[masterTestId]) {
                let fieldId = fieldMapping[masterTestId];
                let inputElement = document.getElementById(fieldId);
                if (inputElement) {
                    inputElement.value = testResult;
                }
            }
        });
        var opRegistryTimes = employeeData.op_registry_datas?.op_registry_times || {};
        var opRegistry = employeeData.op_registry_datas?.op_registry || {};
        var prescribedTests = employeeData.op_registry_datas?.prescribed_test || {};
        var reportingDateTime = opRegistryTimes.reporting_date_time ?? '';
        if (reportingDateTime) {
            document.getElementById('reporting-datetime').value = formatDate(reportingDateTime);
        }
        var incidentDateTime = opRegistryTimes.incident_date_time ?? '';
        if (incidentDateTime) {
            document.getElementById('incident-datetime').value = formatDate(incidentDateTime);
        }
        var leaveFrom = opRegistryTimes.leave_from_date_time ?? '';
        if (leaveFrom) {
            document.getElementById('leave-from').value = formatDate(leaveFrom);
        }
        var leaveUpto = opRegistryTimes.leave_upto_date_time ?? '';
        if (leaveUpto) {
            document.getElementById('leave-upto').value = formatDate(leaveUpto);
        }
        var lostHours = opRegistryTimes.lost_hours;
        if (lostHours) {
            document.getElementById('lostHours').value = lostHours;
        }
        var outTime = opRegistryTimes.out_date_time;
        if (outTime) {
            document.getElementById('outTime').value = formatDate(outTime);
        }
        var doctorNotes = opRegistry.doctor_notes;
        if (doctorNotes) {
            document.getElementById('doctorNotes').value = doctorNotes;
        }
        var medicalHistory = opRegistry.past_medical_history;
        if (medicalHistory) {
            document.getElementById('medicalHistory').value = medicalHistory;
        }
        var shift = parseInt(opRegistry.shift ?? 4);
        if (shift) {
            document.getElementById('workShift').value = shift.toString();
        }
        var firstAidBy = opRegistry.first_aid_by ?? '';
        if (firstAidBy) {
            document.getElementById('firstAidBy').value = firstAidBy;
        }
        var movement_slip = opRegistry.movement_slip;
        if (movement_slip) {
            document.getElementById('movementSlip').checked = movement_slip;
        }
        var physiotherapy = opRegistry.physiotherapy;
        if (physiotherapy) {
            document.getElementById('physiotherapy').checked = physiotherapy;
        }
        var fitness_certificate = opRegistry.fitness_certificate;
        if (fitness_certificate) {
            document.getElementById('fitnessCert').checked = fitness_certificate;
        }
        var isOutPatientAdded = employeeData.isPrescriptionAdded;
        if (isOutPatientAdded) {
            $('#addPrescription').text('View Prescription');
            $('#addPrescription').removeClass('btn-warning');
            $('#addPrescription').addClass('btn-secondary');
        }
        var isTestAdded = employeeData.isTestAdded;
        if (isTestAdded) {
            $('#addTest').text('View Test');
            $('#addTest').removeClass('btn-warning');
            $('#addTest').addClass('btn-secondary');
        }
        var referral = opRegistry.referral;
        if (referral) {
            document.getElementById('referralSelect').value = referral;
            if (referral === 'OutsideReferral') {
                document.getElementById('outsideReferralMR').style.display = 'block';
                document.getElementById("outsideReferralHospitalName").textContent = employeeData.op_outside_referral['hospital_name'];
                document.getElementById('hospitalName').value = employeeData.op_outside_referral['hospital_name'];
                document.getElementById('esiScheme').checked = employeeData.op_outside_referral['employee_esi'];
                document.getElementById("vehicleType").value = employeeData.op_outside_referral['vehicle_type'];
                if (employeeData.op_outside_referral['vehicle_type'] === 'ambulance') {
                    document.getElementById('ambulanceFields').classList.remove('hidden');
                    document.getElementById('driverName').value = employeeData.op_outside_referral['ambulance_driver'];
                    document.getElementById('ambulanceNumber').value = employeeData.op_outside_referral['ambulance_number'];
                    document.getElementById('accompaniedBy').value = employeeData.op_outside_referral['accompanied_by'];
                    document.getElementById('odometerIn').value = employeeData.op_outside_referral['meter_in'];
                    document.getElementById('odometerOut').value = employeeData.op_outside_referral['meter_out'];
                    document.getElementById('timeIn').value = formatTime(employeeData.op_outside_referral['ambulance_intime']);
                    document.getElementById('timeOut').value = formatTime(employeeData.op_outside_referral['ambulance_outtime']);
                }
            }
        }
        var type_of_incident = opRegistry.type_of_incident;
        if (type_of_incident === 'medicalIllness') {
            const medicalFields = document.getElementById('medicalFields');
            const industrialFields = document.getElementById('industrialFields');
            medicalFields.style.display = 'block';
            industrialFields.style.display = 'none';
            document.getElementById('incidentType').value = 'medicalIllness';
            selectValues('select2Primary_body_part', opRegistry.body_part);
            selectValues('select2Primary_symptoms', opRegistry.symptoms);
            selectValues('select2Primary_medical_system', opRegistry.medical_system);
            selectValues('select2Primary_diagnosis', opRegistry.diagnosis);
        } else if (type_of_incident === 'industrialAccident') {
            const medicalFields = document.getElementById('medicalFields');
            const industrialFields = document.getElementById('industrialFields');
            const siteOfInjury = document.getElementById('siteOfInjury');
            medicalFields.style.display = 'none';
            industrialFields.style.display = 'block';
            siteOfInjury.style.display = 'flex';
            document.getElementById('incidentType').value = 'industrialAccident';
            selectValues('select2Primary_body_part_IA', opRegistry.body_part);
            selectValues('select2Primary_nature_of_injury', opRegistry.nature_injury);
            selectValues('select2Primary_injury_mechanism', opRegistry.mechanism_injury);
            var description = opRegistry.description;
            if (description) {
                document.getElementById("injury_description").value = opRegistry.description;
            }
            if (opRegistry.injury_color_text) {
                const selectedValue = opRegistry.injury_color_text;
                const injuryColorRadios = document.querySelectorAll('input[name="injuryColor"]');
                injuryColorRadios.forEach(radio => {
                    if (radio.value.toLowerCase() === selectedValue.toLowerCase()) {
                        radio.checked = true;
                    }
                });
            }
            initializeCheckboxes();
            initializeSiteOfInjuryCheckboxes(opRegistry);
        } else if (type_of_incident === 'outsideAccident') {
            const medicalFields = document.getElementById('medicalFields');
            const industrialFields = document.getElementById('industrialFields');
            const siteOfInjury = document.getElementById('siteOfInjury');
            medicalFields.style.display = 'none';
            industrialFields.style.display = 'block';
            siteOfInjury.style.display = 'none';
            document.getElementById('incidentType').value = 'outsideAccident';
            selectValues('select2Primary_body_part_IA', opRegistry.body_part);
            selectValues('select2Primary_nature_of_injury', opRegistry.nature_injury);
            selectValues('select2Primary_injury_mechanism', opRegistry.mechanism_injury);
            var description = opRegistry.description;
            if (description) {
                document.getElementById("injury_description").value = opRegistry.description;
            }
            if (opRegistry.injury_color_text) {
                const selectedValue = opRegistry.injury_color_text;
                const injuryColorRadios = document.querySelectorAll('input[name="injuryColor"]');
                injuryColorRadios.forEach(radio => {
                    if (radio.value.toLowerCase() === selectedValue.toLowerCase()) {
                        radio.checked = true;
                    }
                });
            }
            initializeCheckboxes();
            initializeSiteOfInjuryCheckboxes(opRegistry);
        }
        var doctorId = prescribedTests.doctor_id;
        if (doctorId > 0) {
            document.getElementById('doctorSelect').value = doctorId;
        }
        function initializeCheckboxes() {
            let bodySideObj;
            if (typeof opRegistry['body_side'] === 'string') {
                try {
                    bodySideObj = JSON.parse(opRegistry['body_side']);
                } catch (error) {
                    return;
                }
            } else {
                bodySideObj = opRegistry['body_side'];
            }
            if (bodySideObj && typeof bodySideObj === 'object') {
                document.getElementById('leftSide').checked = bodySideObj.left === true;
                document.getElementById('rightSide').checked = bodySideObj.right === true;
            } else {
                console.error("Could not get valid body_side data");
            }
        }
    }
    document.getElementById('vehicleType').addEventListener('change', function () {
        const ambulanceFields = document.getElementById('ambulanceFields');
        if (this.value === 'ambulance') {
            ambulanceFields.classList.remove('hidden');
        } else {
            ambulanceFields.classList.add('hidden');
        }
    });
    $(document).ready(function () {
        document.getElementById("saveChangesModal").addEventListener("click", function () {
            let hospitalNameInput = document.getElementById("hospitalName").value.trim();
            let sanitizedHospitalName = hospitalNameInput.replace(/[<>]/g, "");
            document.getElementById("outsideReferralHospitalName").textContent = sanitizedHospitalName || "No Hospital Name Entered";
            let modal = bootstrap.Modal.getInstance(document.getElementById("basicModal"));
            modal.hide();
            document.querySelectorAll(".modal-backdrop").forEach(el => el.remove());
            document.body.classList.remove("modal-open");
            document.body.style.overflow = "auto";
        });
        document.getElementById("referralSelect").addEventListener("change", function () {
            let selectedValue = this.value;
            if (selectedValue === "OutsideReferral") {
                document.getElementById('outsideReferralMR').style.display = 'block';
            } else {
                document.getElementById('outsideReferralMR').style.display = 'none';
            }
        });
        const referralSelect = document.getElementById("referralSelect");
        const outsideReferralMR = document.getElementById("outsideReferralMR");
        referralSelect.addEventListener("change", function () {
            const selectedValue = this.value;
            if (selectedValue === "OutsideReferral") {
                outsideReferralMR.style.display = "block";
                const myModal = new bootstrap.Modal(document.getElementById('basicModal'));
                myModal.show();
            } else {
                outsideReferralMR.style.display = "none";
            }
        });
        $('#isOutPatientAdded').val(1);
        $('#isOutPatientAddedAndOpen').val(1)
        const isOutPatientAdded = $('#isOutPatientAdded').val();
        const openStatus = Number(employeeData.op_registry_datas.op_registry.open_status);
        if (openStatus === 0) {
            $('#isOutPatientAddedAndOpen').val(0);
        }
        const isOutPatientAddedAndOpen = $('#isOutPatientAddedAndOpen').val();
        if (isOutPatientAdded === '1') {
            if (isOutPatientAddedAndOpen === '0') {
                showToast('info', 'Out Patient already added and closed');
                $('input, select, textarea').prop('disabled', true);
                $('#saveClose, #saveHR').prop('disabled', true);
                document.getElementById('addPrescription').disabled = true;
                document.getElementById('addTest').disabled = true;
            } else if (isOutPatientAddedAndOpen === '1') {
                showToast('info', 'Out Patient already added and still open');
            }
        } else {
            const now = new Date();
            const formattedNow = formatDateForInput(now);
            const oneDayLater = new Date(now);
            oneDayLater.setDate(now.getDate() + 1);
            const formattedLater = formatDateForInput(oneDayLater);
            $('#leave-from').val(formattedNow);
            $('#leave-upto, #outTime').val(formattedLater);
            $('#reporting-datetime, #incident-datetime').val(formattedNow);
            toggleIncidentFields();
            if (typeof $.fn.select2 !== 'undefined') {
                $('.select2').select2();
            }
        }
        document.getElementById('addPrescription').addEventListener('click', () => {
            const employeeId = $('#employeeId').val().toString().toLowerCase();
            const opRegistry = employeeData.op_registry_datas?.op_registry || {};
            if (!handleIncidentType()) return;
            if (!handleObservations()) return;
            sendHealthRegistryData(false, () => {
                const updatedOpRegistry = employeeData.op_registry_datas?.op_registry || {};
                if (updatedOpRegistry['op_registry_id']) {
                    window.location = '/prescription/add-employee-prescription/' + employeeId + '/op/' + updatedOpRegistry['op_registry_id'];
                } else {
                    window.location.reload();
                }
            });
        });
        document.getElementById('backToList').addEventListener('click', () => {
            window.location = "/ohc/health-registry/list-registry";
        });
        document.getElementById('addTest').addEventListener('click', () => {
            if (!handleIncidentType()) return;
            if (!handleObservations()) return;
            const redirectAfterSave = () => {
                const employeeId = $('#employeeId').val().toString().toLowerCase();
                window.location = '/ohc/health-registry/add-test/' + employeeId + '/op/' + employeeData.op_registry_datas?.op_registry?.op_registry_id;
            };
            sendHealthRegistryData(false, redirectAfterSave);
        });
        document.getElementById('saveClose').addEventListener('click', () => {
            if (!handleIncidentType()) return;
            if (!handleObservations()) return;
            sendHealthRegistryData(true, () => {
                window.location = '/ohc/health-registry/list-registry';
            });
        });
        document.getElementById('saveHR').addEventListener('click', () => {
            if (!handleIncidentType()) return;
            if (!handleObservations()) return;
            sendHealthRegistryData(false, (opRegistryId) => {
                const employeeId = $('#employeeId').val().toString().toLowerCase();
                window.location = '/ohc/health-registry/edit-registry/edit-outpatient/' + employeeId + '/op/' + opRegistryId;
            });
        });
        const spinnerLabel = document.getElementById('spinnerLabeltext');
        const spinner = document.getElementById('add-registry-spinner');
        const registryCard = document.getElementById('add-registry-card');
        let loadedCount = 0;
        const totalSteps = 7;
        function updateProgress(message) {
            loadedCount++;
            spinnerLabel.textContent = `${message} (${loadedCount}/${totalSteps})`;
        }
        const apiConfigs = [
            {
                url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllSymptoms',
                message: 'Loading Symptoms',
                selectId: 'select2Primary_symptoms'
            },
            {
                url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllDiagnosis',
                message: 'Loading Diagnosis',
                selectId: 'select2Primary_diagnosis'
            },
            {
                url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllMedicalSystem',
                message: 'Loading Medical Systems',
                selectId: 'select2Primary_medical_system'
            },
            {
                url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllBodyParts',
                message: 'Loading Body Parts',
                selectId: ['select2Primary_body_part', 'select2Primary_body_part_IA']
            },
            {
                url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllNatureOfInjury',
                message: 'Loading Nature of Injury',
                selectId: 'select2Primary_nature_of_injury'
            },
            {
                url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllInjuryMechanism',
                message: 'Loading Injury Mechanism',
                selectId: 'select2Primary_injury_mechanism'
            },
            {
                url: 'https://login-users.hygeiaes.com/ohc/health-registry/getMRNumber',
                message: 'Loading MR Number',
                isMRNumber: true
            }
        ];
        function createApiRequest(config) {
            return new Promise((resolve, reject) => {
                const timeoutId = setTimeout(() => {
                    reject(new Error(`Timeout: ${config.message} took too long`));
                }, 10000);
                apiRequest({
                    url: config.url,
                    timeout: 8000,
                    onSuccess: function (response) {
                        clearTimeout(timeoutId);
                        if (response.result && response.message) {
                            if (config.isMRNumber) {
                            } else if (Array.isArray(config.selectId)) {
                                config.selectId.forEach(id => populateSelect(id, response.message));
                            } else {
                                populateSelect(config.selectId, response.message);
                            }
                        }
                        updateProgress(config.message + ' ✓');
                        resolve(config);
                    },
                    onError: function (error) {
                        clearTimeout(timeoutId);
                        console.warn(`Warning: ${config.message} failed, continuing...`, error);
                        updateProgress(config.message + ' ⚠');
                        resolve(config);
                    }
                });
            });
        }
        spinnerLabel.textContent = 'Initializing data load...';
        const startTime = Date.now();
        const allRequests = apiConfigs.map(config => createApiRequest(config));
        Promise.allSettled(allRequests)
            .then((results) => {
                const loadTime = ((Date.now() - startTime) / 1000).toFixed(1);
                console.log(`All API requests completed in ${loadTime}s`);
                const successful = results.filter(r => r.status === 'fulfilled').length;
                const failed = results.filter(r => r.status === 'rejected').length;
                if (failed > 0) {
                    console.warn(`${failed} out of ${totalSteps} requests failed`);
                    showToast('warning', 'Partial Load', `${successful}/${totalSteps} data sources loaded successfully`);
                }
                spinnerLabel.textContent = 'Finalizing setup...';
                setTimeout(() => {
                    if (isOutPatientAdded) {
                        populateAddedOutpatientData();
                    }
                    spinner.style.display = 'none';
                    registryCard.style.display = 'block';
                    console.log('Page ready for user interaction');
                }, 200);
            })
            .catch((error) => {
                console.error('Unexpected error in parallel loading:', error);
                setTimeout(() => {
                    if (isOutPatientAdded) {
                        populateAddedOutpatientData();
                    }
                    spinner.style.display = 'none';
                    registryCard.style.display = 'block';
                    showToast('warning', 'Load Issues', 'Some data may be missing, but you can continue');
                }, 1000);
            });
        const fallbackTimer = setTimeout(() => {
            if (spinner.style.display !== 'none') {
                console.log('Showing form early due to slow network');
                spinner.style.display = 'none';
                registryCard.style.display = 'block';
                showToast('info', 'Loading...', 'Form ready, data still loading in background');
            }
        }, 3000);
        Promise.allSettled(allRequests).then(() => {
            clearTimeout(fallbackTimer);
        });
    });
</script>
@endsection