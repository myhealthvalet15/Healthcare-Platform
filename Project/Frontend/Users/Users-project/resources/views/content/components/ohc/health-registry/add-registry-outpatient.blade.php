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
<style>
.hidden {
    display: none
}
.add-registry-spinner {
    display: flex;
    justify-content: center;
    align-items: center;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1050
}
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
    text-align: center
}
.spinner-container {
    display: flex;
    flex-direction: column;
    align-items: center
}
.sk-bounce {
    display: flex;
    justify-content: space-between;
    width: 50px
}
.sk-bounce-dot {
    width: 30px;
    height: 30px;
    margin: 0 5px;
    background-color: #007bff;
    border-radius: 50%;
    animation: sk-bounce 1.4s infinite ease-in-out both
}
@keyframes sk-bounce {
    0%,
    80%,
    100% {
        transform: scale(0);
        opacity: 0.3
    }
    40% {
        transform: scale(1);
        opacity: 1
    }
}
.custom-radio {
    position: relative;
    cursor: pointer
}
</style>
<script>
var employeeData = <?php echo json_encode($employeeData); ?>;
</script>
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
    var incidentTypesData = employeeData.incidentTypeColorCodes || [];
    function populateIncidentTypes() {
        const s = document.getElementById('incidentType');
        while (s.firstChild) s.removeChild(s.firstChild);
        let selected = null,
            medicalIllness = null;
        incidentTypesData.forEach(i => {
            const o = document.createElement('option');
            const v = {
                'Medical Illness': 'medicalIllness',
                'Industrial Accident': 'industrialAccident',
                'Outside Accident': 'outsideAccident'
            };
            o.value = v[i.incident_type_name] || i.incident_type_name.replace(/\s+/g, '').toLowerCase();
            o.textContent = i.incident_type_name;
            o.dataset.incidentId = i.incident_type_id;
            s.appendChild(o);
            if (i.incident_type_name === 'Medical Illness') medicalIllness = o;
            if (!selected) selected = o
        });
        if (medicalIllness) {
            medicalIllness.selected = true
        } else if (selected) {
            selected.selected = true
        }
    }
    function createInjuryColorOptions(cId, colors) {
        const c = document.getElementById(cId);
        if (!c) return;
        while (c.firstChild) c.removeChild(c.firstChild);
        if (!colors || Object.keys(colors).length === 0) {
            c.style.display = 'none';
            return
        }
        const f = document.createElement('div');
        f.className = 'd-flex flex-wrap';
        Object.entries(colors).forEach(([l, col]) => {
            const id = `${cId}_${l.replace(/\s+/g, '').toLowerCase()}`;
            const fc = document.createElement('div');
            fc.className = 'form-check me-3 mb-2';
            const inp = document.createElement('input');
            inp.className = 'form-check-input custom-radio';
            inp.type = 'radio';
            inp.name = 'injury_color_text';
            inp.id = id;
            inp.value = `${l}_${col}`;
            inp.dataset.color = col;
            inp.dataset.label = l;
            const lbl = document.createElement('label');
            lbl.className = 'form-check-label';
            lbl.setAttribute('for', id);
            lbl.textContent = l;
            const st = document.createElement('style');
            st.textContent =
                `#${id}:checked{background-color:${col} !important;border-color:${col} !important;}`;
            document.head.appendChild(st);
            fc.appendChild(inp);
            fc.appendChild(lbl);
            f.appendChild(fc)
        });
        c.appendChild(f);
        c.style.display = 'block'
    }
    function toggleIncidentFields() {
        const t = document.getElementById('incidentType').value;
        const si = incidentTypesData.find(i => {
            const v = {
                'Medical Illness': 'medicalIllness',
                'Industrial Accident': 'industrialAccident',
                'Outside Accident': 'outsideAccident'
            };
            return (v[i.incident_type_name] || i.incident_type_name.replace(/\s+/g, '').toLowerCase()) === t
        });
        const mf = document.getElementById('medicalFields');
        const inf = document.getElementById('industrialFields');
        const of = document.getElementById('outsideFields');
        const soi = document.getElementById('siteOfInjury');
        const mic = document.getElementById('medicalInjuryColor');
        mf.style.display = 'none';
        inf.style.display = 'none';
        of.style.display = 'none';
        mic.style.display = 'none';
        if (t === 'medicalIllness') {
            mf.style.display = 'block';
            if (si && si.injury_color_types) {
                createInjuryColorOptions('medicalInjuryColorOptions', si.injury_color_types);
                mic.style.display = 'block'
            }
        } else if (t === 'industrialAccident') {
            inf.style.display = 'block';
            soi.style.display = 'flex';
            if (si && si.injury_color_types) createInjuryColorOptions('industrialInjuryColorOptions', si
                .injury_color_types)
        } else if (t === 'outsideAccident') {
            of.style.display = 'block';
            if (si && si.injury_color_types) createInjuryColorOptions('outsideInjuryColorOptions', si
                .injury_color_types)
        }
    }
    document.getElementById('vehicleType').addEventListener('change', function () {
        const a = document.getElementById('ambulanceFields');
        if (this.value === 'ambulance') a.classList.remove('hidden');
        else a.classList.add('hidden')
    });
    function populateSelect(sId, data) {
        const s = document.getElementById(sId);
        if (!s) return;
        while (s.firstChild) s.removeChild(s.firstChild);
        data.forEach(item => {
            let o = document.createElement('option');
            o.value = item.op_component_id;
            o.textContent = item.op_component_name;
            s.appendChild(o)
        });
        if ($(s).hasClass('select2')) $(s).trigger('change')
    }
    function selectValues(sId, sv = []) {
        const s = document.getElementById(sId);
        if (!s) return;
        const opts = s.options;
        for (let i = 0; i < opts.length; i++) {
            if (sv.includes(parseInt(opts[i].value))) opts[i].selected = true
        }
        if ($(s).hasClass('select2')) $(s).trigger('change')
    }
    function formatDateForInput(d) {
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        const h = String(d.getHours()).padStart(2, '0');
        const min = String(d.getMinutes()).padStart(2, '0');
        return `${y}-${m}-${day}T${h}:${min}`
    }
    function validateFields(f) {
        for (const [id, lbl] of Object.entries(f)) {
            const v = document.getElementById(id)?.value.trim() || '';
            if (!v) {
                showToast('error', `Please fill ${lbl}`);
                return false
            }
        }
        return true
    }
    function toggleCheckbox(sId) {
        const cbs = document.querySelectorAll('.site-of-injury');
        cbs.forEach(cb => {
            if (cb.id !== sId) cb.checked = false
        })
    }
    function handleIncidentType() {
        const t = document.getElementById('incidentType').value;
        const ds = document.getElementById('doctorSelect').value;
        const ic = document.querySelector('input[name="injury_color_text"]:checked');
        if (!ic) {
            showToast('error', 'Please select Injury Color');
            return false
        }
        if (!t) {
            showToast('error', 'Please select Incident Type');
            return false
        }
        const doc = {
            doctorId: ds,
            doctorName: $('#doctorSelect option:selected').text()
        };
        return true
    }
    function handleObservations() {
        const obs = {
            doctorNotes: $('#doctorNotes').val(),
            medicalHistory: $('#medicalHistory').val(),
            referral: $('#referralSelect').val(),
            movementSlip: $('#movementSlip').is(':checked'),
            fitnessCert: $('#fitnessCert').is(':checked'),
            physiotherapy: $('#physiotherapy').is(':checked')
        };
        if ($('#referralSelect').val() === "OutsideReferral") {
            const hn = document.getElementById("hospitalName").value;
            if (!hn) {
                showToast('error', 'Please fill Hospital Name');
                return false
            }
            const vt = document.getElementById("vehicleType").value;
            if (!vt) {
                showToast('error', 'Please select Vehicle Type');
                return false
            }
            if (vt === 'ambulance') {
                const dn = document.getElementById("driverName").value;
                const an = document.getElementById("ambulanceNumber").value;
                const oi = document.getElementById("odometerIn").value;
                const oo = document.getElementById("odometerOut").value;
                const ti = document.getElementById("timeIn").value;
                const to = document.getElementById("timeOut").value;
                if (!dn) {
                    showToast('error', 'Please fill Driver Name');
                    return false
                }
                if (!an) {
                    showToast('error', 'Please fill Ambulance Number');
                    return false
                }
                if (!oi) {
                    showToast('error', 'Please fill Odometer In');
                    return false
                }
                if (!oo) {
                    showToast('error', 'Please fill Odometer Out');
                    return false
                }
                if (!ti) {
                    showToast('error', 'Please fill Time In');
                    return false
                }
                if (!to) {
                    showToast('error', 'Please fill Time Out');
                    return false
                }
            }
        }
        return true
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
        }).then(result => {
            if (result.isConfirmed) {
                const hrd = {
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
                    incidentTypeId: $('#incidentType option:selected').data('incidentId'),
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
                const dId = $('#doctorSelect').val();
                const dName = $('#doctorSelect option:selected').text();
                if (dId && dName !== "Select Doctor") hrd.doctor = {
                    doctorId: dId,
                    doctorName: dName
                };
                if (hrd.incidentType === "medicalIllness") {
                    hrd.medicalFields = {
                        bodyPart: $('#select2Primary_body_part').val(),
                        symptoms: $('#select2Primary_symptoms').val(),
                        medicalSystem: $('#select2Primary_medical_system').val(),
                        diagnosis: $('#select2Primary_diagnosis').val(),
                        injuryColor: $('input[name="injury_color_text"]:checked').val()
                    }
                } else if (hrd.incidentType === "industrialAccident") {
                    hrd.industrialFields = {
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
                    }
                } else {
                    hrd.industrialFields = {
                        injuryColor: $('input[name="injury_color_text"]:checked').val(),
                        sideOfBody: {
                            left: $('#leftSideOutside').is(':checked'),
                            right: $('#rightSideOutside').is(':checked')
                        },
                        natureOfInjury: $('#select2Primary_nature_of_injury_outside').val(),
                        bodyPartIA: $('#select2Primary_body_part_outside').val(),
                        injuryMechanism: $('#select2Primary_injury_mechanism_outside').val(),
                        description: $('#injury_description_outside').val()
                    }
                }
                let ref = $('#referralSelect').val();
                hrd.referral = ref;
                if (ref === 'OutsideReferral') {
                    let hn = document.getElementById("hospitalName").value;
                    let es = document.getElementById("esiScheme").checked ? 1 : 0;
                    let vt = document.getElementById("vehicleType").value;
                    hrd.hospitalDetails = {
                        hospitalName: hn,
                        esiScheme: es,
                        vehicleType: vt
                    };
                    if (vt === 'ambulance') {
                        let dn = document.getElementById("driverName").value;
                        let an = document.getElementById("ambulanceNumber").value;
                        let ab = document.getElementById("accompaniedBy").value;
                        let oi = document.getElementById("odometerIn").value;
                        let oo = document.getElementById("odometerOut").value;
                        let ti = document.getElementById("timeIn").value;
                        let to = document.getElementById("timeOut").value;
                        hrd.hospitalDetails = {
                            driverName: dn,
                            hospitalName: hn,
                            esiScheme: es,
                            vehicleType: vt,
                            ambulanceNumber: an,
                            accompaniedBy: ab,
                            odometerIn: oi,
                            odometerOut: oo,
                            timeIn: ti,
                            timeOut: to
                        }
                    }
                }
                hrd.editExistingOne = 0;
                hrd.isFollowup = 0;
                apiRequest({
                    url: 'https://login-users.hygeiaes.com/ohc/health-registry/saveHealthRegistry',
                    method: 'POST',
                    data: hrd,
                    onSuccess: function (r) {
                        showToast('success', 'Success', 'Health registry saved successfully!');
                        onSuccessCallback(r.op_registry_id)
                    },
                    onError: function (e) {
                        console.error('Error saving health registry:', e);
                        showToast('error', 'Error', 'Failed to save health registry')
                    }
                })
            }
        })
    }
    function loadAllDataParallel() {
        const sl = document.getElementById('spinnerLabeltext');
        const sp = document.getElementById('add-registry-spinner');
        const rc = document.getElementById('add-registry-card');
        const iopa = $('#isOutPatientAdded').val();
        sl.textContent = 'Loading data...';
        const apis = [{
            url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllSymptoms',
            selectIds: ['select2Primary_symptoms'],
            name: 'Symptoms'
        }, {
            url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllDiagnosis',
            selectIds: ['select2Primary_diagnosis'],
            name: 'Diagnosis'
        }, {
            url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllMedicalSystem',
            selectIds: ['select2Primary_medical_system'],
            name: 'Medical Systems'
        }, {
            url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllBodyParts',
            selectIds: ['select2Primary_body_part', 'select2Primary_body_part_IA',
                'select2Primary_body_part_outside'
            ],
            name: 'Body Parts'
        }, {
            url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllNatureOfInjury',
            selectIds: ['select2Primary_nature_of_injury', 'select2Primary_nature_of_injury_outside'],
            name: 'Nature of Injury'
        }, {
            url: 'https://login-users.hygeiaes.com/ohc/health-registry/getAllInjuryMechanism',
            selectIds: ['select2Primary_injury_mechanism', 'select2Primary_injury_mechanism_outside'],
            name: 'Injury Mechanism'
        }, {
            url: 'https://login-users.hygeiaes.com/ohc/health-registry/getMRNumber',
            isMRNumber: true,
            name: 'MR Number'
        }];
        let cr = 0;
        const tr = apis.length;
        const ld = [];
        function up() {
            const p = Math.round((cr / tr) * 100);
            sl.textContent = `Loading data... ${p}% (${cr}/${tr})`
        }
        const aps = apis.map((req, idx) => {
            return new Promise((res, rej) => {
                apiRequest({
                    url: req.url,
                    onSuccess: function (resp) {
                        cr++;
                        up();
                        if (resp.result && resp.message) {
                            if (req.isMRNumber) { } else {
                                req.selectIds.forEach(sId => {
                                    populateSelect(sId, resp.message)
                                })
                            }
                        }
                        ld.push({
                            name: req.name,
                            success: true
                        });
                        res(resp)
                    },
                    onError: function (err) {
                        cr++;
                        up();
                        console.error(`Error fetching ${req.name}:`, err);
                        showToast('error', 'Error', `Failed to load ${req.name}`);
                        ld.push({
                            name: req.name,
                            success: false,
                            error: err
                        });
                        res(null)
                    }
                })
            })
        });
        Promise.all(aps).then(results => {
            const sl = ld.filter(i => i.success);
            const fl = ld.filter(i => !i.success);
            if (sl.length > 0) {
                if (iopa) showToast('success', 'Data Fetched Successfully.');
                if (fl.length > 0) {
                    console.warn('Some data failed to load:', fl.map(f => f.name));
                    showToast('warning', 'Warning', `Some data failed to load: ${fl.map(f => f.name).join(', ')}`)
                }
            }
            sl.textContent = "Preparing Outpatient Data...";
            setTimeout(() => {
                sp.style.display = 'none';
                rc.style.display = 'block'
            }, 500)
        }).catch(err => {
            console.error('Unexpected error during parallel loading:', err);
            showToast('error', 'Error', 'An unexpected error occurred while loading data');
            setTimeout(() => {
                sp.style.display = 'none';
                rc.style.display = 'block'
            }, 500)
        })
    }
    $(document).ready(function () {
        populateIncidentTypes();
        setTimeout(() => {
            toggleIncidentFields()
        }, 100);
        document.getElementById('hospital_id').addEventListener('change', function () {
            const hnd = document.getElementById('hospital_name_div');
            if (this.value === "0") {
                hnd.style.display = 'block'
            } else {
                hnd.style.display = 'none';
                document.getElementById('hospitalName').value = ''
            }
        });
        document.getElementById("saveChangesModal").addEventListener("click", function () {
            const hs = document.getElementById("hospital_id");
            const shi = hs.value;
            const hti = document.getElementById("hospitalName").value.trim();
            const hnf = document.getElementById("hospitalName");
            let shn = "";
            if (hnf) hnf.value = shn;
            if (shi === "0") {
                let shn = hti.replace(/[<>]/g, "");
                hnf.value = shn;
                document.getElementById("outsideReferralHospitalName").textContent = shn ||
                    "No Hospital Name Entered"
            } else {
                hnf.value = shi;
                const so = hs.options[hs.selectedIndex];
                document.getElementById("outsideReferralHospitalName").textContent = so.text ||
                    "No Hospital Selected"
            }
            const m = bootstrap.Modal.getInstance(document.getElementById("basicModal"));
            m.hide();
            document.querySelectorAll(".modal-backdrop").forEach(el => el.remove());
            document.body.classList.remove("modal-open");
            document.body.style.overflow = "auto"
        });
        document.getElementById("referralSelect").addEventListener("change", function () {
            let sv = this.value;
            if (sv === "OutsideReferral") document.getElementById('outsideReferralMR').style.display =
                'block';
            else document.getElementById('outsideReferralMR').style.display = 'none'
        });
        const rs = document.getElementById("referralSelect");
        const orm = document.getElementById("outsideReferralMR");
        rs.addEventListener("change", function () {
            const sv = this.value;
            if (sv === "OutsideReferral") {
                orm.style.display = "block";
                const mm = new bootstrap.Modal(document.getElementById('basicModal'));
                mm.show()
            } else orm.style.display = "none"
        });
        document.addEventListener("DOMContentLoaded", function () {
            const rne = document.getElementById("outsideReferralHospitalName");
            if (rne) {
                rne.addEventListener("click", function () {
                    const mm = new bootstrap.Modal(document.getElementById('basicModal'));
                    mm.show()
                })
            } else console.warn("Element #outsideReferralHospitalName not found.")
        });
        const iopa = $('#isOutPatientAdded').val();
        const iopaao = $('#isOutPatientAddedAndOpen').val();
        const now = new Date();
        const fdt1 = formatDateForInput(now);
        document.getElementById('leave-from').value = fdt1;
        const dl = new Date(now);
        dl.setDate(dl.getDate() + 1);
        document.getElementById('leave-upto').value = fdt1;
        document.getElementById('outTime').value = fdt1;
        const fdt2 = formatDateForInput(now);
        document.getElementById('reporting-datetime').value = fdt2;
        document.getElementById('incident-datetime').value = fdt2;
        toggleIncidentFields();
        if (typeof $.fn.select2 !== 'undefined') $('.select2').select2();
        document.getElementById('addPrescription').addEventListener('click', () => {
            const eid = $('#employeeId').val().toString().toLowerCase();
            const opr = employeeData.op_registry_datas?.op_registry || {};
            if (!handleIncidentType()) return;
            if (!handleObservations()) return;
            sendHealthRegistryData(false, orid => {
                window.location = '/prescription/add-employee-prescription/' + eid + '/op/' +
                    orid
            })
        });
        document.getElementById('addTest').addEventListener('click', () => {
            if (!handleIncidentType()) return;
            if (!handleObservations()) return;
            sendHealthRegistryData(false, orid => {
                const eid = $('#employeeId').val().toString().toLowerCase();
                window.location = '/ohc/health-registry/add-test/' + eid + '/op/' + orid
            })
        });
        document.getElementById('saveClose').addEventListener('click', () => {
            if (!handleIncidentType()) return;
            if (!handleObservations()) return;
            sendHealthRegistryData(true, orid => {
                window.location = '/ohc/health-registry/list-registry'
            })
        });
        document.getElementById('saveHR').addEventListener('click', () => {
            if (!handleIncidentType()) return;
            if (!handleObservations()) return;
            sendHealthRegistryData(false, orid => {
                const eid = $('#employeeId').val().toString().toLowerCase();
                window.location = '/ohc/health-registry/edit-registry/edit-outpatient/' + eid +
                    '/op/' + orid
            })
        });
        loadAllDataParallel()
    });
</script>
@endsection