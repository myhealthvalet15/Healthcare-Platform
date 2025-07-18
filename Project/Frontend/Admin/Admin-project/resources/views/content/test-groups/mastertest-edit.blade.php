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
<script>
    let fields = [
        "select2Basic-group",
        // "select2Basic-sub-group",
        // "select2Basic-sub-sub-group",
    ]
    async function addValidation() {
        var form = document.getElementById("master-test");
        form.addEventListener("submit", async function(event) {
            event.preventDefault();
            event.stopPropagation();
            var type = $('#select2Basic-type').val();
            var isValid = true;
            fields.forEach(function(fieldId) {
                var input = document.getElementById(fieldId);
                if (input && input.dataset.optional !== "true") {
                    if (!input.value.trim()) {
                        input.classList.add("is-invalid");
                        isValid = false;
                    } else {
                        input.classList.remove("is-invalid");
                        input.classList.add("is-valid");
                    }
                }
            });
            var excludedFields = [
                "basic-default-description",
                "basic-default-remarks",
                "basic-default-text-condition",
                "multicol-unit"
            ];
            excludedFields.forEach(function(fieldId) {
                var input = document.getElementById(fieldId);
                if (input) {
                    input.classList.remove("is-invalid", "is-valid");
                }
            });
            if (!isValid) {
                event.preventDefault();
                event.stopPropagation();
            } else {
                let payload = {};
                if (type === "text") {
                    let conditions = [];
                    $('.condition-input').each(function() {
                        conditions.push($(this).val());
                    });
                    payload = {
                        group_id: $('#select2Basic-group').val(),
                        sub_group_id: $('#select2Basic-sub-group').val(),
                        sub_sub_group_id: $('#select2Basic-sub-sub-group').val(),
                        test_name: $('#test-name').val(),
                        description: $('#basic-default-description').val(),
                        remarks: $('#basic-default-remarks').val(),
                        type: type,
                        unit: $('#multicol-unit').val(),
                        condition: conditions,
                    };
                } else if (type === "numeric") {
                    if ($("#numeric-dropdown").val() === "no-age-range") {
                        payload = {
                            group_id: $('#select2Basic-group').val(),
                            sub_group_id: $('#select2Basic-sub-group').val(),
                            sub_sub_group_id: $('#select2Basic-sub-sub-group').val(),
                            test_name: $('#test-name').val(),
                            description: $('#basic-default-description').val(),
                            remarks: $('#basic-default-remarks').val(),
                            type: type,
                            unit: $('#multicol-unit').val(),
                            numeric_type: $('#numeric-dropdown').val(),
                            no_age_range_male_min: $('#no-age-range-male-min').val(),
                            no_age_range_male_max: $('#no-age-range-male-max').val(),
                            no_age_range_female_min: $('#no-age-range-female-min').val(),
                            no_age_range_female_max: $('#no-age-range-female-max').val(),
                        }
                    } else if ($("#numeric-dropdown").val() === "multiple-age-range") {
                        let ageFrom = [];
                        let ageTo = [];
                        let multiple_age_range_min_male = [];
                        let multiple_age_range_max_male = [];
                        let multiple_age_range_min_female = [];
                        let multiple_age_range_max_female = [];
                        $('.select2Basic-ageFrom').each(function() {
                            ageFrom.push($(this).val());
                        });
                        $('.select2Basic-ageTo').each(function() {
                            ageTo.push($(this).val());
                        });
                        $('.multiple-age-range-min-male').each(function() {
                            multiple_age_range_min_male.push($(this).val());
                        });
                        $('.multiple-age-range-max-male').each(function() {
                            multiple_age_range_max_male.push($(this).val());
                        });
                        $('.multiple-age-range-min-female').each(function() {
                            multiple_age_range_min_female.push($(this).val());
                        });
                        $('.multiple-age-range-max-female').each(function() {
                            multiple_age_range_max_female.push($(this).val());
                        });

                        function hasInvalidValue(arr) {
                            return arr.some(val => val === null || val === '' || isNaN(val) || !Number.isInteger(Number(val)));
                        }
                        if (
                            ageFrom.length !== ageTo.length ||
                            ageFrom.length !== multiple_age_range_min_male.length ||
                            ageFrom.length !== multiple_age_range_max_male.length ||
                            ageFrom.length !== multiple_age_range_min_female.length ||
                            ageFrom.length !== multiple_age_range_max_female.length ||
                            hasInvalidValue(ageFrom) ||
                            hasInvalidValue(ageTo) ||
                            hasInvalidValue(multiple_age_range_min_male) ||
                            hasInvalidValue(multiple_age_range_max_male) ||
                            hasInvalidValue(multiple_age_range_min_female) ||
                            hasInvalidValue(multiple_age_range_max_female)
                        ) {
                            showToast('error', 'Error', 'Fill all the rows properly with valid numbers');
                            return;
                        }
                        payload = {
                            group_id: $('#select2Basic-group').val(),
                            sub_group_id: $('#select2Basic-sub-group').val(),
                            sub_sub_group_id: $('#select2Basic-sub-sub-group').val(),
                            test_name: $('#test-name').val(),
                            description: $('#basic-default-description').val(),
                            remarks: $('#basic-default-remarks').val(),
                            type: type,
                            unit: $('#multicol-unit').val(),
                            numeric_type: $('#numeric-dropdown').val(),
                            ageFrom: ageFrom,
                            ageTo: ageTo,
                            multiple_age_range_min_male: multiple_age_range_min_male,
                            multiple_age_range_max_male: multiple_age_range_max_male,
                            multiple_age_range_min_female: multiple_age_range_min_female,
                            multiple_age_range_max_female: multiple_age_range_max_female,
                        };
                    } else if ($("#numeric-dropdown").val() === "multiple-text-value") {
                        let textValueDescription = [];
                        let multipleTextValueMinMale = [];
                        let multipleTextValueMaxMale = [];
                        let multipleTextValueMinFemale = [];
                        let multipleTextValueMaxFemale = [];
                        $('.multiple-text-value-description').each(function() {
                            textValueDescription.push($(this).val());
                        });
                        $('.multiple-text-value-min-male').each(function() {
                            multipleTextValueMinMale.push($(this).val());
                        });
                        $('.multiple-text-value-max-male').each(function() {
                            multipleTextValueMaxMale.push($(this).val());
                        });
                        $('.multiple-text-value-min-female').each(function() {
                            multipleTextValueMinFemale.push($(this).val());
                        });
                        $('.multiple-text-value-max-female').each(function() {
                            multipleTextValueMaxFemale.push($(this).val());
                        });

                        function hasInvalidInteger(arr) {
                            return arr.some(val => val === null || val === '' || isNaN(val) || !Number.isInteger(Number(val)));
                        }
                        if (
                            textValueDescription.length !== multipleTextValueMinMale.length ||
                            textValueDescription.length !== multipleTextValueMaxMale.length ||
                            textValueDescription.length !== multipleTextValueMinFemale.length ||
                            textValueDescription.length !== multipleTextValueMaxFemale.length ||
                            hasInvalidInteger(multipleTextValueMinMale) ||
                            hasInvalidInteger(multipleTextValueMaxMale) ||
                            hasInvalidInteger(multipleTextValueMinFemale) ||
                            hasInvalidInteger(multipleTextValueMaxFemale)
                        ) {
                            showToast('error', 'Error', 'Invalid range, fill all the rows properly with valid values');
                            return;
                        }
                        payload = {
                            group_id: $('#select2Basic-group').val(),
                            sub_group_id: $('#select2Basic-sub-group').val(),
                            sub_sub_group_id: $('#select2Basic-sub-sub-group').val(),
                            test_name: $('#test-name').val(),
                            description: $('#basic-default-description').val(),
                            remarks: $('#basic-default-remarks').val(),
                            type: type,
                            unit: $('#multicol-unit').val(),
                            numeric_type: $('#numeric-dropdown').val(),
                            text_value_description: textValueDescription,
                            multiple_text_value_min_male: multipleTextValueMinMale,
                            multiple_text_value_max_male: multipleTextValueMaxMale,
                            multiple_text_value_min_female: multipleTextValueMinFemale,
                            multiple_text_value_max_female: multipleTextValueMaxFemale,
                        };
                    } else if ($("#numeric-dropdown").val() === "just-values") {
                        payload = {
                            group_id: $('#select2Basic-group').val(),
                            sub_group_id: $('#select2Basic-sub-group').val(),
                            sub_sub_group_id: $('#select2Basic-sub-sub-group').val(),
                            test_name: $('#test-name').val(),
                            description: $('#basic-default-description').val(),
                            remarks: $('#basic-default-remarks').val(),
                            type: type,
                            unit: $('#multicol-unit').val(),
                            numeric_type: $('#numeric-dropdown').val(),
                            just_values: $('#just-values-textarea').val(),
                        }
                    } else {
                        showToast('error', 'Error', 'Invalid numeric type');
                    }
                } else {
                    showToast('error', 'Error', 'Invalid type');
                }
                try {
                    const element = document.getElementById("edit_master_test_id");
                    const testId = element ? Number(element.value) : null;
                    if (!Number.isInteger(testId)) {
                        showToast('error', "Invalid Request");
                        return;
                    }
                    var url = 'https://mhv-admin.hygeiaes.com/test-group/edit-tests/' + testId;
                    const data = await apiRequest({
                        url: url,
                        method: 'POST',
                        data: payload
                    });
                    if (data.result) {
                        showToast('success', 'Success', data.message || 'Group added successfully');
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showToast('error', 'Error', data.message || 'Failed to add group: ' + data.message);
                    }
                } catch (error) {
                    showToast('error', 'Error', 'An error occurred while adding the group');
                }
            }
            event.preventDefault();
            event.stopPropagation();
            form.classList.add("was-validated");
        });
    }

    function uiCodes() {
        let maxFields = 5;
        let fieldCount = 1;
        let multipleAgeRangeMaxFields = 5;
        let multipleAgeRangeFieldCount = 1;
        let multipleTextValueFieldCount = 1;
        $(document).on('click', '.add-row', function() {
            if (fieldCount < maxFields) {
                fieldCount++;
                let newRow = `
                    <div class="d-flex mb-2 align-items-center">
                        <input type="text" class="form-control me-2 condition-input" 
                            placeholder="Enter condition" name="basic-default-text-condition[]">
                        <button type="button" class="btn btn-sm btn-danger remove-row">🗑</button>
                    </div>
                `;
                $('#dynamic-text-fields').append(newRow);
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Limit Reached!',
                    text: `You can only add up to ${maxFields} text inputs.`,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            }
        });
        $(document).on('click', '.remove-row', function() {
            $(this).parent().remove();
            fieldCount--;
        });
        $('.select2').select2({
            placeholder: "Select an option",
            allowClear: true
        });
        $('#select2Basic-type').on('change', function() {
            const selectedType = $(this).val();
            if (selectedType === 'text') {
                isValid = false;
                $('#text-input-container').show();
                $('#numeric-dropdown-container').hide();
                $('#numeric-dropdown').val('').trigger('change');
                fields = [
                    "select2Basic-group",
                    // "select2Basic-sub-group",
                    // "select2Basic-sub-sub-group",
                ]
            } else if (selectedType === 'numeric') {
                isValid = false;
                $('#text-input-container').hide();
                $('#numeric-dropdown-container').show();
                fields = [
                    "select2Basic-group",
                    // "select2Basic-sub-group",
                    // "select2Basic-sub-sub-group",
                    "numeric-dropdown"
                ]
            }
        });
        $('#select2Basic-type').trigger('change');

        function findEqualLengths(textValueDescription, minMale, maxMale, minFemale, maxFemale) {
            const lengths = [
                textValueDescription.length,
                minMale.length,
                maxMale.length,
                minFemale.length,
                maxFemale.length
            ];
            return lengths.every(length => length === lengths[0]);
        }

        function handleNumericDropdownChange(selectedValue) {
            $('#just-values-container').remove();
            $('#multiple-age-range-container').remove();
            $('#multiple-text-value-container').remove();
            $('#gender-range-container').remove();
            if (selectedValue === 'just-values') {
                $('#numeric-dropdown-container').after(`
                    <div class="col-md-6 mb-3" id="just-values-container">
                        <label class="form-label" for="just-values-textarea">Enter Values</label>
                        <textarea id="just-values-textarea" class="form-control" placeholder="Enter values here" required></textarea>
                        <div class="invalid-feedback"> Fill up the just values.</div>
                    </div>
                `);
                const editValuesElement = document.getElementById("edit_just_values");
                const textareaElement = document.getElementById("just-values-textarea");
                if (textareaElement) {
                    textareaElement.value = editValuesElement?.value || '';
                }
                fields = ["select2Basic-group", /* "select2Basic-sub-group" , "select2Basic-sub-sub-group" */ , "numeric-dropdown", "just-values-textarea"];
            } else if (selectedValue === 'multiple-age-range') {
                function findEqualLengthsAgeRange(ageFrom, ageTo, minMale, maxMale, minFemale, maxFemale) {
                    const lengths = [
                        ageFrom.length,
                        ageTo.length,
                        minMale.length,
                        maxMale.length,
                        minFemale.length,
                        maxFemale.length
                    ];
                    return lengths.every(length => length === lengths[0]);
                }
                const ageRange = document.getElementsByClassName("edit_multiple_age_range-ageRange");
                const ageRangeMinMale = document.getElementsByClassName("edit_multiple_age_range-male_min");
                const ageRangeMaxMale = document.getElementsByClassName("edit_multiple_age_range-male_max");
                const ageRangeMinFemale = document.getElementsByClassName("edit_multiple_age_range-female_min");
                const ageRangeMaxFemale = document.getElementsByClassName("edit_multiple_age_range-female_max");

                function findEqualLengthsAgeRange(ageRange, minMale, maxMale, minFemale, maxFemale) {
                    const lengths = [
                        ageRange.length,
                        minMale.length,
                        maxMale.length,
                        minFemale.length,
                        maxFemale.length
                    ];
                    return lengths.every(length => length === lengths[0]);
                }
                const equalLengthsAgeRange = findEqualLengthsAgeRange(ageRange, ageRangeMinMale, ageRangeMaxMale, ageRangeMinFemale, ageRangeMaxFemale);
                if (!equalLengthsAgeRange) {
                    return;
                }

                function getValuesInput(collection) {
                    return Array.from(collection).map(input => input.value);
                }

                function parseAgeRanges(ageRangeStrings) {
                    const fromAges = [];
                    const toAges = [];
                    ageRangeStrings.forEach(rangeStr => {
                        const [from, to] = rangeStr.split('-');
                        fromAges.push(from);
                        toAges.push(to);
                    });
                    return {
                        fromAges,
                        toAges
                    };
                }
                const ageRangeStrings = getValuesInput(ageRange);
                const {
                    fromAges,
                    toAges
                } = parseAgeRanges(ageRangeStrings);
                const ageRangeData = {
                    rowCount: ageRange.length,
                    fromAge: fromAges,
                    toAge: toAges,
                    male: {
                        min: getValuesInput(ageRangeMinMale),
                        max: getValuesInput(ageRangeMaxMale)
                    },
                    female: {
                        min: getValuesInput(ageRangeMinFemale),
                        max: getValuesInput(ageRangeMaxFemale)
                    }
                };
                $('#numeric-dropdown-container').after(`
                    <div class="col-md-12 mb-3" id="multiple-age-range-container">
                        <label class="form-label">Age Range & Limits</label>
                        <div id="age-range-fields">
                            ${generateAgeRangeRow(true, ageRangeData)}
                        </div>
                    </div>
                `);
                multipleAgeRangeFieldCount = ageRangeData.rowCount;
            } else if (selectedValue === 'multiple-text-value') {
                $('#numeric-dropdown-container').after(`
                    <div class="col-md-12 mb-3" id="multiple-text-value-container">
                        <label class="form-label">Description & Limits</label>
                        <div id="text-value-fields">
                            ${generateTextValueRow(true, getRowCount())}
                        </div>
                    </div>
                `);
                multipleTextValueFieldCount = 1;
            } else if (selectedValue === 'no-age-range') {
                $('#numeric-dropdown-container').after(`
                    <div class="col-md-12 mb-3" id="gender-range-container">
                        <label class="form-label">Gender-based Limits</label>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="male-min">Male Min</label>
                                <input type="number" id="no-age-range-male-min" class="form-control" placeholder="Min" required>
                                <div class="invalid-feedback"> Fill up the male minimum.</div>
                            </div>
                            <div class="col-md-3">
                                <label for="male-max">Male Max</label>
                                <input type="number" id="no-age-range-male-max" class="form-control" placeholder="Max" required>
                                <div class="invalid-feedback"> Fill up the male maximum.</div>
                            </div>
                            <div class="col-md-3">
                                <label for="female-min">Female Min</label>
                                <input type="number" id="no-age-range-female-min" class="form-control" placeholder="Min" required>
                                <div class="invalid-feedback"> Fill up the female minimum.</div>
                            </div>
                            <div class="col-md-3">
                                <label for="female-max">Female Max</label>
                                <input type="number" id="no-age-range-female-max" class="form-control" placeholder="Max" required>
                                <div class="invalid-feedback"> Fill up the female maximum.</div>
                            </div>
                        </div>
                    </div>
                `);
                const setElementValue = (targetId, sourceId) => {
                    const targetElement = document.getElementById(targetId);
                    const sourceElement = document.getElementById(sourceId);
                    if (targetElement) {
                        targetElement.value = sourceElement?.value || '';
                    }
                };
                setElementValue("just-values-textarea", "edit_just_values");
                setElementValue("no-age-range-male-min", "edit_no_age_range_male_min");
                setElementValue("no-age-range-male-max", "edit_no_age_range_male_max");
                setElementValue("no-age-range-female-min", "edit_no_age_range_female_min");
                setElementValue("no-age-range-female-max", "edit_no_age_range_female_max");
                fields = ["select2Basic-group", /* "select2Basic-sub-group", "select2Basic-sub-sub-group" */ , "numeric-dropdown", "no-age-range-male-min", "no-age-range-male-max", "no-age-range-female-min", "no-age-range-female-max"];
            }
        }

        function generateAgeRangeRow(isFirstRow = false, data = null) {
            let ageOptions = '<option value="" selected disabled>Select Age</option>';
            for (let i = 1; i <= 100; i++) {
                ageOptions += `<option value="${i}">${i}</option>`;
            }
            let rowsHtml = '';
            if (data && data.rowCount) {
                for (let i = 0; i < data.rowCount; i++) {
                    const fromAge = data.fromAge[i] || '';
                    const toAge = data.toAge[i] || '';
                    const minMale = data.male.min[i] || '';
                    const maxMale = data.male.max[i] || '';
                    const minFemale = data.female.min[i] || '';
                    const maxFemale = data.female.max[i] || '';
                    const headerRow = i === 0 ? `
                <div class="row g-2 mb-1">
                    <div class="col-md-2"><label class="form-label">From Age</label></div>
                    <div class="col-md-2"><label class="form-label">To Age</label></div>
                    <div class="col-md-2"><label class="form-label">Min Male</label></div>
                    <div class="col-md-2"><label class="form-label">Max Male</label></div>
                    <div class="col-md-2"><label class="form-label">Min Female</label></div>
                    <div class="col-md-2"><label class="form-label">Max Female</label></div>
                </div>` : '';
                    let fromAgeOptions = '<option value="" disabled>Select Age</option>';
                    let toAgeOptions = '<option value="" disabled>Select Age</option>';
                    for (let j = 1; j <= 100; j++) {
                        fromAgeOptions += `<option value="${j}" ${fromAge == j ? 'selected' : ''}>${j}</option>`;
                        toAgeOptions += `<option value="${j}" ${toAge == j ? 'selected' : ''}>${j}</option>`;
                    }
                    const ageRangeRow = `
                ${headerRow}
                <div class="row g-2 mb-2 align-items-center age-range-row">
                    <div class="col-md-2">
                        <select class="form-select age-from select2Basic-ageFrom" id="select2Basic-ageFrom-${i}" name="select2Basic-ageFrom[]" required>${fromAgeOptions}</select>
                        <div class="invalid-feedback">Select the age from.</div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select age-to select2Basic-ageTo" id="select2Basic-ageTo-${i}" name="select2Basic-ageTo[]" required>${toAgeOptions}</select>
                        <div class="invalid-feedback">Select the age to.</div>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control min-male multiple-age-range-min-male" id="multiple-age-range-min-male-${i}" name="multiple-age-range-min-male[]" value="${minMale}" placeholder="Min Male" required/>
                        <div class="invalid-feedback">Fill up the male minimum.</div>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control max-male multiple-age-range-max-male" id="multiple-age-range-max-male-${i}" name="multiple-age-range-max-male[]" value="${maxMale}" placeholder="Max Male" required/>
                        <div class="invalid-feedback">Fill up the male maximum.</div>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control min-female multiple-age-range-min-female" id="multiple-age-range-min-female-${i}" name="multiple-age-range-min-female[]" value="${minFemale}" placeholder="Min Female" required/>
                        <div class="invalid-feedback">Fill up the female minimum.</div>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex">
                            <div class="flex-grow-1 me-2">
                                <input type="number" class="form-control max-female multiple-age-range-max-female" id="multiple-age-range-max-female-${i}" name="multiple-age-range-max-female[]" value="${maxFemale}" placeholder="Max Female" required/>
                                <div class="invalid-feedback">Fill up the female maximum.</div>
                            </div>
                            ${i === 0
                            ? `<button type="button" class="btn btn-success add-row-multipleAgeRange" style="padding: 2px 6px; font-size: 0.75rem; height: 24px; line-height: 1;"><i class="fas fa-plus"></i></button>`
                            : `<button type="button" class="btn btn-danger remove-row-multipleAgeRange" style="padding: 2px 6px; font-size: 0.75rem; height: 24px; line-height: 1;"><i class="fas fa-trash"></i></button>`
                        }
                        </div>
                    </div>
                </div>`;
                    rowsHtml += ageRangeRow;
                }
                return rowsHtml;
            } else {
                return `
            ${isFirstRow ? `
                <div class="row g-2 mb-1">
                    <div class="col-md-2"><label class="form-label">From Age</label></div>
                    <div class="col-md-2"><label class="form-label">To Age</label></div>
                    <div class="col-md-2"><label class="form-label">Min Male</label></div>
                    <div class="col-md-2"><label class="form-label">Max Male</label></div>
                    <div class="col-md-2"><label class="form-label">Min Female</label></div>
                    <div class="col-md-2"><label class="form-label">Max Female</label></div>
                </div>` : ''}
                <div class="row g-2 mb-2 align-items-center age-range-row">
                    <div class="col-md-2">
                        <select class="form-select age-from select2Basic-ageFrom" id="select2Basic-ageFrom-0" name="select2Basic-ageFrom[]" required>${ageOptions}</select>
                        <div class="invalid-feedback">Select the age from.</div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select age-to select2Basic-ageTo" id="select2Basic-ageTo-0" name="select2Basic-ageTo[]" required>${ageOptions}</select>
                        <div class="invalid-feedback">Select the age to.</div>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control min-male multiple-age-range-min-male" id="multiple-age-range-min-male-0" name="multiple-age-range-min-male[]" placeholder="Min Male" required/>
                        <div class="invalid-feedback">Fill up the male minimum.</div>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control max-male multiple-age-range-max-male" id="multiple-age-range-max-male-0" name="multiple-age-range-max-male[]" placeholder="Max Male" required/>
                        <div class="invalid-feedback">Fill up the male maximum.</div>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control min-female multiple-age-range-min-female" id="multiple-age-range-min-female-0" name="multiple-age-range-min-female[]" placeholder="Min Female" required/>
                        <div class="invalid-feedback">Fill up the female minimum.</div>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex">
                            <div class="flex-grow-1 me-2">
                                <input type="number" class="form-control max-female multiple-age-range-max-female" id="multiple-age-range-max-female-0" name="multiple-age-range-max-female[]" placeholder="Max Female" required/>
                                <div class="invalid-feedback">Fill up the female maximum.</div>
                            </div>
                            ${isFirstRow
                        ? `<button type="button" class="btn btn-success add-row-multipleAgeRange" style="padding: 2px 6px; font-size: 0.75rem; height: 24px; line-height: 1;"><i class="fas fa-plus"></i></button>`
                        : `<button type="button" class="btn btn-danger remove-row-multipleAgeRange" style="padding: 2px 6px; font-size: 0.75rem; height: 24px; line-height: 1;"><i class="fas fa-trash"></i></button>`
                    }
                        </div>
                    </div>
                </div>`;
            }
        }

        function generateTextValueRow(isFirstRow = false, data) {
            console.log(data.rowCount);
            let rowsHtml = '';
            const rowCount = data.rowCount > 0 ? data.rowCount : 1;
            for (let i = 0; i < rowCount; i++) {
                const description = data.descriptions?.[i] || '';
                const minMale = data.male?.min?.[i] || '';
                const maxMale = data.male?.max?.[i] || '';
                const minFemale = data.female?.min?.[i] || '';
                const maxFemale = data.female?.max?.[i] || '';
                const shouldShowHeader = (i === 0 && isFirstRow && $('#text-value-fields .text-value-row').length === 0);
                const isVeryFirstRow = isFirstRow && i === 0 && $('#text-value-fields .text-value-row').length === 0;
                const buttonHtml = isVeryFirstRow ?
                    `<button type="button" class="btn btn-success add-row-multipleTextValue" style="padding: 2px 6px; font-size: 0.75rem; height: 24px; line-height: 1;"><i class="fas fa-plus"></i></button>` :
                    `<button type="button" class="btn btn-danger remove-row-multipleTextValue" style="padding: 2px 6px; font-size: 0.75rem; height: 24px; line-height: 1;"><i class="fas fa-trash"></i></button>`;
                const textValuerow = `
            ${shouldShowHeader ? `
            <div class="row g-2 mb-1 header-row">
                <div class="col-md-3"><label class="form-label">Description</label></div>
                <div class="col-md-2"><label class="form-label">Min Male</label></div>
                <div class="col-md-2"><label class="form-label">Max Male</label></div>
                <div class="col-md-2"><label class="form-label">Min Female</label></div>
                <div class="col-md-2"><label class="form-label">Max Female</label></div>
            </div>` : ''}
            <div class="row g-2 mb-2 align-items-center text-value-row">
                <div class="col-md-3">
                    <input type="text" class="form-control description multiple-text-value-description" id="text-value-description-${i}" value="${description}" placeholder="Enter description" required/>
                    <div class="invalid-feedback">Fill up the description.</div>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control min-male multiple-text-value-min-male" id="multiple-text-value-min-male-${i}" value="${minMale}" placeholder="Min Male" required/>
                    <div class="invalid-feedback">Fill up the male minimum.</div>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control max-male multiple-text-value-max-male" id="multiple-text-value-max-male-${i}" value="${maxMale}" placeholder="Max Male" required/>
                    <div class="invalid-feedback">Fill up the male maximum.</div>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control min-female multiple-text-value-min-female" id="multiple-text-value-min-female-${i}" value="${minFemale}" placeholder="Min Female" required/>
                    <div class="invalid-feedback">Fill up the female minimum.</div>
                </div>
                <div class="col-md-2">
                    <div class="d-flex">
                        <div class="flex-grow-1 me-2">
                            <input type="number" class="form-control max-female multiple-text-value-max-female" id="multiple-text-value-max-female-${i}" value="${maxFemale}" placeholder="Max Female" required/>
                            <div class="invalid-feedback">Fill up the female maximum.</div>
                        </div>
                        ${buttonHtml}
                    </div>
                </div>
            </div>
        `;
                rowsHtml += textValuerow;
            }
            return rowsHtml;
        }
        $('#numeric-dropdown').on('change', function() {
            const selectedValue = $(this).val();
            handleNumericDropdownChange(selectedValue);
        });
        const numeric_type = document.getElementById("edit_numeric_type").value;
        if (numeric_type) {
            handleNumericDropdownChange(numeric_type);
        }
        $(document).on('click', '.add-row-multipleAgeRange', function() {
            if (multipleAgeRangeFieldCount < multipleAgeRangeMaxFields) {
                $('#age-range-fields').append(generateAgeRangeRow(false));
                multipleAgeRangeFieldCount++;
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Limit Reached!',
                    text: `You can only add up to ${multipleAgeRangeMaxFields} age range rows.`,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            }
        });
        $(document).on('click', '.remove-row-multipleAgeRange', function() {
            $(this).closest('.age-range-row').remove();
            multipleAgeRangeFieldCount--;
        });
        $(document).on('click', '.add-row-multipleTextValue', function() {
            if (multipleTextValueFieldCount < multipleAgeRangeMaxFields) {
                const emptyRowData = {
                    rowCount: 1,
                    descriptions: [''],
                    male: {
                        min: [''],
                        max: ['']
                    },
                    female: {
                        min: [''],
                        max: ['']
                    }
                };
                $('#text-value-fields').append(generateTextValueRow(false, emptyRowData));
                multipleTextValueFieldCount++;
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Limit Reached!',
                    text: `You can only add up to ${multipleAgeRangeMaxFields} rows.`,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            }
        });

        function getRowCount() {
            const textValueDescription = document.getElementsByClassName("edit_multiple-text-value_text_value_description");
            const minMale = document.getElementsByClassName("edit_multiple-text-value_male_min");
            const maxMale = document.getElementsByClassName("edit_multiple-text-value_male_max");
            const minFemale = document.getElementsByClassName("edit_multiple-text-value_female_min");
            const maxFemale = document.getElementsByClassName("edit_multiple-text-value_female_max");
            const equalLengthArrays = findEqualLengths(textValueDescription, minMale, maxMale, minFemale, maxFemale);
            if (!equalLengthArrays) {
                return;
            }

            function getValues(collection) {
                return Array.from(collection).map(input => input.value);
            }
            const data = {
                rowCount: textValueDescription.length,
                descriptions: getValues(textValueDescription),
                male: {
                    min: getValues(minMale),
                    max: getValues(maxMale)
                },
                female: {
                    min: getValues(minFemale),
                    max: getValues(maxFemale)
                }
            };
            return data;
        }
        $(document).on('click', '.remove-row-multipleTextValue', function() {
            $(this).closest('.text-value-row').remove();
            multipleTextValueFieldCount--;
        });
    }

    function populateDropdowns() {
        const groupSelect = $('#select2Basic-group');
        const subGroupSelect = $('#select2Basic-sub-group');
        const subSubGroupSelect = $('#select2Basic-sub-sub-group');
        subGroupSelect.prop('disabled', true);
        subSubGroupSelect.prop('disabled', true);

        function resetDownstreamDropdowns(dropdown) {
            dropdown.html('<option value="">Select an option</option>').prop('disabled', true);
            dropdown.trigger('change');
        }

        function getNumericValue(elementId) {
            const value = document.getElementById(elementId)?.value;
            return !isNaN(value) && value.trim() !== "" ? value : null;
        }
        const groupId = getNumericValue("edit_testgroup_id");
        const subGroupId = getNumericValue("edit_subgroup_id");
        const subSubGroupId = getNumericValue("edit_subsubgroup_id");
        apiRequest({
            url: 'https://mhv-admin.hygeiaes.com/test-group/getAllGroups',
            method: 'GET',
            onSuccess: (response) => {
                if (response.result) {
                    groupSelect.html('<option value="">Select Group</option>');
                    response.message.forEach(group => {
                        const isSelected = group.test_group_id.toString() === groupId ? 'selected' : '';
                        groupSelect.append(`<option value="${group.test_group_id}" ${isSelected}>${group.test_group_name}</option>`);
                    });
                    groupSelect.data('fullData', response.message);
                    groupSelect.select2({
                        placeholder: "Select Group"
                    });
                    if (groupId) {
                        groupSelect.val(groupId).trigger('change');
                    }
                } else {
                    showToast('error', 'Error', response.message);
                }
            },
            onError: (error) => {
                showToast('error', 'Error', 'Failed to fetch groups: ' + error);
            }
        });
        groupSelect.on('change', function() {
            const selectedGroupId = $(this).val();
            const fullData = groupSelect.data('fullData');
            resetDownstreamDropdowns(subGroupSelect);
            resetDownstreamDropdowns(subSubGroupSelect);
            if (selectedGroupId && fullData) {
                const selectedGroup = fullData.find(g => g.test_group_id.toString() === selectedGroupId);
                if (selectedGroup && Array.isArray(selectedGroup.subgroups)) {
                    const validSubgroups = selectedGroup.subgroups.filter(sg => sg.group_type === 2);
                    subGroupSelect.html('<option value="">Select Sub Group</option>');
                    validSubgroups.forEach(subgroup => {
                        const isSelected = subgroup.test_group_id.toString() === subGroupId ? 'selected' : '';
                        subGroupSelect.append(`<option value="${subgroup.test_group_id}" ${isSelected}>${subgroup.test_group_name}</option>`);
                    });
                    subGroupSelect.prop('disabled', false).select2({
                        placeholder: "Select Sub Group"
                    });
                    if (subGroupId) {
                        subGroupSelect.val(subGroupId).trigger('change');
                    }
                }
            }
        });
        subGroupSelect.on('change', function() {
            const selectedSubGroupId = $(this).val();
            const selectedGroupId = groupSelect.val();
            const fullData = groupSelect.data('fullData');
            resetDownstreamDropdowns(subSubGroupSelect);
            if (selectedSubGroupId && selectedGroupId && fullData) {
                const selectedGroup = fullData.find(g => g.test_group_id.toString() === selectedGroupId);
                const validSubSubgroups = selectedGroup?.subgroups?.filter(sg => sg.group_type === 3 && sg.subgroup_id?.toString() === selectedSubGroupId) || [];
                if (validSubSubgroups.length > 0) {
                    subSubGroupSelect.html('<option value="">Select Sub Sub Group</option>');
                    validSubSubgroups.forEach(subsubgroup => {
                        const isSelected = subsubgroup.test_group_id.toString() === subSubGroupId ? 'selected' : '';
                        subSubGroupSelect.append(`<option value="${subsubgroup.test_group_id}" ${isSelected}>${subsubgroup.test_group_name}</option>`);
                    });
                    subSubGroupSelect.prop('disabled', false).select2({
                        placeholder: "Select Sub Sub Group"
                    });
                    if (subSubGroupId) {
                        subSubGroupSelect.val(subSubGroupId).trigger('change');
                    }
                }
            }
        });
    }
    $(document).ready(function() {
        var isValid = false;
        uiCodes();
        populateDropdowns();
        addValidation();
    });
</script>
@endsection