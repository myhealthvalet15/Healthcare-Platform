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

