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

    .select2-results__option--group-header {
        font-weight: bold;
        color: #007bff;
        cursor: pointer;
        padding: 5px;
    }

    .select2-results__option--group-header:hover {
        color: white !important;
    }

    .select2-results__option:hover {
        background-color: transparent !important;
    }

    .select2-results__option--highlighted[aria-selected] .select2-results__option--group-header {
        color: white !important;
    }

    .custom-date {
        width: 185px;
        margin-left: 74px;
        background-color: #fff;
        color: #000;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 4px 8px;
    }

    .custom-date::-webkit-calendar-picker-indicator {
        filter: invert(0);
        /* default black icon */
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
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const now = new Date();
        const offset = now.getTimezoneOffset();
        const localDateTime = new Date(now.getTime() - offset * 60 * 1000)
            .toISOString()
            .slice(0, 16);
        const dateInput = document.getElementById('html5-datetime-local-input');
        dateInput.value = localDateTime;
        dateInput.max = localDateTime;
        const selectElement = document.getElementById('select2Primary_tests');
        const addButton = document.getElementById('addTest');
        const employeeId = employeeData['employee_id'];
        const backToHealthReg = document.getElementById('backToHealthReg');
        const testNamesByID = new Map();
        const addedTestIds = new Map();
        let selectedTestIds = new Set();
        const style = document.createElement('style');
        style.textContent = `
        .select2-results__option--group-header:hover {
            color: white !important;
        }
        .select2-results__options {
            scrollbar-width: auto;
            scrollbar-color: #007bff #f0f0f0;
        }
        .select2-results__options::-webkit-scrollbar {
            width: 12px;
        }
        .select2-results__options::-webkit-scrollbar-track {
            background: #f0f0f0;
        }
        .select2-results__options::-webkit-scrollbar-thumb {
            border-radius: 6px;
            border: 3px solid #f0f0f0;
        }
        .select2-container--open .select2-dropdown {
            max-height: 400px;
            overflow-y: auto;
        }
    `;
        document.head.appendChild(style);
        const pathSegments = window.location.pathname.split('/');
        const employeeIdIndex = pathSegments.indexOf('add-test') + 1;
        const employeeIds = pathSegments[employeeIdIndex];
        let mode = 'op'; // default mode is now 'op'
        let opId = '0'; // default to '0'
        let prescriptionId = null;

        // Determine if it's op mode or prescription mode
        if (pathSegments.length > employeeIdIndex + 1) {
            if (pathSegments[employeeIdIndex + 1] === 'op') {
                mode = 'op';
                opId = pathSegments[employeeIdIndex + 2] || '0';
            } else if (pathSegments[employeeIdIndex + 1] === 'prescription') {
                mode = 'prescription';
                prescriptionId = pathSegments[employeeIdIndex + 2];
            }
        }
        if (mode === 'prescription' && backToHealthReg) {
            backToHealthReg.style.display = 'none';
        }
        apiRequest({
            url: 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllSubGroup',
            method: 'GET',
            onSuccess: function (response) {
                if (response.result && response.data) {
                    const selectElement = document.getElementById('select2Primary_tests');
                    const subgroupsOptgroup = document.createElement('optgroup');
                    const testsInSubSubgroups = new Set();
                    if (Array.isArray(response.data.subgroups)) {
                        response.data.subgroups.forEach(subgroup => {
                            if (Array.isArray(subgroup.subgroups)) {
                                subgroup.subgroups.forEach(subSubgroup => {
                                    if (Array.isArray(subSubgroup.tests)) {
                                        subSubgroup.tests.forEach(test => {
                                            testsInSubSubgroups.add(test.master_test_id.toString());
                                        });
                                    }
                                });
                            }
                        });
                    }
                    if (Array.isArray(response.data.subgroups)) {
                        response.data.subgroups.forEach(subgroup => {
                            addedTestIds.clear();
                            const subgroupOption = document.createElement('option');
                            subgroupOption.value = `sg_${subgroup.test_group_id}`;
                            subgroupOption.textContent = `${subgroup.mother_group}: ${subgroup.test_group_name}`;
                            subgroupOption.classList.add('group-header');
                            subgroupsOptgroup.appendChild(subgroupOption);
                            const subgroupTestIds = [];
                            if (Array.isArray(subgroup.tests) && subgroup.tests.length > 0) {
                                const filteredTests = subgroup.tests.filter(test =>
                                    !testsInSubSubgroups.has(test.master_test_id.toString())
                                );
                                filteredTests.forEach(test => {
                                    const testId = test.master_test_id.toString();
                                    subgroupTestIds.push(testId);
                                    testNamesByID.set(testId, test.test_name);
                                    addedTestIds.set(testId, 'direct');
                                    const option = document.createElement('option');
                                    option.value = testId;
                                    option.textContent = `  — ${test.test_name}`;
                                    option.dataset.parentGroup = subgroup.test_group_id;
                                    option.dataset.originalGroup = 'subgroup';
                                    subgroupsOptgroup.appendChild(option);
                                });
                            }
                            subgroupOption.dataset.testIds = JSON.stringify(subgroupTestIds);
                            if (Array.isArray(subgroup.subgroups)) {
                                subgroup.subgroups.forEach(subSubgroup => {
                                    if (!Array.isArray(subSubgroup.tests) || subSubgroup.tests.length === 0) {
                                        return;
                                    }
                                    const subSubgroupOption = document.createElement('option');
                                    subSubgroupOption.value = `ssg_${subSubgroup.test_group_id}`;
                                    subSubgroupOption.textContent = `  — ${subSubgroup.test_group_name}`;
                                    subSubgroupOption.classList.add('group-header');
                                    subSubgroupOption.dataset.parentGroup = subgroup.test_group_id;
                                    subgroupsOptgroup.appendChild(subSubgroupOption);
                                    const subSubgroupTestIds = [];
                                    subSubgroup.tests.forEach(test => {
                                        const testId = test.master_test_id.toString();
                                        subSubgroupTestIds.push(testId);
                                        testNamesByID.set(testId, test.test_name);
                                        const option = document.createElement('option');
                                        option.value = testId;
                                        option.textContent = `    — ${test.test_name}`;
                                        option.dataset.parentGroup = subSubgroup.test_group_id;
                                        option.dataset.originalGroup = 'subsubgroup';
                                        subgroupsOptgroup.appendChild(option);
                                        addedTestIds.set(testId, 'subsubgroup');
                                    });
                                    subSubgroupOption.dataset.testIds = JSON.stringify(subSubgroupTestIds);
                                    subgroupTestIds.push(...subSubgroupTestIds);
                                });
                            }
                            subgroupOption.dataset.testIds = JSON.stringify([...new Set(subgroupTestIds)]);
                        });
                    }
                    if (subgroupsOptgroup.children.length > 0) {
                        selectElement.appendChild(subgroupsOptgroup);
                    }
                    if (Array.isArray(response.data.individual_tests) && response.data.individual_tests.length > 0) {
                        const individualOptgroup = document.createElement('optgroup');
                        individualOptgroup.label = 'Individual Tests';
                        response.data.individual_tests.forEach(test => {
                            const testId = test.master_test_id.toString();
                            testNamesByID.set(testId, test.test_name);
                            const option = document.createElement('option');
                            option.value = testId;
                            option.textContent = test.test_name;
                            option.dataset.originalGroup = 'individual';
                            individualOptgroup.appendChild(option);
                        });
                        if (individualOptgroup.children.length > 0) {
                            selectElement.appendChild(individualOptgroup);
                        }
                    }
                    if (window.jQuery && window.jQuery().select2) {
                        $('#select2Primary_tests').select2({
                            templateResult: formatOption,
                            templateSelection: formatSelection,
                            width: '100%',
                            dropdownCssClass: 'select2-dropdown-improved',
                        });
                        $('#select2Primary_tests').on('select2:select', function (e) {
                            const id = e.params.data.id;
                            if (id.startsWith('sg_') || id.startsWith('ssg_')) {
                                const groupOption = $(this).find(`option[value="${id}"]`)[0];
                                try {
                                    const testIds = JSON.parse(groupOption.dataset.testIds || '[]');
                                    testIds.forEach(testId => {
                                        selectedTestIds.add(testId);
                                    });
                                    updateSelectedTests();
                                    $(this).find(`option[value="${id}"]`).prop('selected', false);
                                    $(this).trigger('change');
                                } catch (error) {
                                    console.error('Error parsing test IDs:', error);
                                }
                            }
                            else {
                                selectedTestIds.add(id);
                                updateSelectedTests();
                            }
                        });
                        $('#select2Primary_tests').on('select2:unselect', function (e) {
                            const id = e.params.data.id;
                            if (!id.startsWith('sg_') && !id.startsWith('ssg_')) {
                                selectedTestIds.delete(id);
                                updateSelectedTests();
                            }
                        });
                        function updateSelectedTests() {
                            $(selectElement).find('option').prop('selected', false);
                            Array.from(selectedTestIds).forEach(testId => {
                                const option = $(selectElement).find(`option[value="${testId}"]`).first();
                                if (option.length) {
                                    option.prop('selected', true);
                                }
                            });
                            $(selectElement).trigger('change');
                        }
                    }
                } else {
                    console.warn('Unexpected response structure:', response);
                }
            },
            onError: function (error) {
            }
        });
        function formatOption(option) {
            if (!option.id) {
                return option.text;
            }
            if (option.id.startsWith('sg_') || option.id.startsWith('ssg_')) {
                return $('<span class="select2-results__option--group-header">' + option.text + ' [Select All]</span>');
            }
            const $option = $(option.element);
            if ($option.data('originalGroup') === 'subsubgroup') {
                return $('<span style="padding-left: 36px;">' + option.text + '</span>');
            } else if ($option.data('originalGroup') === 'subgroup') {
                return $('<span style="padding-left: 12px;">' + option.text + '</span>');
            }
            return $('<span>' + option.text + '</span>');
        }
        function formatSelection(option) {
            if (!option.id) {
                return option.text;
            }
            if (option.id.startsWith('sg_') || option.id.startsWith('ssg_')) {
                return '';
            }
            return testNamesByID.get(option.id) || option.text.replace(/^[\s—]+/, '');
        }
        if (employeeIds === null || !/^[a-zA-Z0-9]+$/.test(employeeIds)) {
            showToast("error", "Invalid Employee ID");
            return;
        }
        let apiUrl = 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllMasterTests/' + employeeIds;
        if (mode === 'op') {
            apiUrl += '/op/' + opId;
        } else if (mode === 'prescription') {
            apiUrl += '/prescription/' + prescriptionId;
        }
        apiRequest({
            url: apiUrl,
            method: 'GET',
            onSuccess: function (response) {
                if (response.result && Array.isArray(response.data)) {
                    const preselectedTestIds = response.data.map(String);
                    selectedTestIds = new Set(preselectedTestIds);
                    const interval = setInterval(() => {
                        if ($(selectElement).find('option').length > 0) {
                            $(selectElement).find('option').prop('selected', false);
                            Array.from(selectedTestIds).forEach(testId => {
                                if (testId.startsWith('sg_') || testId.startsWith('ssg_')) {
                                    return;
                                }
                                const option = $(selectElement).find(`option[value="${testId}"]`).first();
                                if (option.length) {
                                    option.prop('selected', true);
                                }
                            });
                            $(selectElement).trigger('change');
                            clearInterval(interval);
                        }
                    }, 100);
                } else if (response.message === "Employee ID does not exist") {
                    showToast("warning", "Employee ID not found.");
                } else if (response.message === "No tests found for this employee") {
                    showToast("info", "No existing tests assigned to this employee.");
                } else {
                    console.warn('Unexpected response structure:', response);
                }
            },
            onError: function (error) {
            }
        });
        addButton.addEventListener('click', function () {
            const testIds = Array.from(selectedTestIds).filter(id =>
                !id.startsWith('sg_') && !id.startsWith('ssg_')
            );
            if (testIds.length === 0) {
                showToast("info", "Please select at least one test.");
                return;
            }
            const dateTimeInput = document.getElementById('html5-datetime-local-input');
            const selectedDateTime = dateTimeInput.value;
            let apiUrl;
            if (mode === 'prescription') {
                apiUrl = `/ohc/health-registry/add-test/${employeeIds}/prescription/${prescriptionId}`;
            } else {
                apiUrl = `/ohc/health-registry/add-test/${employeeIds}/op/${opId}`;
            }
            Swal.fire({
                title: 'Confirm Add Tests?',
                text: "Do you want to add the selected test(s)?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Yes, add tests',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    apiRequest({
                        url: apiUrl,
                        method: 'POST',
                        data: {
                            test_ids: testIds,
                            selected_datetime: selectedDateTime
                        },
                        onSuccess: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Tests added successfully!',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(() => {
                                if (mode === 'prescription') {
                                    window.location.reload();
                                } else if (isOpRegistryIdIsthere == null) {
                                    window.location.reload();
                                } else {
                                    window.location.href = '/ohc/health-registry/edit-registry/edit-outpatient/' + employeeIds.toString().toLowerCase() + '/op/' + isOpRegistryIdIsthere;
                                }
                            }, 1500);
                        },
                        onError: function (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: 'Failed to add tests: ' + error
                            });
                        }
                    });
                }
            });
        });
        const isOutPatientAddedAndOpen = document.getElementById('isOutPatientAddedAndOpen').value;
        if (isOutPatientAddedAndOpen == 0) {
            $('#select2Primary_tests').prop('disabled', true);
            $('#addTest').prop('disabled', true);
        }
        if (backToHealthReg) {
            backToHealthReg.addEventListener('click', function () {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Any unsaved changes will be lost!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, go back',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/ohc/health-registry/edit-registry/edit-outpatient/' + employeeIds.toString().toLowerCase() + '/op/' + isOpRegistryIdIsthere;
                    }
                });
            });
        }
    });
</script>
@endsection