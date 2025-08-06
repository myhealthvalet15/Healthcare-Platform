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
