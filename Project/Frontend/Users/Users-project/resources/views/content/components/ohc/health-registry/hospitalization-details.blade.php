@extends('layouts/layoutMaster')
@section('title', 'Add Hospitalization')
<!-- Vendor Styles -->
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/bs-stepper/bs-stepper.scss', 
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/spinkit/spinkit.scss'
])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite([

'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection

<!-- Page Scripts -->
@section('page-script')
@vite([
'resources/assets/js/form-wizard-numbered.js'
])
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('lib/js/page-scripts/hospitalization-details.js') }}?v={{ time() }}"></script>
   
  <script> 
  const bladeEmployeeId = "{{ $employee_id }}";
  const bladeEmployeeUserId = "{{ $employee_user_id }}";
  const employeeUserId = "{{ $employee_user_id }}";
  const opRegistryId = "{{ $op_registry_id }}";
</script>

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
@endphp<style>
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

<div class="container mt-4">
    <div class="card shadow-sm">
    

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



        <div class="card-body">
            <form id="hospitalizationForm" enctype="multipart/form-data">
                @csrf
<input type="hidden" name="employee_id" value="{{ $employee_id }}">
<input type="hidden" name="employee_user_id" value="{{ $employee_user_id }}">
<input type="hidden" name="op_registry_id" value="{{ $op_registry_id }}">
                <!-- Row 1: Hospital + Hospital Name (if other) + Doctor + Doctor Name (if other) -->
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label for="hospital_id" class="form-label">Hospital</label>
                        <select name="hospital_id" id="hospital_id" class="form-select">                            <option value="">-- Select Hospital --</option>
                            <option value="1">City Hospital</option>
                            <option value="2">State Medical</option>
                            <option value="0">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="hospital_name_div" style="display:none;">
                        <label for="hospital_name" class="form-label">Hospital Name</label>
                        <input type="text" name="hospital_name" class="form-control" placeholder="Enter Hospital Name">
                    </div>
                    <div class="col-md-3">
                        <label for="doctor_id" class="form-label">Doctor</label>
                        <select name="doctor_id" id="doctor_id" class="form-select">
                            <option value="">-- Select Doctor --</option>
                            <option value="101">Dr. Aditi Verma</option>
                            <option value="102">Dr. Rajeev Kumar</option>
                            <option value="103">Dr. Meera Singh</option>
                            <option value="0">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="doctor_name_div" style="display:none;">
                        <label for="doctor_name" class="form-label">Doctor Name</label>
                        <input type="text" name="doctor_name" class="form-control" placeholder="Enter Doctor Name">
                    </div>
                </div>
<!-- Row 2: From Date & To Date (side-by-side, compact) + Condition (right) -->
<div class="row g-3 mb-3 align-items-end">
    <!-- From & To Dates (compact, side-by-side) -->
   <div class="col-md-6">
        <div class="row g-2">
            <div class="col-md-6">
                <label for="from_date" class="form-label">From Date & Time</label>
                <input type="datetime-local" name="from_date" class="form-control form-control-sm"  style="height: 38px;" >
            </div>
            <div class="col-md-6">
                <label for="to_date" class="form-label">To Date & Time</label>
                <input type="datetime-local" name="to_date" class="form-control form-control-sm" style="height: 38px;">
            </div>
        </div>
    </div>

    <!-- Condition (full height on the right) -->
    <div class="col-md-6">
        <label for="conditionSelect" class="form-label">Condition</label>
        <select id="conditionSelect" name="condition[]" class="form-select" multiple>
            <!-- Options will be loaded dynamically -->
        </select>
    </div>
</div>

<!-- Row 3: Full-width Description -->
<div class="row g-3 mb-3">
    <div class="col-12">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3" placeholder="Enter description here..."></textarea>
    </div>
</div>


                <!-- Row 4: File Uploads -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Discharge Summary (1 file)</label>
                        <input type="file" name="discharge_summary" class="form-control" accept=".pdf,.jpg,.png">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Test Reports (up to 3 files)</label>
                        <input type="file" name="summary_reports[]" class="form-control" multiple accept=".pdf,.jpg,.png">
                    </div>
                </div>

                <!-- Submit --><div id="hospitalizationContainer"></div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection



