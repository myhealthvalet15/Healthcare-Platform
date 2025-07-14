@extends('layouts/layoutMaster')
@section('title', 'Assign Healthplans')
<!-- Vendor Styles -->
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
<!-- Vendor Scripts -->
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
<!-- Page Scripts -->
@section('page-script')
@vite(['resources/assets/js/form-layouts.js',
'resources/assets/js/extended-ui-sweetalert2.js'])
@endsection
@section('content')
<div class="row mb-12">
    <div class="col-md">
        <div class="card">
            <h5 class="card-header">Assign Healthplan</h5>
            <div class="col" id="assign-healthplan-spinner"
                style="display: flex; justify-content: center; align-items: center; height: 100vh; flex-direction: column;">
                <div class="sk-bounce sk-primary">
                    <div class="sk-bounce-dot"></div>
                    <div class="sk-bounce-dot"></div>
                </div>
                <p class="loading-text" style="margin-top: 10px;">Loading
                    Data...</p>
            </div>
            <div class="card-body" id="assign-healthplan-element" style="display: none;">
                <form class="needs-validation" novalidate>
                    <div class="row g-6">
                        <div class="col-md-4">
                            <label for="employeeTypeSelect2" class="form-label">Employee Type</label>
                            <div class="select2-primary">
                                <select id="employeeTypeSelect2" class="select2 form-select" multiple>
                                    <!-- Employee types will be populated here -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="departmentSelect2" class="form-label">Department</label>
                            <div class="select2-primary">
                                <select id="departmentSelect2" class="select2 form-select" multiple>
                                    <!-- Departments will be populated here -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="designationSelect2" class="form-label">Designation</label>
                            <div class="select2-primary">
                                <select id="designationSelect2" class="select2 form-select" multiple>
                                    <!-- Designations will be populated here -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label for="employeesSelect2" class="form-label">Employees</label>
                            <div class="select2-primary">
                                <select id="employeesSelect2" class="select2 form-select" multiple>
                                    <!-- Employees will be populated here -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-light fw-medium d-block">&nbsp;</small>
                            <div class="form-check form-check-primary mt-4">
                                <input name="customCheckPrimary" class="form-check-input" type="checkbox" value
                                    id="customCheckPrimary" />
                                <label class="form-check-label" for="customCheckPrimary">
                                    All Employees
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="bs-validation-healthplan">Healthplans</label>
                            <select class="form-select" id="bs-validation-healthplan" required>
                                <option value>Select Healthplans</option>
                                <!-- Healthplans will be populated here -->
                            </select>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please select your
                                healthplan</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="bs-validation-assign-date">Assign
                                Date</label>
                            <input type="text" class="form-control flatpickr-validation" id="bs-validation-assign-date"
                                placeholder="DD-MM-YYYY" required />
                            <div class="valid-feedback"> Looks good! </div>
                            <div class="invalid-feedback"> Please Enter Assign
                                Date </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="bs-validation-due-date">Due Date</label>
                            <input type="text" class="form-control flatpickr-validation" id="bs-validation-due-date"
                                placeholder="DD-MM-YYYY" required />
                            <div class="valid-feedback"> Looks good! </div>
                            <div class="invalid-feedback"> Please Enter Due Date
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="bs-validation-doctor">Doctor</label>
                            <select class="form-select" id="bs-validation-doctor">
                                <option value>Select Doctor</option>
                                <option value="usa">USA</option>
                                <option value="uk">UK</option>
                                <option value="france">France</option>
                                <option value="australia">Australia</option>
                                <option value="spain">Spain</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="bs-validation-favourite">Favourite</label>
                            <select class="form-select" id="bs-validation-favourite">
                                <option value>Select favourite</option>
                                <option value="usa">USA</option>
                                <option value="uk">UK</option>
                                <option value="france">France</option>
                                <option value="australia">Australia</option>
                                <option value="spain">Spain</option>
                            </select>
                        </div>
                    </div>
                    <!-- Added spacing container for the submit button -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        'use strict';
        let filtersApplied = false;
        let filteredEmployeeIds = [];
        $('.select2').each(function () {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>');
            $this.select2({
                placeholder: 'Select options',
                dropdownParent: $this.parent()
            });
        });
        $('#employeeTypeSelect2').select2({
            placeholder: 'Select Employee Type',
            dropdownParent: $('#employeeTypeSelect2').parent()
        });
        $('#departmentSelect2').select2({
            placeholder: 'Select Department',
            dropdownParent: $('#departmentSelect2').parent()
        });
        $('#designationSelect2').select2({
            placeholder: 'Select Designation',
            dropdownParent: $('#designationSelect2').parent()
        });
        $('#employeesSelect2').select2({
            placeholder: 'Select Employees',
            dropdownParent: $('#employeesSelect2').parent()
        });
        const flatPickrList = [].slice.call(document.querySelectorAll('.flatpickr-validation'));
        if (flatPickrList) {
            flatPickrList.forEach(flatPickr => {
                flatPickr.flatpickr({
                    allowInput: true,
                    monthSelectorType: 'static',
                    dateFormat: 'd-m-Y'
                });
            });
        }
        let apiRequests = [];
        apiRequests.push(new Promise((resolve, reject) => {
            apiRequest({
                url: 'https://login-users.hygeiaes.com/corporate/getEmployeeType',
                onSuccess: function (response) {
                    if (response.result && Array.isArray(response.data)) {
                        let employeeTypeSelect = $('#employeeTypeSelect2');
                        employeeTypeSelect.empty();
                        response.data.forEach(function (type) {
                            employeeTypeSelect.append(`<option value="${type.employee_type_id}">${type.employee_type_name}</option>`);
                        });
                        employeeTypeSelect.trigger('change');
                    } else {
                        showToast('info', 'Notice', response.data || 'No employee types found.');
                    }
                    resolve();
                },
                onError: function (error) {
                    console.error('Error fetching employee types:', error);
                    showToast('error', 'Error', 'Failed to load employee types');
                    reject(error);
                }
            });
        }));
        apiRequests.push(new Promise((resolve, reject) => {
            apiRequest({
                url: 'https://login-users.hygeiaes.com/corporate/getDepartments',
                onSuccess: function (response) {
                    if (response.result && Array.isArray(response.data)) {
                        let departmentSelect = $('#departmentSelect2');
                        departmentSelect.empty();
                        response.data.forEach(function (dept) {
                            departmentSelect.append(`<option value="${dept.hl1_id}">${dept.hl1_name}</option>`);
                        });
                        departmentSelect.trigger('change');
                    } else {
                        showToast('info', 'Notice', response.data || 'No departments found.');
                    }
                    resolve();
                },
                onError: function (error) {
                    console.error('Error fetching departments:', error);
                    showToast('error', 'Error', 'Failed to load departments');
                    reject(error);
                }
            });
        }));
        apiRequests.push(new Promise((resolve, reject) => {
            apiRequest({
                url: 'https://login-users.hygeiaes.com/corporate/getDesignation',
                onSuccess: function (response) {
                    if (response.result && Array.isArray(response.data)) {
                        let designationSelect = $('#designationSelect2');
                        designationSelect.empty();
                        response.data.forEach(function (designation) {
                            designationSelect.append(`<option value="${designation}">${designation}</option>`);
                        });
                        designationSelect.trigger('change');
                    } else {
                        showToast('info', 'Notice', response.data || 'No designations found.');
                    }
                    resolve();
                },
                onError: function (error) {
                    console.error('Error fetching designations:', error);
                    showToast('error', 'Error', 'Failed to load designations');
                    reject(error);
                }
            });
        }));
        apiRequests.push(new Promise((resolve, reject) => {
            apiRequest({
                url: 'https://login-users.hygeiaes.com/corporate/getAllEmployees',
                onSuccess: function (response) {
                    if (response.result && Array.isArray(response.data)) {
                        let employeesSelect = $('#employeesSelect2');
                        employeesSelect.empty();
                        response.data.forEach(function (emp) {
                            let fullName = emp.first_name + ' ' + emp.last_name;
                            employeesSelect.append(`<option value="${emp.user_id}">${emp.employee_id} - ${fullName}</option>`);
                        });
                        employeesSelect.trigger('change');
                    } else {
                        showToast('info', 'Notice', response.data || 'No employees found.');
                    }
                    resolve();
                },
                onError: function (error) {
                    console.error('Error fetching employees:', error);
                    showToast('error', 'Error', 'Failed to load employees');
                    reject(error);
                }
            });
        }));
        apiRequests.push(new Promise((resolve, reject) => {
            apiRequest({
                url: 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllHealthplans',
                onSuccess: function (response) {
                    if (response.result && Array.isArray(response.message)) {
                        let healthplanSelect = $('#bs-validation-healthplan');
                        healthplanSelect.empty();
                        healthplanSelect.append('<option value>Select Healthplans</option>');
                        response.message.forEach(function (plan) {
                            healthplanSelect.append(`<option value="${plan.corporate_healthplan_id}">${plan.healthplan_title}</option>`);
                        });
                    } else {
                        showToast('info', 'Notice', 'No health plans found.');
                    }
                    resolve();
                },
                onError: function (error) {
                    console.error('Error fetching healthplans:', error);
                    showToast('error', 'Error', 'Failed to load healthplans');
                    reject(error);
                }
            });
        }));
        apiRequests.push(new Promise((resolve, reject) => {
            apiRequest({
                url: 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getDoctors',
                onSuccess: function (response) {
                    if (response.result && Array.isArray(response.data)) {
                        let doctor = $('#bs-validation-doctor');
                        doctor.find('option:not(:first)').remove();
                        response.data.forEach(function (doctorData) {
                            doctor.append(`<option value="${doctorData.doctor_id}">${doctorData.doctor_name}</option>`);
                        });
                    } else {
                        showToast('info', 'Notice', 'No doctors found.');
                    }
                    resolve();
                },
                onError: function (error) {
                    console.error('Error fetching doctors:', error);
                    showToast('error', 'Error', 'Failed to load doctors');
                    reject(error);
                }
            });
        }));
        apiRequests.push(new Promise((resolve, reject) => {
            apiRequest({
                url: 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getFavourite',
                onSuccess: function (response) {
                    if (response.result && Array.isArray(response.data)) {
                        let favouriteSelect = $('#bs-validation-favourite');
                        favouriteSelect.find('option:not(:first)').remove();
                        response.data.forEach(function (item) {
                            favouriteSelect.append(`<option value="${item.favourite_id}">${item.favourite_name}</option>`);
                        });
                    } else {
                        showToast('info', 'Notice', 'No favourites found.');
                    }
                    resolve();
                },
                onError: function (error) {
                    console.error('Error fetching favourites:', error);
                    showToast('error', 'Error', 'Failed to load favourites');
                    reject(error);
                }
            });
        }));
        Promise.all(apiRequests)
            .then(() => {
                document.getElementById("assign-healthplan-spinner").style.display = 'none';
                document.getElementById("assign-healthplan-element").style.display = 'block';
                let initialSetup = true;
                $('#employeeTypeSelect2, #departmentSelect2, #designationSelect2').on('change', function () {
                    if (initialSetup) return;
                    const selectedEmployeeTypes = $('#employeeTypeSelect2').val() || [];
                    const selectedDepartments = $('#departmentSelect2').val() || [];
                    const selectedDesignations = $('#designationSelect2').val() || [];
                    filtersApplied = (selectedEmployeeTypes.length > 0 ||
                        selectedDepartments.length > 0 ||
                        selectedDesignations.length > 0);
                    if (filtersApplied) {
                        $('#employeesSelect2').prop('disabled', true);
                        apiRequest({
                            url: '/corporate/getAllEmployeesFilters',
                            method: 'POST',
                            data: {
                                employee_type_id: selectedEmployeeTypes,
                                department: selectedDepartments,
                                designation: selectedDesignations
                            },
                            onSuccess: function (response) {
                                let employeesSelect = $('#employeesSelect2');
                                employeesSelect.empty().trigger('change');
                                filteredEmployeeIds = [];
                                if (response.result && Array.isArray(response.data) && response.data.length > 0) {
                                    filteredEmployeeIds = response.data.map(emp => emp.user_id);
                                    response.data.forEach(function (emp) {
                                        let fullName = emp.first_name + ' ' + emp.last_name;
                                        employeesSelect.append(`<option value="${emp.user_id}">${emp.employee_id} - ${fullName}</option>`);
                                    });
                                    showToast('success', 'Filter Applied', 'Employee list filtered successfully');
                                } else {
                                    showToast('warning', 'No Results', 'No employees found with the selected filters');
                                }
                                employeesSelect.prop('disabled', false);
                                employeesSelect.trigger('change');
                            },
                            onError: function (error) {
                                console.error('Error fetching filtered employees:', error);
                                $('#employeesSelect2').prop('disabled', false);
                                showToast('error', 'Error', 'Failed to filter employees');
                            }
                        });
                    } else {
                        filtersApplied = false;
                        filteredEmployeeIds = [];
                        loadAllEmployees();
                    }
                });
                initialSetup = false;
            })
            .catch(() => {
                console.warn('Requests failed.');
            });
        function loadAllEmployees() {
            apiRequest({
                url: 'https://login-users.hygeiaes.com/corporate/getAllEmployees',
                onSuccess: function (response) {
                    let employeesSelect = $('#employeesSelect2');
                    employeesSelect.empty().trigger('change');
                    if (response.result && Array.isArray(response.data) && response.data.length > 0) {
                        response.data.forEach(function (emp) {
                            let fullName = emp.first_name + ' ' + emp.last_name;
                            employeesSelect.append(`<option value="${emp.user_id}">${emp.employee_id} - ${fullName}</option>`);
                        });
                    } else {
                        showToast('info', 'Notice', response.message || 'No employees found.');
                    }
                    employeesSelect.prop('disabled', false);
                },
                onError: function (error) {
                    console.error('Error fetching employees:', error);
                    showToast('error', 'Error', 'Failed to load employees');
                    $('#employeesSelect2').prop('disabled', false);
                }
            });
        }
        const bsValidationForms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(bsValidationForms).forEach(function (form) {
            form.addEventListener(
                'submit',
                function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    const allEmployeesChecked = $('#customCheckPrimary').prop('checked');
                    const selectedEmployees = $('#employeesSelect2').val() || [];
                    const employeesSelected = allEmployeesChecked || selectedEmployees.length > 0;
                    if (!employeesSelected) {
                        if (!$('#employees-validation-feedback').length) {
                            $('#employeesSelect2')
                                .parent()
                                .append(
                                    '<div id="employees-validation-feedback" class="invalid-feedback d-block">Please select at least one employee or check "All Employees"</div>'
                                );
                        }
                    } else {
                        $('#employees-validation-feedback').remove();
                    }
                    if (!form.checkValidity() || !employeesSelected) {
                        form.classList.add('was-validated');
                        return;
                    }
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Do you want to assign this health plan?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, assign it!',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let userIds;
                            if (allEmployeesChecked) {
                                userIds = filtersApplied ? filteredEmployeeIds : ['all'];
                            } else {
                                userIds = selectedEmployees;
                            }
                            const formData = {
                                user_ids: userIds,
                                healthplan_id: $('#bs-validation-healthplan').val(),
                                assign_date: $('#bs-validation-assign-date').val(),
                                due_date: $('#bs-validation-due-date').val(),
                                doctor_id: $('#bs-validation-doctor').val() || null,
                                favourite_id: $('#bs-validation-favourite').val() || null,
                            };
                            apiRequest({
                                url: '/corporate/assignHealthPlan',
                                method: 'POST',
                                data: formData,
                                onSuccess: function (response) {
                                    showToast('success', 'Success', 'Health plan assigned successfully!');
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 2000);
                                },
                                onError: function (error) {
                                    showToast('error', 'Error', error || 'Failed to assign health plan');
                                },
                            });
                        }
                    });
                },
                false
            );
        });
        $('#customCheckPrimary').on('change', function () {
            let employeesSelect = $('#employeesSelect2');
            const isChecked = $(this).prop('checked');
            if (isChecked) {
                employeesSelect.prop('disabled', true);
                employeesSelect.val(null).trigger('change');
            } else {
                employeesSelect.prop('disabled', false);
                employeesSelect.trigger('change');
            }
        });
        window.Helpers.initCustomOptionCheck();
    });
</script>
@endsection