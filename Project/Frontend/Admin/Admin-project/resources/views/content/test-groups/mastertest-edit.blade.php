@extends('layouts/layoutMaster')
@section('title', 'Tests - Test Group')
<!-- Vendor Styles -->
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection
<!-- Vendor Scripts -->
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/cleavejs/cleave.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js'
])
@endsection
<!-- Page Scripts -->
@section('page-script')
@vite(['resources/assets/js/form-layouts.js'])
@endsection
@section('content')
<div class="card mb-6">
    <h5 class="card-header">Edit Test: {{ $testData['test_name'] }}</h5>
    <form class="card-body needs-validation" novalidate id="master-test">
        <div class="row g-6">
            <!-- Test ID (Hidden) -->
            <input type="hidden" id="edit_master_test_id" value="{{ $testData['master_test_id'] }}">
            <input type="hidden" id="edit_testgroup_id" value="{{ $testData['testgroup_id'] }}">
            <input type="hidden" id="edit_subgroup_id" value="{{ $testData['subgroup_id'] }}">
            <input type="hidden" id="edit_subsubgroup_id" value="{{ $testData['subsubgroup_id'] }}">
            <input type="hidden" id="edit_numeric_type" value="{{ $testData['numeric_type'] }}">
            @if ($testData['numeric_type'] === 'no-age-range')
            @php
            $mMinMax = json_decode($testData['m_min_max'], true);
            $fMinMax = json_decode($testData['f_min_max'], true);
            @endphp
            <input type="hidden" id="edit_no_age_range_male_min" value="{{ $mMinMax['min'] ?? '' }}">
            <input type="hidden" id="edit_no_age_range_male_max" value="{{ $mMinMax['max'] ?? '' }}">
            <input type="hidden" id="edit_no_age_range_female_min" value="{{ $fMinMax['min'] ?? '' }}">
            <input type="hidden" id="edit_no_age_range_female_max" value="{{ $fMinMax['max'] ?? '' }}">
            @elseif ($testData['numeric_type'] === 'multiple-age-range')
            @php
            $mMinMax = json_decode($testData['m_min_max'], true);
            $fMinMax = json_decode($testData['f_min_max'], true);
            $ageRanges = json_decode($testData['age_range'], true);
            @endphp
            @foreach ($ageRanges as $index => $range)
            <input type="hidden" class="edit_multiple_age_range-ageRange"
                id="edit_multiple_age_range-age_range_{{ $index }}" value="{{ $range }}">
            <input type="hidden" class="edit_multiple_age_range-male_min"
                id="edit_multiple_age_range-male_min_{{ $index }}" value="{{ $mMinMax['min'][$index] ?? '' }}">
            <input type="hidden" class="edit_multiple_age_range-male_max"
                id="edit_multiple_age_range-male_max_{{ $index }}" value="{{ $mMinMax['max'][$index] ?? '' }}">
            <input type="hidden" class="edit_multiple_age_range-female_min"
                id="edit_multiple_age_range-female_min_{{ $index }}" value="{{ $fMinMax['min'][$index] ?? '' }}">
            <input type="hidden" class="edit_multiple_age_range-female_max"
                id="edit_multiple_age_range-female_max_{{ $index }}" value="{{ $fMinMax['max'][$index] ?? '' }}">
            @endforeach
            @elseif ($testData['numeric_type'] === 'multiple-text-value')
            @php
            $mMinMax = json_decode($testData['m_min_max'], true);
            $fMinMax = json_decode($testData['f_min_max'], true);
            $descriptions = json_decode($testData['multiple_text_value_description'], true);
            @endphp
            @foreach ($descriptions as $index => $desc)
            <input type="hidden" class="edit_multiple-text-value_text_value_description"
                id="edit_multiple-text-value_text_value_description_{{ $index }}" value="{{ $desc }}">
            <input type="hidden" class="edit_multiple-text-value_male_min"
                id="edit_multiple-text-value_male_min_{{ $index }}" value="{{ $mMinMax['min'][$index] ?? '' }}">
            <input type="hidden" class="edit_multiple-text-value_male_max"
                id="edit_multiple-text-value_male_max_{{ $index }}" value="{{ $mMinMax['max'][$index] ?? '' }}">
            <input type="hidden" class="edit_multiple-text-value_female_min"
                id="edit_multiple-text-value_female_min_{{ $index }}" value="{{ $fMinMax['min'][$index] ?? '' }}">
            <input type="hidden" class="edit_multiple-text-value_female_max"
                id="edit_multiple-text-value_female_max_{{ $index }}" value="{{ $fMinMax['max'][$index] ?? '' }}">
            @endforeach
            @elseif ($testData['numeric_type'] === 'just-values')
            <input type="hidden" id="edit_just_values" value="{{ $testData['normal_values'] ?? '' }}">
            @endif
            <!-- Test Name -->
            <div class="col-md-3 mb-3">
                <label class="form-label" for="multicol-test-name">Test Name</label>
                <input type="text" id="test-name" class="form-control" value="{{ $testData['test_name'] }}"
                    placeholder="Test Name" required />
                <div class="invalid-feedback"> Enter the test name.</div>
            </div>
            <!-- Group -->
            <div class="col-md-3 mb-3">
                <label for="select2Basic-group" class="form-label">Group</label>
                <select id="select2Basic-group" class="select2 form-select form-select-lg" required>
                    <option value="">Select Group</option>
                </select>
                <div class="invalid-feedback"> Select a group. </div>
            </div>
            <!-- Sub Group -->
            <div class="col-md-3 mb-3">
                <label for="select2Basic-sub-group" class="form-label">Sub Group</label>
                <select id="select2Basic-sub-group" class="select2 form-select form-select-lg">
                    <option value="">Select Sub Group</option>
                </select>
                <div class="invalid-feedback"> Select a sub-group. </div>
            </div>
            <!-- Sub Sub Group -->
            <div class="col-md-3 mb-3">
                <label for="select2Basic-sub-sub-group" class="form-label">Sub Sub Group</label>
                <select id="select2Basic-sub-sub-group" class="select2 form-select form-select-lg">
                    <option value="">Select Sub Sub Group</option>
                </select>
                <div class="invalid-feedback"> Select a sub-sub group.</div>
            </div>
            <!-- Description -->
            <div class="col-md-6 mb-3">
                <label class="form-label" for="basic-default-description">Description</label>
                <textarea id="basic-default-description" class="form-control"
                    placeholder="Write the description here">{{ $testData['test_desc'] }}</textarea>
                <div class="invalid-feedback"> Provide a description.</div>
            </div>
            <!-- Remarks -->
            <div class="col-md-6 mb-3">
                <label class="form-label" for="basic-default-remarks">Remarks</label>
                <textarea id="basic-default-remarks" class="form-control"
                    placeholder="Write the remarks here">{{ $testData['remarks'] }}</textarea>
                <div class="invalid-feedback"> Provide remarks. </div>
            </div>
            <!-- Type Selection -->
            <div class="col-md-3 mb-3">
                <label for="select2Basic-type" class="form-label">Type</label>
                <select id="select2Basic-type" class="select2 form-select form-select-lg" required>
                    <option value="text" {{ $testData['type']==='text' ? 'selected' : '' }}>Text</option>
                    <option value="numeric" {{ $testData['type']==='numeric' ? 'selected' : '' }}>Numeric</option>
                </select>
                <div class="invalid-feedback"> Select a type. </div>
            </div>
            <!-- Unit -->
            <div class="col-md-3 mb-3">
                <label class="form-label" for="multicol-unit">Unit</label>
                <input type="text" id="multicol-unit" class="form-control" placeholder="Unit"
                    value="{{ $testData['unit'] }}" />
                <div class="invalid-feedback"> Enter a unit. </div>
            </div>
            <!-- Dynamic Text Inputs for Conditions -->
            <div class="col-md-6 mb-3" id="text-input-container">
                <label class="form-label">Add Conditions</label>
                <div id="dynamic-text-fields">
                    @if (!empty($testData['condition']))
                    @foreach (json_decode($testData['condition'], true) as $condition)
                    <div class="d-flex mb-2 align-items-center">
                        <input type="text" class="form-control me-2 condition-input" placeholder="Enter condition"
                            name="basic-default-text-condition[]" value="{{ $condition }}" />
                        <button type="button" class="btn btn-sm btn-danger remove-row">-</button>
                    </div>
                    @endforeach
                    @else
                    <div class="d-flex mb-2 align-items-center">
                        <input type="text" class="form-control me-2 condition-input" placeholder="Enter condition"
                            name="basic-default-text-condition[]" />
                        <button type="button" class="btn btn-sm btn-success add-row">+</button>
                    </div>
                    @endif
                </div>
            </div>
            <!-- Numeric Dropdown (Initially Hidden) -->
            <div class="col-md-6 mb-3" id="numeric-dropdown-container" style="display: none;">
                <label class="form-label">Numeric</label>
                <select id="numeric-dropdown" class="select2 form-select form-select-lg" required>
                    <option value="no-age-range" {{ $testData['numeric_type']==='no-age-range' ? 'selected' : '' }}>No
                        age range</option>
                    <option value="multiple-age-range" {{ $testData['numeric_type']==='multiple-age-range' ? 'selected'
                        : '' }}>Multiple age range</option>
                    <option value="multiple-text-value" {{ $testData['numeric_type']==='multiple-text-value'
                        ? 'selected' : '' }}>Multiple text value</option>
                    <option value="just-values" {{ $testData['numeric_type']==='just-values' ? 'selected' : '' }}>Just
                        values</option>
                </select>
                <div class="invalid-feedback"> Select a numeric type</div>
            </div>
            <!-- Submit Button -->
            <div class="pt-6">
                <button type="submit" id="submitButton" class="btn btn-primary me-4"><i
                        class="fa-solid fa-pencil"></i>&nbsp;Update</button>
            </div>
        </div>
    </form>
</div>
<script src="/lib/js/page-scripts/mastertest-edit.js"></script>
@endsection