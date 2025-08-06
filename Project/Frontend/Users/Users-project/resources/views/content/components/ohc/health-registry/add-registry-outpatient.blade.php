@extends('layouts/layoutMaster')
@section('title', 'Incident Report Form')
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/typeahead-js/typeahead.scss', 'resources/assets/vendor/libs/tagify/tagify.scss', 'resources/assets/vendor/libs/spinkit/spinkit.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js', 'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/typeahead-js/typeahead.js', 'resources/assets/vendor/libs/tagify/tagify.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/bloodhound/bloodhound.js'])
@endsection
@section('page-script')
    @vite(['resources/assets/js/form-validation.js', 'resources/assets/js/forms-selects.js', 'resources/assets/js/extended-ui-sweetalert2.js', 'resources/assets/js/forms-tagify.js', 'resources/assets/js/forms-typeahead.js'])
@endsection
@section('content')
<link rel="stylesheet" href="/lib/css/page-styles/add-registry-outpatient.css">
<div class="add-registry-spinner" id="add-registry-spinner" style="display:block;">
<div class="spinner-card">
    <div class="spinner-container">
        <div class="sk-bounce sk-primary">
            <div class="sk-bounce-dot"></div>
            <div class="sk-bounce-dot"></div>
        </div><label id="spinnerLabeltext">Retrieving data...</label>
    </div>
