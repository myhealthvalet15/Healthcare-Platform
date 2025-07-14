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
    <h5 class="card-header">Add Test</h5>
    <form class="card-body needs-validation" novalidate id="master-test">
        <div class="row g-6">
            <!-- Test Name -->
            <div class="col-md-3 mb-3">
                <label class="form-label" for="multicol-test-name">Test
                    Name</label>
                <input type="text" id="test-name" class="form-control" placeholder="Test Name" required />
                <div class="invalid-feedback"> Enter the test name.
                </div>
            </div>
            <!-- Group -->
            <div class="col-md-3 mb-3">
                <label for="select2Basic-group" class="form-label">Group</label>
                <select id="select2Basic-group" class="select2 form-select form-select-lg" required>
                    <option value>Select Group</option>
                </select>
                <div class="invalid-feedback"> Select a group. </div>
            </div>
            <!-- Sub Group -->
            <div class="col-md-3 mb-3">
                <label for="select2Basic-sub-group" class="form-label">Sub
                    Group</label>
                <select id="select2Basic-sub-group" class="select2 form-select form-select-lg">
                    <option value>Select Sub Group</option>
                </select>
                <div class="invalid-feedback"> Select a sub-group. </div>
            </div>
            <!-- Sub Sub Group -->
            <div class="col-md-3 mb-3">
                <label for="select2Basic-sub-sub-group" class="form-label">Sub
                    Sub Group</label>
                <select id="select2Basic-sub-sub-group" class="select2 form-select form-select-lg">
                    <option value>Select Sub Sub Group</option>
                </select>
                <div class="invalid-feedback"> Select a sub-sub group.
                </div>
            </div>
            <!-- Description -->
            <div class="col-md-6 mb-3">
                <label class="form-label" for="basic-default-description">Description</label>
                <textarea id="basic-default-description" class="form-control"
                    placeholder="Write the description here"></textarea>
                <div class="invalid-feedback"> Provide a description.
                </div>
            </div>
            <!-- Remarks -->
            <div class="col-md-6 mb-3">
                <label class="form-label" for="basic-default-remarks">Remarks</label>
                <textarea id="basic-default-remarks" class="form-control"
                    placeholder="Write the remarks here"></textarea>
                <div class="invalid-feedback"> Provide remarks. </div>
            </div>
            <!-- Type Selection -->
            <div class="col-md-3 mb-3">
                <label for="select2Basic-type" class="form-label">Type</label>
                <select id="select2Basic-type" class="select2 form-select form-select-lg" required>
                    <option value="text">Text</option>
                    <option value="numeric">Numeric</option>
                </select>
                <div class="invalid-feedback"> Select a type. </div>
            </div>
            <!-- Unit -->
            <div class="col-md-3 mb-3">
                <label class="form-label" for="multicol-unit">Unit</label>
                <input type="text" id="multicol-unit" class="form-control" placeholder="Unit" />
                <div class="invalid-feedback"> Enter a unit. </div>
            </div>
            <!-- Dynamic Text Inputs -->
            <div class="col-md-6 mb-3" id="text-input-container" style="display: none;">
                <label class="form-label">Add Conditions</label>
                <div id="dynamic-text-fields">
                    <div class="d-flex mb-2 align-items-center">
                        <input type="text" class="form-control me-2 condition-input" placeholder="Enter condition"
                            name="basic-default-text-condition[]" />
                        <button type="button" class="btn btn-sm btn-success add-row">+</button>
                        <div class="invalid-feedback">Enter at least one
                            condition.</div>
                    </div>
                </div>
            </div>
            <!-- Numeric Dropdown (Initially Hidden) -->
            <div class="col-md-6 mb-3" id="numeric-dropdown-container" style="display: none;">
                <label class="form-label">Numeric</label>
                <select id="numeric-dropdown" class="select2 form-select form-select-lg" required>
                    <option value="no-age-range">No age range</option>
                    <option value="multiple-age-range">Multiple age
                        range</option>
                    <option value="multiple-text-value">Multiple text
                        value</option>
                    <option value="just-values">Just values</option>
                </select>
                <div class="invalid-feedback"> Select a numeric type</div>
            </div>
            <!-- Submit Button -->
            <div class="pt-6">
                <button type="submit" id="submitButton" class="btn btn-primary me-4"><i
                        class="fa-solid fa-plus"></i>&nbsp;Add</button>
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
                            return arr.some(val => val === null || val === '' || isNaN(val));
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
                            showToast('error', 'Error', 'Invalid range, fill all the rows properly with valid numbers');
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
                            return arr.some(val => val === null || val === '' || isNaN(val));
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
                    var url = 'https://mhv-admin.hygeiaes.com/test-group/add-tests';
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
                        form.reset();
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }
                } catch (error) {
                    showToast('error', 'Error', 'An error occurred while adding the group');
                    form.reset();
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
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
        $('#numeric-dropdown').on('change', function() {
            const selectedValue = $(this).val();
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
                fields = [
                    "select2Basic-group",
                    // "select2Basic-sub-group",
                    // "select2Basic-sub-sub-group",
                    "numeric-dropdown",
                    "just-values-textarea"
                ]
            } else if (selectedValue === 'multiple-age-range') {
                $('#numeric-dropdown-container').after(`
                    <div class="col-md-12 mb-3" id="multiple-age-range-container">
                        <label class="form-label">Age Range & Limits</label>
                        <div id="age-range-fields">
                            ${generateAgeRangeRow(true)}
                        </div>
                    </div>
                `);
                multipleAgeRangeFieldCount = 1;
            } else if (selectedValue === 'multiple-text-value') {
                $('#numeric-dropdown-container').after(`
                    <div class="col-md-12 mb-3" id="multiple-text-value-container">
                        <label class="form-label">Description & Limits</label>
                        <div id="text-value-fields">
                            ${generateTextValueRow(true)}
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
                    </div>`);
                fields = [
                    "select2Basic-group",
                    // "select2Basic-sub-group",
                    // "select2Basic-sub-sub-group",
                    "numeric-dropdown",
                    "no-age-range-male-min",
                    "no-age-range-male-max",
                    "no-age-range-female-min",
                    "no-age-range-female-max",
                ]
            }
        });

        function generateAgeRangeRow(isFirstRow = false) {
            let ageOptions = '<option value="" selected disabled>Select Age</option>';
            for (let i = 1; i <= 100; i++) {
                ageOptions += `<option value="${i}">${i}</option>`;
            }
            var ageRangeRow = `
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
                        <select class="form-select age-from select2Basic-ageFrom" id='select2Basic-ageFrom' name='select2Basic-ageFrom[]' required>${ageOptions}</select>
                        <div class="invalid-feedback">Select the age from.</div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select age-to select2Basic-ageTo" id='select2Basic-ageTo' name='select2Basic-ageTo[]' required>${ageOptions}</select>
                        <div class="invalid-feedback">Select the age to.</div>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control min-male multiple-age-range-min-male" id='multiple-age-range-min-male' name='multiple-age-range-min-male[]' placeholder="Min Male" required/>
                        <div class="invalid-feedback">Fill up the male minimum.</div>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control max-male multiple-age-range-max-male" id='multiple-age-range-max-male' name='multiple-age-range-max-male[]' placeholder="Max Male" required/>
                        <div class="invalid-feedback">Fill up the male maximum.</div>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control min-female multiple-age-range-min-female" id='multiple-age-range-min-female' name='multiple-age-range-min-female[]' placeholder="Min Female" required/>
                        <div class="invalid-feedback">Fill up the female minimum.</div>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex">
                            <div class="flex-grow-1 me-2">
                                <input type="number" class="form-control max-female multiple-age-range-max-female" id='multiple-age-range-max-female' name='multiple-age-range-max-female[]' placeholder="Max Female" required/>
                                <div class="invalid-feedback">Fill up the female maximum.</div>
                            </div>
                            ${isFirstRow
                    ? `<button type="button" class="btn btn-success add-row-multipleAgeRange" style="padding: 2px 6px; font-size: 0.75rem; height: 24px; line-height: 1;"><i class="fas fa-plus"></i></button>`
                    : `<button type="button" class="btn btn-danger remove-row-multipleAgeRange" style="padding: 2px 6px; font-size: 0.75rem; height: 24px; line-height: 1;"><i class="fas fa-trash"></i></button>`
                }
                        </div>
                    </div>
                </div>`;
            fields = [
                "select2Basic-group",
                // "select2Basic-sub-group",
                // "select2Basic-sub-sub-group",
                "numeric-dropdown",
                "select2Basic-ageFrom",
                "select2Basic-ageTo",
                "multiple-age-range-min-male",
                "multiple-age-range-max-male",
                "multiple-age-range-min-female",
                "multiple-age-range-max-female",
            ]
            return ageRangeRow;
        }

        function generateTextValueRow(isFirstRow = false) {
            var textValuerow = `
                ${isFirstRow ? `
                <div class="row g-2 mb-1">
                    <div class="col-md-3"><label class="form-label">Description</label></div>
                    <div class="col-md-2"><label class="form-label">Min Male</label></div>
                    <div class="col-md-2"><label class="form-label">Max Male</label></div>
                    <div class="col-md-2"><label class="form-label">Min Female</label></div>
                    <div class="col-md-2"><label class="form-label">Max Female</label></div>
                </div>` : ''}
                <div class="row g-2 mb-2 align-items-center text-value-row">
                    <div class="col-md-3">
                        <input type="text" class="form-control description multiple-text-value-description" id="text-value-description" placeholder="Enter description" required/>
                        <div class="invalid-feedback">Fill up the description.</div>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control min-male multiple-text-value-min-male" id="multiple-text-value-min-male" placeholder="Min Male" required/>
                        <div class="invalid-feedback">Fill up the male minimum.</div>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control max-male multiple-text-value-max-male" id="multiple-text-value-max-male" placeholder="Max Male" required/>
                        <div class="invalid-feedback">Fill up the male maximum.</div>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control min-female multiple-text-value-min-female" id="multiple-text-value-min-female" placeholder="Min Female" required/>
                        <div class="invalid-feedback">Fill up the female minimum.</div>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex">
                            <div class="flex-grow-1 me-2">
                                <input type="number" class="form-control max-female multiple-text-value-max-female" id="multiple-text-value-max-female" placeholder="Max Female"required/>
                                <div class="invalid-feedback">Fill up the female maximum.</div>
                            </div>
                            ${isFirstRow
                    ? `<button type="button" class="btn btn-success add-row-multipleTextValue" style="padding: 2px 6px; font-size: 0.75rem; height: 24px; line-height: 1;"><i class="fas fa-plus"></i></button>`
                    : `<button type="button" class="btn btn-danger remove-row-multipleTextValue" style="padding: 2px 6px; font-size: 0.75rem; height: 24px; line-height: 1;"><i class="fas fa-trash"></i></button>`
                }
                        </div>
                    </div>
                </div>
            `;
            fields = [
                "select2Basic-group",
                // "select2Basic-sub-group",
                // "select2Basic-sub-sub-group",
                "numeric-dropdown",
                "text-value-description",
                "multiple-text-value-min-male",
                "multiple-text-value-max-male",
                "multiple-text-value-min-female",
                "multiple-text-value-max-female",
            ]
            return textValuerow;
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
                $('#text-value-fields').append(generateTextValueRow(false));
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
        apiRequest({
            url: 'https://mhv-admin.hygeiaes.com/test-group/getAllGroups',
            method: 'GET',
            onSuccess: (response) => {
                if (response.result) {
                    groupSelect.html('<option value="">Select Group</option>');
                    response.message.forEach(group => {
                        groupSelect.append(`<option value="${group.test_group_id}">${group.test_group_name}</option>`);
                    });
                    groupSelect.data('fullData', response.message);
                    groupSelect.select2({
                        placeholder: "Select Group"
                    });
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
                if (selectedGroup && Array.isArray(selectedGroup.subgroups) && selectedGroup.subgroups.length > 0) {
                    subGroupSelect.html('<option value="">Select Sub Group</option>');
                    const validSubgroups = selectedGroup.subgroups.filter(sg => sg.group_type === 2);
                    validSubgroups.forEach(subgroup => {
                        subGroupSelect.append(`<option value="${subgroup.test_group_id}">${subgroup.test_group_name}</option>`);
                    });
                    subGroupSelect.prop('disabled', false);
                    subGroupSelect.select2({
                        placeholder: "Select Sub Group"
                    });
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
                const validSubSubgroups = selectedGroup?.subgroups?.filter(sg =>
                    sg.group_type === 3 && sg.subgroup_id?.toString() === selectedSubGroupId
                ) || [];
                if (validSubSubgroups.length > 0) {
                    subSubGroupSelect.html('<option value="">Select Sub Sub Group</option>');
                    validSubSubgroups.forEach(subsubgroup => {
                        subSubGroupSelect.append(`<option value="${subsubgroup.test_group_id}">${subsubgroup.test_group_name}</option>`);
                    });
                    subSubGroupSelect.prop('disabled', false);
                    subSubGroupSelect.select2({
                        placeholder: "Select Sub Sub Group"
                    });
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