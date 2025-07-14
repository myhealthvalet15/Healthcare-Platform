@extends('layouts/layoutMaster')
@section('title', 'Test Details')
{{-- VENDOR STYLES --}}
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/typeahead-js/typeahead.scss',
'resources/assets/vendor/libs/spinkit/spinkit.scss',
'resources/assets/vendor/libs/animate-css/animate.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
])
@endsection
{{-- VENDOR SCRIPTS --}}
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/typeahead-js/typeahead.js',
'resources/assets/vendor/libs/bloodhound/bloodhound.js',
])
@endsection
{{-- PAGE SCRIPTS --}}
@section('page-script')
@vite([
'resources/assets/js/forms-selects.js',
'resources/assets/js/extended-ui-sweetalert2.js',
'resources/assets/js/forms-typeahead.js',
])
@endsection
{{-- MAIN CONTENT --}}
@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
    body {
        margin: 0;
        background: #f4f4f4;
    }

    .boxed-container {
        max-width: 1200px;
        margin: 20px auto;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .group-header {
        background-color: rgb(115, 103, 240, 0.7);
        color: #fff;
        font-weight: bold;
        padding: 10px 15px;
        justify-content: space-between;
        align-items: center;
    }

    .group-test {
        color: #333;
        padding: 10px 20px;
        justify-content: space-between;
        align-items: center;
    }

    .subgroup-header {
        background-color: rgba(63, 204, 175, 0.7);
        color: #333;
        padding: 10px 20px;
        font-weight: bold;
        justify-content: space-between;
        align-items: center;
    }

    .subgroup-test {
        color: #333;
        padding: 10px 20px;
        justify-content: space-between;
        align-items: center;
    }

    .subsubgroup-header {
        background-color: rgba(205, 204, 211, 0.7);
        color: #333;
        padding: 10px 25px;
        justify-content: space-between;
        align-items: center;
    }

    .subsubgroup-test {
        color: #333;
        padding: 10px 25px;
        justify-content: space-between;
        align-items: center;
    }

    .prescription-id {
        font-weight: bold;
        color: #fcd34d;
    }

    .grey-bg {
        background: #d4d4d4;
        padding: 10px 15px;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .icons {
        display: flex;
        gap: 10px;
    }

    .icons i {
        cursor: pointer;
        color: #333;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: #f3e8ff;
        color: #333;
    }

    th,
    td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
        vertical-align: top;
    }

    td:first-child,
    th:first-child {
        text-align: left;
    }

    .drug-name i {
        margin-left: 5px;
        color: #555;
    }
</style>
<script>
    const testDetailsData = @json($testDetails);
</script>
<div class="boxed-container">
    <div class="patient-info mb-4 p-4 rounded shadow-sm bg-white">
        <h4 class="mb-3">Test Details</h4>
        <div class="row gy-2">
            <div class="col-md-3">
                <p class="mb-1"><strong>Patient:</strong> {{
                    $testDetails['name'] }}</p>
                <p class="mb-1"><strong>Employee ID:</strong> {{
                    $testDetails['employee_id'] }}</p>
            </div>
            <div class="col-md-3">
                <p class="mb-1"><strong>Age:</strong> {{ $testDetails['age']
                    }}</p>
                <p class="mb-1"><strong>Department:</strong> {{
                    $testDetails['department'] }}</p>
            </div>
            <div class="col-md-3">
                <p class="mb-1"><strong>Test Date:</strong> {{ date('d-m-Y',
                    strtotime($testDetails['test_date'])) }}
                </p>
                <p class="mb-1"><strong>Status:</strong> {{
                    $testDetails['healthplan_status'] }}</p>
            </div>
            <div class="col-md-3">
                <p class="mb-1"><strong>Gender:</strong>
                    <span id="patient-gender">
                        {{ isset($testDetails['gender']) ?
                        $testDetails['gender'] : 'Not specified' }}
                    </span>
                </p>
            </div>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th align="left">Test Name</th>
                <th align="left">Results</th>
                <th align="left">Unit</th>
                <th align="left">Ranges</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($testDetails['tests']) && !empty($testDetails['tests']))
            @foreach($testDetails['tests'] as $groupName => $groupData)
            <tr class="group-header">
                <td colspan="4">{{ $groupName }}</td>
            </tr>
            @foreach($groupData as $subgroupName => $subgroupData)
            @if(is_array($subgroupData))
            <tr>
                <td colspan="4" class="subgroup-header">{{ $subgroupName }}</td>
            </tr>
            @foreach($subgroupData as $key => $value)
            @if(is_array($value) && !isset($value['name']))
            <tr>
                <td colspan="4" class="subsubgroup-header">{{ $key }}</td>
            </tr>
            @foreach($value as $test)
            @php
            $testName = is_array($test) ? $test['name'] : $test;
            $unit = is_array($test) ? ($test['unit'] ?? '') : '';
            $testResult = is_array($test) && isset($test['test_result']) ? $test['test_result'] : '';
            $maleRanges = '';
            if (is_array($test) && isset($test['m_min_max'])) {
            try {
            if (is_string($test['m_min_max'])) {
            $mRangeData = json_decode($test['m_min_max'], true);
            if (is_array($mRangeData)) {
            if (isset($mRangeData['min']) && isset($mRangeData['max'])) {
            if (is_array($mRangeData['min']) && is_array($mRangeData['max']) &&
            count($mRangeData['min']) === count($mRangeData['max'])) {
            $rangeTexts = [];
            for ($i = 0; $i < count($mRangeData['min']); $i++) { if (isset($mRangeData['min'][$i]) &&
                isset($mRangeData['max'][$i])) { $rangeTexts[]=$mRangeData['min'][$i] . ' - ' . $mRangeData['max'][$i];
                } } $maleRanges=implode(', ', $rangeTexts);
                                } else {
                                    $maleRanges = $mRangeData[' min'] . ' - ' . $mRangeData['max']; } } else {
                $maleRanges=is_string($test['m_min_max']) ? strip_tags(json_encode($mRangeData)) :
                strip_tags(var_export($test['m_min_max'], true)); } } else { $maleRanges=$test['m_min_max']; } } else {
                $maleRanges=is_string($test['m_min_max']) ? $test['m_min_max'] :
                strip_tags(var_export($test['m_min_max'], true)); } } catch (Exception $e) {
                $maleRanges=is_string($test['m_min_max']) ? $test['m_min_max'] :
                strip_tags(var_export($test['m_min_max'], true)); } } $femaleRanges='' ; if (is_array($test) &&
                isset($test['f_min_max'])) { try { if (is_string($test['f_min_max'])) {
                $fRangeData=json_decode($test['f_min_max'], true); if (is_array($fRangeData)) { if
                (isset($fRangeData['min']) && isset($fRangeData['max'])) { if (is_array($fRangeData['min']) &&
                is_array($fRangeData['max']) && count($fRangeData['min'])===count($fRangeData['max'])) { $rangeTexts=[];
                for ($i=0; $i < count($fRangeData['min']); $i++) { if (isset($fRangeData['min'][$i]) &&
                isset($fRangeData['max'][$i])) { $rangeTexts[]=$fRangeData['min'][$i] . ' - ' . $fRangeData['max'][$i];
                } } $femaleRanges=implode(', ', $rangeTexts);
                                } else {
                                    $femaleRanges = $fRangeData[' min'] . ' - ' . $fRangeData['max']; } } else {
                $femaleRanges=is_string($test['f_min_max']) ? strip_tags(json_encode($fRangeData)) :
                strip_tags(var_export($test['f_min_max'], true)); } } else { $femaleRanges=$test['f_min_max']; } } else
                { $femaleRanges=is_string($test['f_min_max']) ? $test['f_min_max'] :
                strip_tags(var_export($test['f_min_max'], true)); } } catch (Exception $e) {
                $femaleRanges=is_string($test['f_min_max']) ? $test['f_min_max'] :
                strip_tags(var_export($test['f_min_max'], true)); } } $displayRanges='' ; if (!empty($maleRanges) &&
                !empty($femaleRanges)) { if ($maleRanges==$femaleRanges) { $displayRanges=$maleRanges; } else {
                $displayRanges='M: ' . $maleRanges . ' / F: ' . $femaleRanges; } } elseif (!empty($maleRanges)) {
                $displayRanges=$maleRanges; } elseif (!empty($femaleRanges)) { $displayRanges=$femaleRanges; }
                $displayRanges=preg_replace('/[\{\}\"\\\\]/', '' , $displayRanges); $testId=str_replace([' ', ' (', ')'
                , '%' ], ['_', '' , '' , 'percent' ], $testName); @endphp <tr>
                <td class="drug-name subsubgroup-test">{{ $testName }}</td>
                <td><input type="text" class="form-control" value="{{ $testResult }}" id="test_{{ $testId }}"
                        placeholder="" aria-describedby="floatingInputHelp" /></td>
                <td>{{ $unit }}</td>
                <td>{{ $displayRanges }}</td>
                </tr>
                @endforeach
                @else
                @php
                $testName = is_array($value) ? $value['name'] : $value;
                $unit = is_array($value) ? ($value['unit'] ?? '') : '';
                $testResult = is_array($value) && isset($value['test_result']) ? $value['test_result'] : '';
                $maleRanges = '';
                if (is_array($value) && isset($value['m_min_max'])) {
                try {
                if (is_string($value['m_min_max'])) {
                $mRangeData = json_decode($value['m_min_max'], true);
                if (is_array($mRangeData)) {
                if (isset($mRangeData['min']) && isset($mRangeData['max'])) {
                if (is_array($mRangeData['min']) && is_array($mRangeData['max']) &&
                count($mRangeData['min']) === count($mRangeData['max'])) {
                $rangeTexts = [];
                for ($i = 0; $i < count($mRangeData['min']); $i++) { if (isset($mRangeData['min'][$i]) &&
                    isset($mRangeData['max'][$i])) { $rangeTexts[]=$mRangeData['min'][$i] . ' - ' .
                    $mRangeData['max'][$i]; } } $maleRanges=implode(', ', $rangeTexts);
                                } else {
                                    $maleRanges = $mRangeData[' min'] . ' - ' . $mRangeData['max']; } } else {
                    $maleRanges=is_string($value['m_min_max']) ? strip_tags(json_encode($mRangeData)) :
                    strip_tags(var_export($value['m_min_max'], true)); } } else { $maleRanges=$value['m_min_max']; } }
                    else { $maleRanges=is_string($value['m_min_max']) ? $value['m_min_max'] :
                    strip_tags(var_export($value['m_min_max'], true)); } } catch (Exception $e) {
                    $maleRanges=is_string($value['m_min_max']) ? $value['m_min_max'] :
                    strip_tags(var_export($value['m_min_max'], true)); } } $femaleRanges='' ; if (is_array($value) &&
                    isset($value['f_min_max'])) { try { if (is_string($value['f_min_max'])) {
                    $fRangeData=json_decode($value['f_min_max'], true); if (is_array($fRangeData)) { if
                    (isset($fRangeData['min']) && isset($fRangeData['max'])) { if (is_array($fRangeData['min']) &&
                    is_array($fRangeData['max']) && count($fRangeData['min'])===count($fRangeData['max'])) {
                    $rangeTexts=[]; for ($i=0; $i < count($fRangeData['min']); $i++) { if (isset($fRangeData['min'][$i])
                    && isset($fRangeData['max'][$i])) { $rangeTexts[]=$fRangeData['min'][$i] . ' - ' .
                    $fRangeData['max'][$i]; } } $femaleRanges=implode(', ', $rangeTexts);
                                } else {
                                    $femaleRanges = $fRangeData[' min'] . ' - ' . $fRangeData['max']; } } else {
                    $femaleRanges=is_string($value['f_min_max']) ? strip_tags(json_encode($fRangeData)) :
                    strip_tags(var_export($value['f_min_max'], true)); } } else { $femaleRanges=$value['f_min_max']; } }
                    else { $femaleRanges=is_string($value['f_min_max']) ? $value['f_min_max'] :
                    strip_tags(var_export($value['f_min_max'], true)); } } catch (Exception $e) {
                    $femaleRanges=is_string($value['f_min_max']) ? $value['f_min_max'] :
                    strip_tags(var_export($value['f_min_max'], true)); } } $displayRanges='' ; if (!empty($maleRanges)
                    && !empty($femaleRanges)) { if ($maleRanges==$femaleRanges) { $displayRanges=$maleRanges; } else {
                    $displayRanges='M: ' . $maleRanges . ' / F: ' . $femaleRanges; } } elseif (!empty($maleRanges)) {
                    $displayRanges=$maleRanges; } elseif (!empty($femaleRanges)) { $displayRanges=$femaleRanges; }
                    $displayRanges=preg_replace('/[\{\}\"\\\\]/', '' , $displayRanges); $testId=str_replace([' ', '
                    (', ')' , '%' ], ['_', '' , '' , 'percent' ], $testName); @endphp <tr>
                    <td class="drug-name subgroup-test">{{ $testName }}</td>
                    <td><input type="text" class="form-control" value="{{ $testResult }}" id="test_{{ $testId }}"
                            placeholder="" aria-describedby="floatingInputHelp" /></td>
                    <td>{{ $unit }}</td>
                    <td>{{ $displayRanges }}</td>
                    </tr>
                    @endif
                    @endforeach
                    @else
                    @php
                    $testName = is_array($subgroupData) ? $subgroupData['name'] : $subgroupData;
                    $unit = is_array($subgroupData) ? ($subgroupData['unit'] ?? '') : '';
                    $testResult = is_array($subgroupData) && isset($subgroupData['test_result']) ?
                    $subgroupData['test_result'] : '';
                    $maleRanges = '';
                    if (is_array($subgroupData) && isset($subgroupData['m_min_max'])) {
                    try {
                    if (is_string($subgroupData['m_min_max'])) {
                    $mRangeData = json_decode($subgroupData['m_min_max'], true);
                    if (is_array($mRangeData)) {
                    if (isset($mRangeData['min']) && isset($mRangeData['max'])) {
                    if (is_array($mRangeData['min']) && is_array($mRangeData['max']) &&
                    count($mRangeData['min']) === count($mRangeData['max'])) {
                    $rangeTexts = [];
                    for ($i = 0; $i < count($mRangeData['min']); $i++) { if (isset($mRangeData['min'][$i]) &&
                        isset($mRangeData['max'][$i])) { $rangeTexts[]=$mRangeData['min'][$i] . ' - ' .
                        $mRangeData['max'][$i]; } } $maleRanges=implode(', ', $rangeTexts);
                                } else {
                                    $maleRanges = $mRangeData[' min'] . ' - ' . $mRangeData['max']; } } else {
                        $maleRanges=is_string($subgroupData['m_min_max']) ? strip_tags(json_encode($mRangeData)) :
                        strip_tags(var_export($subgroupData['m_min_max'], true)); } } else {
                        $maleRanges=$subgroupData['m_min_max']; } } else {
                        $maleRanges=is_string($subgroupData['m_min_max']) ? $subgroupData['m_min_max'] :
                        strip_tags(var_export($subgroupData['m_min_max'], true)); } } catch (Exception $e) {
                        $maleRanges=is_string($subgroupData['m_min_max']) ? $subgroupData['m_min_max'] :
                        strip_tags(var_export($subgroupData['m_min_max'], true)); } } $femaleRanges='' ; if
                        (is_array($subgroupData) && isset($subgroupData['f_min_max'])) { try { if
                        (is_string($subgroupData['f_min_max'])) { $fRangeData=json_decode($subgroupData['f_min_max'],
                        true); if (is_array($fRangeData)) { if (isset($fRangeData['min']) && isset($fRangeData['max']))
                        { if (is_array($fRangeData['min']) && is_array($fRangeData['max']) &&
                        count($fRangeData['min'])===count($fRangeData['max'])) { $rangeTexts=[]; for ($i=0; $i <
                        count($fRangeData['min']); $i++) { if (isset($fRangeData['min'][$i]) &&
                        isset($fRangeData['max'][$i])) { $rangeTexts[]=$fRangeData['min'][$i] . ' - ' .
                        $fRangeData['max'][$i]; } } $femaleRanges=implode(', ', $rangeTexts);
                                } else {
                                    $femaleRanges = $fRangeData[' min'] . ' - ' . $fRangeData['max']; } } else {
                        $femaleRanges=is_string($subgroupData['f_min_max']) ? strip_tags(json_encode($fRangeData)) :
                        strip_tags(var_export($subgroupData['f_min_max'], true)); } } else {
                        $femaleRanges=$subgroupData['f_min_max']; } } else {
                        $femaleRanges=is_string($subgroupData['f_min_max']) ? $subgroupData['f_min_max'] :
                        strip_tags(var_export($subgroupData['f_min_max'], true)); } } catch (Exception $e) {
                        $femaleRanges=is_string($subgroupData['f_min_max']) ? $subgroupData['f_min_max'] :
                        strip_tags(var_export($subgroupData['f_min_max'], true)); } } $displayRanges='' ; if
                        (!empty($maleRanges) && !empty($femaleRanges)) { if ($maleRanges==$femaleRanges) {
                        $displayRanges=$maleRanges; } else { $displayRanges='M: ' . $maleRanges . ' / F: ' .
                        $femaleRanges; } } elseif (!empty($maleRanges)) { $displayRanges=$maleRanges; } elseif
                        (!empty($femaleRanges)) { $displayRanges=$femaleRanges; }
                        $displayRanges=preg_replace('/[\{\}\"\\\\]/', '' , $displayRanges); $testId=str_replace([' ', '
                        (', ')' , '%' ], ['_', '' , '' , 'percent' ], $testName); @endphp <tr>
                        <td class="drug-name group-test">{{ $testName }}</td>
                        <td><input type="text" class="form-control" value="{{ $testResult }}" id="test_{{ $testId }}"
                                placeholder="" aria-describedby="floatingInputHelp" /></td>
                        <td>{{ $unit }}</td>
                        <td>{{ $displayRanges }}</td>
                        </tr>
                        @endif
                        @endforeach
                        @endforeach
                        @else
                        <tr>
                            <td colspan="4" class="text-center">No test details available</td>
                        </tr>
                        @endif
        </tbody>
    </table>
    <div class="mt-4 text-end p-3">
        <button type="button" class="btn btn-primary" id="saveResultsBtn">Save Results</button>
    </div>
</div>
<script>
    class TestResultsManager {
        constructor() {
            this.testData = {};
            this.csrfToken = '';
            this.saveButtonId = 'saveResultsBtn';
            this.apiEndpoint = '/ohc/test-details/save-results';
            this.testCodeValue = '';
        }
        init(testData, testCode) {
            this.testData = testData || {};
            this.testCodeValue = testCode || '';
            this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            this.setupEventListeners();
            this.detectPatientGender();
        }
        setupEventListeners() {
            const saveButton = document.getElementById(this.saveButtonId);
            if (saveButton) {
                saveButton.addEventListener('click', () => this.handleSaveResults());
            }
        }
        detectPatientGender() {
            const genderElement = document.getElementById('patient-gender');
            if (genderElement) {
                const gender = genderElement.textContent.trim().toLowerCase();
                if (gender === 'male' || gender === 'female') {
                    console.log('Patient gender is:', gender);
                }
            }
        }
        findMasterTestIdByName(testName) {
            for (const groupName in this.testData) {
                const group = this.testData[groupName];
                for (const subgroupKey in group) {
                    const subgroup = group[subgroupKey];
                    if (typeof subgroup === 'object' && subgroup !== null && subgroup.name === testName) {
                        return subgroup.master_test_id;
                    }
                    if (typeof subgroup === 'object' && subgroup !== null && !Array.isArray(subgroup) && !subgroup.name) {
                        for (const testKey in subgroup) {
                            const test = subgroup[testKey];
                            if (typeof test === 'object' && test !== null && test.name === testName) {
                                return test.master_test_id;
                            }
                        }
                    }
                    if (Array.isArray(subgroup)) {
                        for (const test of subgroup) {
                            if (typeof test === 'object' && test !== null && test.name === testName) {
                                return test.master_test_id;
                            }
                        }
                    }
                    if (typeof subgroup === 'object' && subgroup !== null && !Array.isArray(subgroup) && !subgroup.name) {
                        for (const subSubGroupName in subgroup) {
                            const subSubGroup = subgroup[subSubGroupName];
                            if (Array.isArray(subSubGroup)) {
                                for (const test of subSubGroup) {
                                    if (typeof test === 'object' && test !== null && test.name === testName) {
                                        return test.master_test_id;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return null;
        }
        collectTestResults() {
            const testResults = [];
            const testInputs = document.querySelectorAll('table input.form-control');
            testInputs.forEach(input => {
                const testRow = input.closest('tr');
                if (!testRow) {
                    console.warn('Could not find parent row for input:', input);
                    return;
                }

                const testNameElement = testRow.querySelector('.drug-name');
                if (testNameElement) {
                    const testName = testNameElement.textContent.trim();
                    const masterTestId = this.findMasterTestIdByName(testName);

                    if (masterTestId) {
                        const testValue = input.value.trim();
                        testResults.push({
                            master_test_id: masterTestId,
                            test_result: testValue !== '' ? testValue : null,
                            test_code: this.testCodeValue
                        });
                    } else {
                        console.warn(`Could not find master_test_id for test: ${testName}`);
                    }
                }
            });
            return testResults;
        } handleSaveResults() {
            const testResults = this.collectTestResults();
            if (testResults.length === 0) {
                this.showAlert({
                    title: 'No Results',
                    text: 'Please enter at least one test result',
                    icon: 'warning'
                });
                return;
            }
            Swal.fire({
                title: 'Confirm Submission',
                text: 'Are you sure you want to save these test results?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, save results',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submitResults(testResults);
                }
            });
        }
        submitResults(testResults) {
            const employeeId = testDetailsData['employee_id'] || '';
            apiRequest({
                url: this.apiEndpoint,
                method: 'POST',
                data: {
                    test_results: testResults,
                    employee_id: employeeId
                },
                onSuccess: (data) => {
                    if (data.result) {
                        this.showAlert({
                            title: 'Success',
                            text: 'Test results saved successfully',
                            icon: 'success'
                        });
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        this.showAlert({
                            title: 'Error',
                            text: data.message || 'Failed to save test results',
                            icon: 'error'
                        });
                    }
                },
                onError: (error) => {
                    console.error('Error:', error);
                    this.showAlert({
                        title: 'Error',
                        text: 'An unexpected error occurred',
                        icon: 'error'
                    });
                }
            });
        }
        showAlert(options) {
            if (typeof Swal !== 'undefined') {
                Swal.fire(options);
            } else {
                console.warn('SweetAlert not loaded. Message:', options.text);
                alert(options.text);
            }
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof testDetailsData === 'undefined') {
            console.error('Test data not found. Make sure testDetailsData is defined before this script runs.');
            return;
        }
        const manager = new TestResultsManager();
        const testCode = testDetailsData.test_code || '';
        manager.init(testDetailsData.tests, testCode);
    });
</script>
@endsection