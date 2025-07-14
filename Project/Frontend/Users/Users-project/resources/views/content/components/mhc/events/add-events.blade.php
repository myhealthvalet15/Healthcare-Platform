@extends('layouts/layoutMaster')
@section('title', 'Events - Create Event')
@section('vendor-style')
@vite([
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/typeahead-js/typeahead.scss',
'resources/assets/vendor/libs/tagify/tagify.scss',
'resources/assets/vendor/libs/spinkit/spinkit.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/animate-css/animate.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/typeahead-js/typeahead.js',
'resources/assets/vendor/libs/tagify/tagify.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection
@section('page-script')
@vite(['resources/assets/js/form-layouts.js',
'resources/assets/js/extended-ui-sweetalert2.js'])
@endsection
@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Create Event</h5>
    </div>
    <div class="card-body">
        <form id="eventForm" method="POST" action="javascript:void(0);">
            @csrf
            <div class="row">
                <div class="mb-3 col-md-4">
                    <label for="event_name" class="form-label">Event Name</label>
                    <input type="text" class="form-control" id="event_name" name="event_name" required>
                    <div class="invalid-feedback d-block" id="event_name_error"></div>
                </div>
                <div class="mb-3 col-md-4">
                    <label for="from_date" class="form-label">From Date & Time</label>
                    <input type="text" class="form-control flatpickr-date-time" id="from_date" name="from_date" placeholder="Select start date & time" required>
                    <div class="invalid-feedback d-block" id="from_date_error"></div>
                </div>
                <div class="mb-3 col-md-4">
                    <label for="to_date" class="form-label">To Date & Time</label>
                    <input type="text" class="form-control flatpickr-date-time" id="to_date" name="to_date" placeholder="Select end date & time" required>
                    <div class="invalid-feedback d-block" id="to_date_error"></div>
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-md-6">
                    <label for="event_description" class="form-label">Event Description</label>
                    <textarea class="form-control" id="event_description" name="event_description" rows="3"></textarea>
                    <div class="invalid-feedback d-block" id="event_description_error"></div>
                </div>
                <div class="mb-3 col-md-6">
                    <label for="guest_name" class="form-label">Guest Name</label>
                    <input type="text" class="form-control" id="guest_name" name="guest_name">
                    <div class="invalid-feedback d-block" id="guest_name_error"></div>
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-md-4">
                    <label for="department" class="form-label">Department</label>
                    <select class="form-select select2" id="department" name="department">
                        <option value="">Select Department</option>
                    </select>
                    <div class="invalid-feedback d-block" id="department_error"></div>
                </div>
                <div class="mb-3 col-md-4">
                    <label for="employee_type" class="form-label">Employee Type</label>
                    <select class="form-select select2" id="employee_type" name="employee_type">
                        <option value="">Select Employee Type</option>
                    </select>
                    <div class="invalid-feedback d-block" id="employee_type_error"></div>
                </div>
                <div class="mb-3 col-md-4">
                    <label for="test" class="form-label">Test</label>
                    <select class="form-select select2" id="test" name="test">
                        <option value="">Select Test</option>
                    </select>
                    <div class="invalid-feedback d-block" id="test_error"></div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Create Event</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('eventForm');
    const errorFields = [
        'event_name', 'from_date', 'to_date', 'event_description',
        'guest_name', 'department', 'employee_type', 'test'
    ];

    function clearErrors() {
        errorFields.forEach(field => {
            document.getElementById(field + '_error').textContent = '';
        });
    }

    function showError(field, message) {
        document.getElementById(field + '_error').textContent = message;
    }

    function validateForm(data) {
        let valid = true;
        clearErrors();

        if (!data.event_name.trim()) {
            showError('event_name', 'Event name is required.');
            valid = false;
        }
        if (!data.from_date.trim()) {
            showError('from_date', 'From date & time is required.');
            valid = false;
        }
        if (!data.to_date.trim()) {
            showError('to_date', 'To date & time is required.');
            valid = false;
        }
        if (data.from_date && data.to_date) {
            const from = new Date(data.from_date);
            const to = new Date(data.to_date);
            if (from > to) {
                showError('to_date', 'To date must be after From date.');
                valid = false;
            }
        }
      
        // Optional: event_description and guest_name can be left blank

        return valid;
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = {
            event_name: form.event_name.value,
            from_date: form.from_date.value,
            to_date: form.to_date.value,
            event_description: form.event_description.value,
            guest_name: form.guest_name.value,
            department: $('#department').val(),
            employee_type: $('#employee_type').val(),
            test: $('#test').val()
        };

        if (!validateForm(formData)) {
            return;
        }

        fetch('/mhc/events/store-events', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
      .then(res => {
        console.log('Response:', res);
   if (res.result) {
        form.reset();

        // Reset select2 fields if present
        if (window.$ && $.fn.select2) {
            $('#department').val('').trigger('change');
            $('#employee_type').val('').trigger('change');
            $('#test').val('').trigger('change');
        }

        // Show toast success message using SweetAlert2 toast
      toastr.success('Event created successfully!');
        // Redirect after a short delay
        setTimeout(() => {
           // window.location.href = 'https://login-users.hygeiaes.com/mhc/events/list-events';
        }, 2100); // Wait until the toast finishes
    } else if (res.errors) {
        Object.keys(res.errors).forEach(field => {
            if (document.getElementById(field + '_error')) {
                showError(field, res.errors[field][0]);
            }
        });
    } else {
        Swal.fire('Error', 'An error occurred. Please try again.', 'error');
    }
})
  .catch(() => {
            Swal.fire('Error', 'An error occurred. Please try again.', 'error');
        });
    });
});
    document.addEventListener('DOMContentLoaded', function () {
        if (window.flatpickr) {
            flatpickr('.flatpickr-date-time', {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
            });
        }
        if (window.$ && $.fn.select2) {
            $('.select2').select2();
        }
        // Department
        // Department (multiselect)
        const departmentSelect = document.getElementById('department');
        departmentSelect.setAttribute('multiple', 'multiple');
        apiRequest({
            url: 'https://login-users.hygeiaes.com/corporate/getDepartments',
            method: 'GET',
            dataType: 'json',
            onSuccess: function (data) {
            if (data.result && Array.isArray(data.data)) {
                departmentSelect.innerHTML = '';
                data.data.forEach(dept => {
                const option = document.createElement('option');
                option.value = dept.hl1_id;
                option.textContent = dept.hl1_name;
                departmentSelect.appendChild(option);
                });
                if (window.$ && $.fn.select2) {
                $('#department').select2('destroy');
                $('#department').select2({
                    width: '100%',
                    placeholder: 'Select Department',
                    allowClear: true
                });
                $('#department').val(null).trigger('change');
                }
            }
            },
            onError: function (error) {
            // Optionally handle error
            }
        });

        // Employee Type (multiselect)
        const employeeTypeSelect = document.getElementById('employee_type');
        employeeTypeSelect.setAttribute('multiple', 'multiple');
        apiRequest({
            url: 'https://login-users.hygeiaes.com/corporate/getEmployeeType',
            method: 'GET',
            dataType: 'json',
            onSuccess: function (data) {
            if (data.result && Array.isArray(data.data)) {
                employeeTypeSelect.innerHTML = '';
                data.data.forEach(type => {
                const option = document.createElement('option');
                option.value = type.employee_type_id;
                option.textContent = type.employee_type_name;
                employeeTypeSelect.appendChild(option);
                });
                if (window.$ && $.fn.select2) {
                $('#employee_type').select2('destroy');
                $('#employee_type').select2({
                    width: '100%',
                    placeholder: 'Select Employee Type',
                    allowClear: true
                });
                $('#employee_type').val(null).trigger('change');
                }
            }
            },
            onError: function (error) {
            // Optionally handle error
            }
        });
        // Test (multiselect)
        const testSelect = document.getElementById('test');
        testSelect.setAttribute('multiple', 'multiple');
        apiRequest({
            url: 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllSubGroup',
            method: 'GET',
            dataType: 'json',
            onSuccess: function (response) {
                if (response.result && response.data) {
                    const selectElement = document.getElementById('test');
                    // Remove all options before adding new ones
                    selectElement.innerHTML = '';

                    const subgroupsOptgroup = document.createElement('optgroup');
                    subgroupsOptgroup.label = 'Test Groups';
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
                            // Subgroup header
                            const subgroupOption = document.createElement('option');
                            subgroupOption.value = `sg_${subgroup.test_group_id}`;
                            subgroupOption.textContent = `${subgroup.mother_group}: ${subgroup.test_group_name}`;
                            subgroupOption.disabled = true;
                            subgroupsOptgroup.appendChild(subgroupOption);

                            // Subgroup direct tests
                            if (Array.isArray(subgroup.tests) && subgroup.tests.length > 0) {
                                const filteredTests = subgroup.tests.filter(test =>
                                    !testsInSubSubgroups.has(test.master_test_id.toString())
                                );
                                filteredTests.forEach(test => {
                                    const option = document.createElement('option');
                                    option.value = test.master_test_id;
                                    option.textContent = `  — ${test.test_name}`;
                                    subgroupsOptgroup.appendChild(option);
                                });
                            }

                            // Subsubgroups
                            if (Array.isArray(subgroup.subgroups)) {
                                subgroup.subgroups.forEach(subSubgroup => {
                                    if (!Array.isArray(subSubgroup.tests) || subSubgroup.tests.length === 0) {
                                        return;
                                    }
                                    // Subsubgroup header
                                    const subSubgroupOption = document.createElement('option');
                                    subSubgroupOption.value = `ssg_${subSubgroup.test_group_id}`;
                                    subSubgroupOption.textContent = `  — ${subSubgroup.test_group_name}`;
                                    subSubgroupOption.disabled = true;
                                    subgroupsOptgroup.appendChild(subSubgroupOption);

                                    // Subsubgroup tests
                                    subSubgroup.tests.forEach(test => {
                                        const option = document.createElement('option');
                                        option.value = test.master_test_id;
                                        option.textContent = `    — ${test.test_name}`;
                                        subgroupsOptgroup.appendChild(option);
                                    });
                                });
                            }
                        });
                    }

                    if (subgroupsOptgroup.children.length > 0) {
                        selectElement.appendChild(subgroupsOptgroup);
                    }

                    // Individual tests
                    if (Array.isArray(response.data.individual_tests) && response.data.individual_tests.length > 0) {
                        const individualOptgroup = document.createElement('optgroup');
                        individualOptgroup.label = 'Individual Tests';
                        response.data.individual_tests.forEach(test => {
                            const option = document.createElement('option');
                            option.value = test.master_test_id ? test.master_test_id : '';
                            option.textContent = test.test_name ? test.test_name : '';
                            individualOptgroup.appendChild(option);
                        });
                        if (individualOptgroup.children.length > 0) {
                            selectElement.appendChild(individualOptgroup);
                        }
                    }

                    // Re-initialize select2 after updating options
                    if (window.$ && $.fn.select2) {
                        $('#test').select2('destroy');
                        $('#test').select2({
                            width: '100%',
                            placeholder: 'Select Test',
                            allowClear: true
                        });
                        $('#test').val(null).trigger('change');
                    }
                } else {
                    console.warn('Unexpected response structure:', response);
                }
            },
            onError: function (error) {
                // Optionally handle error
                }
            });
        });
        </script>
               

@endsection