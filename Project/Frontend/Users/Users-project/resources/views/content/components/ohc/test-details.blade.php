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
<link rel="stylesheet" href="/lib/css/page-styles/test-details.css">
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
<script src="/lib/js/page-scripts/test-details.js"></script>
@endsection