</div>
</div>
<div style="display:none;" id="add-registry-card">
<div class="card mb-4">
    <div class="card-header text-white p-2 border-0 rounded-top" style="background-color:#6B1BC7;">
        <div class="row">
            <div class="col-md-6 d-flex position-relative">
                <div class="me-3 d-flex align-items-center"><img
                        src="https://t3.ftcdn.net/jpg/01/65/63/94/360_F_165639425_kRh61s497pV7IOPAjwjme1btB8ICkV0L.jpg"
                        alt="Profile" class="rounded" width="60"></div>
                <div class="d-flex flex-column justify-content-center">
                    <h6 class="text-warning mb-1" style="color:#ffff00 !important;"><input type="hidden"
                            name="employeeId" id="employeeId" value="{{ $employeeData['employee_id'] ?? '' }}"><input
                            type="hidden" name="isOutPatientAdded" id="isOutPatientAdded"
                            value="{{ $employeeData['employee_is_outpatient_added'] ?? '' }}"><input type="hidden"
                            name="isOutPatientAddedAndOpen" id="isOutPatientAddedAndOpen"
                            value="{{ $employeeData['employee_is_outpatient_open'] ?? '' }}">{{ strtoupper($employeeData['employee_firstname'] ?? '') }}
                        {{ strtoupper($employeeData['employee_lastname'] ?? '') }} -
                        {{ $employeeData['employee_id'] ?? '' }}</h6>
                    <p class="mb-1">{{ $employeeData['employee_age'] ?? 'N/A' }} /
                        {{ $employeeData['employee_gender'] ?? 'N/A' }}</p>
                    <p class="mb-0">{{ ucwords(strtolower($employeeData['employee_designation'] ?? 'N/A')) }},
                        {{ ucwords(strtolower($employeeData['employee_department'] ?? 'N/A')) }}</p>
                </div>
                <div
                    class="position-absolute end-0 top-0 bottom-0 d-flex flex-column justify-content-end me-4 py-2">
                    <button type="button" class="btn btn-sm"
                        style="background-color:#ffffff;color:#6a0dad;border:none;padding:6px 12px;border-radius:8px;font-weight:bold;width:auto;">Edit
                        Profile</button></div>
                <div class="position-absolute end-0 top-0 bottom-0 border-end border-light"></div>
            </div>
            <div class="col-md-6">
                <div class="ps-md-4 d-flex flex-column justify-content-center h-100">
                    <p class="mb-2"><strong>Conditions:</strong>
                        {{ !empty($employeeData['healthParameters']['Published Conditions']) ? implode(', ', $employeeData['healthParameters']['Published Conditions']) : 'N/A' }}
                    </p>
                    <p class="mb-2"><strong>Allergy Ingredients:</strong>
                        {{ !empty($employeeData['healthParameters']['Allergic Ingredients']) ? implode(', ', $employeeData['healthParameters']['Allergic Ingredients']) : 'N/A' }}
                    </p>
                    <p class="mb-0"><strong>Food Allergy:</strong>
                        {{ !empty($employeeData['healthParameters']['Allergic Foods']) ? implode(', ', $employeeData['healthParameters']['Allergic Foods']) : 'N/A' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body pt-3">
        <div class="row g-2">
            <div class="col-md-3"><label class="form-label">Reporting Date & Time</label><input
                    class="form-control form-control-sm" type="datetime-local" id="reporting-datetime" /></div>
            <div class="col-md-3"><label class="form-label">Incident Date & Time</label><input
                    class="form-control form-control-sm" type="datetime-local" id="incident-datetime" /></div>
            <div class="col-md-3"><label class="form-label">Work Shift</label><select
                    class="form-select form-select-sm" id="workShift">
                    <option value="4" selected>General Shift</option>
                    <option value="1">First Shift</option>
                    <option value="2">Second Shift</option>
                    <option value="3">Third Shift</option>
                </select></div>
            <div class="col-md-3"><label class="form-label">First Aid by</label><input type="text"
                    class="form-control form-control-sm" placeholder="John Doe"
                    value="{{ $employeeData['employee_firstname'] . ' ' . $employeeData['employee_lastname'] }}"
                    id="firstAidBy" /></div>
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
        <div class="me-2" style="width:160px;"><select class="form-select form-select-sm w-100" id="incidentType"
                onchange="toggleIncidentFields()"></select></div>
        <div style="width:160px;"><select class="form-select form-select-sm w-100" id="doctorSelect">
                <option value="0" selected>Select Doctor</option>
                <option value="1">Doctor 1</option>
                <option value="2">Doctor 2</option>
            </select></div>
    </div>
</div>
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body p-3">
                <div class="row mb-2 align-items-center">
                    <div class="col-5"><label class="form-label mb-0">Temperature</label></div>
                    <div class="col-3 px-0"><input type="text" class="form-control form-control-sm"
                            id="vpTemperature_167" /></div>
                    <div class="col-4"><span class="form-text mb-0 ms-2">°F</span></div>
                </div>
                <div class="row mb-2 align-items-center">
                    <div class="col-5"><label class="form-label mb-0">BP Systolic/Diastolic</label><small
                            class="d-block">mm/hg</small></div>
                    <div class="col-7 px-0 d-flex"><input type="text"
                            class="form-control form-control-sm me-1" style="width:40%;"
                            id="vpSystolic_168" /><input type="text" class="form-control form-control-sm"
                            style="width:40%;" id="vpDiastolic_169" /></div>
                </div>
                <div class="row mb-2 align-items-center">
                    <div class="col-5"><label class="form-label mb-0">Pulse Rate</label></div>
                    <div class="col-3 px-0"><input type="text" class="form-control form-control-sm"
                            id="vpPulseRate_170" /></div>
                    <div class="col-4"><span class="form-text mb-0 ms-2">Beats/min</span></div>
                </div>
                <div class="row mb-2 align-items-center">
                    <div class="col-5"><label class="form-label mb-0">Respiratory</label></div>
                    <div class="col-3 px-0"><input type="text" class="form-control form-control-sm"
                            id="vpRespiratory_171" /></div>
                    <div class="col-4"><span class="form-text mb-0 ms-2">bpm</span></div>
                </div>
                <div class="row mb-2 align-items-center">
                    <div class="col-5"><label class="form-label mb-0">SPO2</label></div>
                    <div class="col-3 px-0"><input type="text" class="form-control form-control-sm"
                            id="vpSPO2_172" /></div>
                    <div class="col-4"><span class="form-text mb-0 ms-2">%</span></div>
                </div>
                <div class="row align-items-center">
                    <div class="col-5"><label class="form-label mb-0">Random Glucose</label></div>
                    <div class="col-3 px-0"><input type="text" class="form-control form-control-sm"
                            id="vpRandomGlucose_173" /></div>
                    <div class="col-4"><span class="form-text mb-0 ms-2">mg/dl</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm" id="medicalFields">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3"><label class="form-label text-primary mb-3"
                            style="font-size:15px;"><strong>Body Part</strong></label></div>
                    <div class="col-md-9">
                        <div class="select2-primary body-part"><select id="select2Primary_body_part"
                                class="select2 form-select" multiple></select></div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><label class="form-label text-primary mb-3"
                            style="font-size:15px;"><strong>Symptoms</strong></label></div>
                    <div class="col-md-9">
                        <div class="select2-primary symptoms"><select id="select2Primary_symptoms"
                                class="select2 form-select" multiple></select></div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><label class="form-label text-primary mb-3"
                            style="font-size:15px;"><strong>Medical System</strong></label></div>
                    <div class="col-md-9">
                        <div class="select2-primary medical-system"><select id="select2Primary_medical_system"
                                class="select2 form-select" multiple></select></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3"><label class="form-label text-primary mb-3"
                            style="font-size:15px;"><strong>Diagnosis</strong></label></div>
                    <div class="col-md-9">
                        <div class="select2-primary diagnosis"><select id="select2Primary_diagnosis"
                                class="select2 form-select" multiple></select></div>
                    </div>
                </div>
                <div class="row mt-3" id="medicalInjuryColor" style="display: none;">
                    <div class="col-md-3">
                        <label class="form-label text-primary mb-3" style="font-size:15px;"><strong>Injury Color</strong></label>
                    </div>
                    <div class="col-md-9">
                        <div class="pt-1" id="medicalInjuryColorOptions"></div>
                </div>
            </div>

            </div>
        </div>
        <div class="card shadow-sm" id="industrialFields" style="display:none;">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4"><label class="form-label text-primary mb-3"
                                style="font-size:15px;color:#6c5ce7 !important;font-weight:500;"><strong>Injury
                                    Color</strong></label>
                            <div id="industrialInjuryColorOptions"></div>
                        </div>
                        <div class="mb-4"><label class="form-label text-primary mb-2"
                                style="font-size:15px;color:#6c5ce7 !important;font-weight:500;"><strong>Side of
                                    Body</strong></label>
                            <div>
                                <div class="d-flex">
                                    <div class="form-check me-3"><input class="form-check-input" type="checkbox"
                                            id="leftSide" style="width:14px;height:14px;"><label
                                            class="form-check-label" for="leftSide">Left</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox"
                                            id="rightSide" style="width:14px;height:14px;"><label
                                            class="form-check-label" for="rightSide">Right</label></div>
                                </div>
                            </div>
                        </div>
                        <div id="siteOfInjury">
                            <div class="mb-4"><label class="form-label text-primary mb-2"
                                    style="font-size:15px;color:#6c5ce7 !important;font-weight:500;"><strong>Site
                                        of Injury</strong></label>
                                <div>
                                    <div class="d-flex">
                                        <div class="form-check me-3"><input
                                                class="form-check-input site-of-injury" type="checkbox"
                                                id="shopFloor" style="width:14px;height:14px;"
                                                onclick="toggleCheckbox('shopFloor')"><label
                                                class="form-check-label" for="shopFloor">Shop Floor</label></div>
                                        <div class="form-check"><input class="form-check-input site-of-injury"
                                                type="checkbox" id="nonShopFloor" style="width:14px;height:14px;"
                                                onclick="toggleCheckbox('nonShopFloor')"><label
                                                class="form-check-label" for="nonShopFloor">Non-Shop Floor</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4"><label class="form-label text-primary mb-2"
                                style="font-size:15px;color:#6c5ce7 !important;font-weight:500;"><strong>Nature of
                                    Injury</strong></label>
                            <div class="select2-primary nature-of-injury"><select
                                    id="select2Primary_nature_of_injury" class="select2 form-select"
                                    multiple></select></div>
                        </div>
                        <div class="mb-4"><label class="form-label text-primary mb-2"
                                style="font-size:15px;color:#6c5ce7 !important;font-weight:500;"><strong>Body
                                    Part</strong></label>
                            <div class="select2-primary body-part-injury"><select id="select2Primary_body_part_IA"
                                    class="select2 form-select" multiple></select></div>
                        </div>
                        <div class="mb-4"><label class="form-label text-primary mb-2"
                                style="font-size:15px;color:#6c5ce7 !important;font-weight:500;"><strong>Mechanism
                                    of Injury</strong></label>
                            <div class="select2-primary injury-mechanism"><select
                                    id="select2Primary_injury_mechanism" class="select2 form-select"
                                    multiple></select></div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12"><label class="form-label text-primary mb-2"
                            style="font-size:15px;color:#6c5ce7 !important;font-weight:500;"><strong>Description of
                                Accident Occurrence</strong></label>
                        <textarea class="form-control" id="injury_description" rows="1"
                            placeholder="Enter detailed description of the accident"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm" id="outsideFields" style="display:none;">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4"><label class="form-label text-primary mb-3"
                                style="font-size:15px;color:#6c5ce7 !important;font-weight:500;"><strong>Injury
                                    Color</strong></label>
                            <div id="outsideInjuryColorOptions"></div>
                        </div>
                        <div class="mb-4"><label class="form-label text-primary mb-2"
                                style="font-size:15px;color:#6c5ce7 !important;font-weight:500;"><strong>Side of
                                    Body</strong></label>
                            <div>
                                <div class="d-flex">
                                    <div class="form-check me-3"><input class="form-check-input" type="checkbox"
                                            id="leftSideOutside" style="width:14px;height:14px;"><label
                                            class="form-check-label" for="leftSideOutside">Left</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox"
                                            id="rightSideOutside" style="width:14px;height:14px;"><label
                                            class="form-check-label" for="rightSideOutside">Right</label></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4"><label class="form-label text-primary mb-2"
                                style="font-size:15px;color:#6c5ce7 !important;font-weight:500;"><strong>Nature of
                                    Injury</strong></label>
                            <div class="select2-primary nature-of-injury"><select
                                    id="select2Primary_nature_of_injury_outside" class="select2 form-select"
                                    multiple></select></div>
                        </div>
                        <div class="mb-4"><label class="form-label text-primary mb-2"
                                style="font-size:15px;color:#6c5ce7 !important;font-weight:500;"><strong>Body
                                    Part</strong></label>
                            <div class="select2-primary body-part-injury"><select
                                    id="select2Primary_body_part_outside" class="select2 form-select"
                                    multiple></select></div>
                        </div>
                        <div class="mb-4"><label class="form-label text-primary mb-2"
                                style="font-size:15px;color:#6c5ce7 !important;font-weight:500;"><strong>Mechanism
                                    of Injury</strong></label>
                            <div class="select2-primary injury-mechanism"><select
                                    id="select2Primary_injury_mechanism_outside" class="select2 form-select"
                                    multiple></select></div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12"><label class="form-label text-primary mb-2"
                            style="font-size:15px;color:#6c5ce7 !important;font-weight:500;"><strong>Description of
                                Accident Occurrence</strong></label>
                        <textarea class="form-control" id="injury_description_outside" rows="1"
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
                    <div class="col-md-4"><label class="form-label">Leave From</label></div>
                    <div class="col-md-8"><input class="form-control" type="datetime-local" id="leave-from" />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><label class="form-label">Leave Upto</label></div>
                    <div class="col-md-8"><input class="form-control" type="datetime-local" id="leave-upto" />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><label class="form-label">Lost Hours</label></div>
                    <div class="col-md-8"><input type="text" id="lostHours" class="form-control"
                            value="00:00" /></div>
                </div>
                <div class="row">
                    <div class="col-md-4"><label class="form-label">Out Time</label></div>
                    <div class="col-md-8"><input class="form-control" id="outTime" type="datetime-local"
                            value="2021-06-18T12:30:00" /></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <h5 class="text-primary mb-3">Observations</h5>
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4"><label class="form-label">Doctor Notes</label></div>
                    <div class="col-md-8">
                        <textarea class="form-control" rows="2" id="doctorNotes"></textarea>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><label class="form-label">Medical History</label></div>
                    <div class="col-md-6">
                        <textarea class="form-control" rows="2" id="medicalHistory"></textarea>
                    </div>
                    <div class="col-md-2"><button class="btn btn-primary"
                            style="font-size:12px;padding:4px 8px;">View Past History</button></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><label class="form-label">Referral</label></div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6"><select class="form-select" id="referralSelect">
                                    <option value="noOutsideReferral">No Outside Referral</option>
                                    <option value="OutsideReferral">Outside Referral</option>
                                    <option value="ama">AMA</option>
                                </select></div>
                            <div class="col-md-6" style="display:none;" id="outsideReferralMR">
                                <div class="d-flex align-items-center">
                                    <div><strong>
                                            <div id="outsideReferralHospitalName"
                                                style="font-weight:bold;cursor:pointer;"></div>
                                        </strong></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex gap-3">
                            <div class="form-check"><input class="form-check-input" type="checkbox"
                                    id="movementSlip"><label class="form-check-label" for="movementSlip">Movement
                                    Slip</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox"
                                    id="fitnessCert"><label class="form-check-label" for="fitnessCert">Fitness
                                    Certificate</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox"
                                    id="physiotherapy"><label class="form-check-label"
                                    for="physiotherapy">Physiotherapy</label></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
    <div class="row mt-4">
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
                    <button class="btn btn-warning me-2" id="addPrescription">Add Prescription</button>
                    <button class="btn btn-warning me-2" id="addTest">Add Test</button>
                    <button class="btn btn-primary me-2" id="saveClose">Save & Close</button>
                    <button class="btn btn-secondary" id="saveHR">Save Health Registry</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hospital Admission Details</h5><button type="button"
                        class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6"><label for="hospital_id" class="form-label">Hospital</label><select
                                name="hospital_id" id="hospital_id" class="form-select">
                                <option value="">-- Select Hospital --</option>
                                <option value="1">City Hospital</option>
                                <option value="2">State Medical</option>
                                <option value="0">Other</option>
                            </select></div>
                        <div class="col-md-6" id="hospital_name_div" style="display:none;"><label
                                for="hospital_name" class="form-label">Hospital Name</label><input type="text"
                                name="hospitalName" id="hospitalName" class="form-control"
                                placeholder="Enter Hospital Name"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-check"><input class="form-check-input" type="checkbox"
                                    id="esiScheme"><label class="form-check-label" for="esiScheme">Patient
                                    covered by E.S.I Scheme</label></div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12"><label for="vehicleType" class="form-label">Select
                                Vehicle</label><select id="vehicleType" class="form-select">
                                <option value="own">Own Vehicle</option>
                                <option value="ambulance">Ambulance</option>
                            </select></div>
                    </div>
                    <div id="ambulanceFields" class="hidden">
                        <div class="row mb-3">
                            <div class="col-md-6"><label for="driverName" class="form-label">Name of
                                    Driver</label><input type="text" id="driverName" class="form-control"
                                    placeholder="Enter Driver Name"></div>
                            <div class="col-md-6"><label for="ambulanceNumber" class="form-label">Ambulance
                                    Number</label><input type="text" id="ambulanceNumber" class="form-control"
                                    placeholder="Enter Ambulance Number"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12"><label for="accompaniedBy" class="form-label">Accompanied
                                    By</label><input type="text" id="accompaniedBy" class="form-control"
                                    placeholder="Enter Name / Phone Num / Employee Id"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6"><label for="odometerOut" class="form-label">Odometer
                                    Out</label><input type="number" id="odometerOut" class="form-control"
                                    placeholder="Enter Odometer Out"></div>
                            <div class="col-md-6"><label for="odometerIn" class="form-label">Odometer
                                    In</label><input type="number" id="odometerIn" class="form-control"
                                    placeholder="Enter Odometer In"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6"><label for="timeOut" class="form-label">Time Out</label><input
                                    type="time" id="timeOut" class="form-control"></div>
                            <div class="col-md-6"><label for="timeIn" class="form-label">Time In</label><input
                                    type="time" id="timeIn" class="form-control"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Close</button><button type="button" class="btn btn-primary"
                        id="saveChangesModal">Save Changes</button></div>
            </div>
        </div>
    </div>
</div>
<script>
var employeeData = <?php echo json_encode($employeeData); ?>;
</script>
<script src="/lib/js/page-scripts/add-registry-outpatient.js"></script>
@endsection