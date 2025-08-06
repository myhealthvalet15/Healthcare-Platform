@extends('layouts.layoutMaster')
@section('title', 'Add Test')
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/typeahead-js/typeahead.scss',
'resources/assets/vendor/libs/tagify/tagify.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
])
@endsection
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
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
$url = request()->url();
$lastSegment = basename($url);
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
<link rel="stylesheet" href="/lib/css/page-styles/add-test.css">
<?php
$opRegistryId =
$employeeData['op_registry_datas']['op_registry']['op_registry_id'] ?? null;
//print_r($employeeData);
?>
<script>
    var employeeData = <?php echo json_encode($employeeData); ?>;
    var isOpRegistryIdIsthere = <?php echo json_encode($opRegistryId); ?>;
</script>
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
                    <div class="date-container">
                        <input class="form-control custom-date" type="datetime-local" value="" id="html5-datetime-local-input">
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
    <br />
    @php
    $severityData = $employeeData['incidentTypeColorCodesAdded'] ?? '';
    $severityParts = explode('_', $severityData);
    $severityText = !empty($severityParts[0]) ? substr($severityParts[0], 0, 2) : 'N/A';
    $colorCode = !empty($severityParts[1]) ? trim($severityParts[1]) : '#87CEEB';
    @endphp
    @if($employeeData['showWhiteStrip'] == 1)
    <div class="medical-info-wrapper">
        @if($referal_type == 'industrialAccident' || $referal_type == 'outsideAccident')
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
                <p class="info-content">{{ implode(', ', $medical_systems) }}</p>
            </div>
            <div class="medical-info-column">
                <p class="info-title">Diagnosis</p>
                <p class="info-content">{{ implode(', ', $diagnosis) }}</p>
            </div>
            @elseif($referal_type == 'industrialAccident')
            <div class="medical-info-column">
                <p class="info-title">Mechanism Injuries</p>
                <p class="info-content">{{ implode(', ', $mechanism_injuries) }}</p>
            </div>
            <div class="medical-info-column">
                <p class="info-title">Nature Injuries</p>
                <p class="info-content">{{ implode(', ', $nature_injuries) }}</p>
            </div>
            <div class="medical-info-column">
                <p class="info-title">Site of Injury</p>
                <p class="info-content">{{ implode(', ', array_map(fn($s) => ucwords(str_replace(['shopFloor',
                    'nonShopFloor'], ['Shop Floor', 'Non Shop Floor'], $s)), array_keys(array_filter((array)
                    $site_of_injury)))) }}</p>
            </div>
            <div class="medical-info-column">
                <p class="info-title">Body Side</p>
                <p class="info-content">{{ implode(', ', array_map('ucwords', array_keys(array_filter((array)
                    $body_side)))) }}</p>
            </div>
            @elseif($referal_type == 'outsideAccident')
            <div class="medical-info-column">
                <p class="info-title">Mechanism Injuries</p>
                <p class="info-content">{{ implode(', ', $mechanism_injuries) }}</p>
            </div>
            <div class="medical-info-column">
                <p class="info-title">Nature Injuries</p>
                <p class="info-content">{{ implode(', ', $nature_injuries) }}</p>
            </div>
            <div class="medical-info-column">
                <p class="info-title">Body Side</p>
                <p class="info-content">{{ implode(', ', array_map('ucwords', array_keys(array_filter((array)
                    $body_side)))) }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
<div class="card p-4">
    <div class="d-flex flex-column">
        <div>
            <input type="hidden" name="employeeId" id="employeeId" value="{{ $employeeData['employee_id'] ?? '' }}">
            <input type="hidden" name="isOutPatientAddedAndOpen" id="isOutPatientAddedAndOpen"
                value="{{ $employeeData['employee_is_outpatient_open'] ?? '' }}">
            <label class="form-label mb-3">Select Tests</label>
            <div class="select2-primary mb-4">
                <select id="select2Primary_tests" class="select2 form-select" multiple>
                </select>
            </div>
            <div class="d-flex justify-content-between mt-2">
                <?php
                if (
                isset($employeeData['op_registry_datas']) &&
                isset($employeeData['op_registry_datas']['op_registry']) &&
                isset($employeeData['op_registry_datas']['op_registry']['op_registry_id'])
                &&
                $employeeData['op_registry_datas']['op_registry']['op_registry_id']
                > 0
                ) {
                ?>
                <button type="button" class="btn btn-warning" id="backToHealthReg">
                    Back to Health Registry
                </button>
                <?php
                }
                ?>
                <button type="button" class="btn btn-primary" id="addTest">Add
                    Tests</button>
            </div>
        </div>
    </div>
</div>
<script src="/lib/js/page-scripts/health-registry-add-test.js"></script>
@endsection