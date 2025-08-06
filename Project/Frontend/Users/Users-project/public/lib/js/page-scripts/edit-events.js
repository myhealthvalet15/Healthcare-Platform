document.addEventListener('DOMContentLoaded', function () {
    const loaded = {
        department: false,
        employee_type: false,
        test: false
    };

    // 1. Patch apiRequest to flag when dropdowns are loaded
    const originalApiRequest = window.apiRequest;
    window.apiRequest = function (options) {
        const key =
            options.url.includes('getDepartments') ? 'department' :
                options.url.includes('getEmployeeType') ? 'employee_type' :
                    options.url.includes('getAllSubGroup') ? 'test' : null;

        if (key) {
            const originalSuccess = options.onSuccess;
            options.onSuccess = function (data) {
                if (originalSuccess) originalSuccess(data);
                loaded[key] = true;
            };
        }

        return originalApiRequest(options);
    };

    // 2. Load test dropdown options
    const loadTestsDropdown = () => {
        const testSelect = document.getElementById('test');
        testSelect.setAttribute('multiple', 'multiple');

        apiRequest({
            url: 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllSubGroup',
            method: 'GET',
            dataType: 'json',
            onSuccess: function (response) {
                if (!response.result || !response.data) return;

                const selectElement = document.getElementById('test');
                selectElement.innerHTML = '';
                const subgroupsOptgroup = document.createElement('optgroup');
                subgroupsOptgroup.label = 'Test Groups';
                const testsInSubSubgroups = new Set();

                // Gather nested test IDs
                response.data.subgroups?.forEach(sg => {
                    sg.subgroups?.forEach(ssg => {
                        ssg.tests?.forEach(test => testsInSubSubgroups.add(test.master_test_id.toString()));
                    });
                });

                response.data.subgroups?.forEach(sg => {
                    const sgOption = document.createElement('option');
                    sgOption.value = `sg_${sg.test_group_id}`;
                    sgOption.textContent = `${sg.mother_group}: ${sg.test_group_name}`;
                    sgOption.disabled = true;
                    subgroupsOptgroup.appendChild(sgOption);

                    sg.tests?.filter(t => !testsInSubSubgroups.has(t.master_test_id.toString()))
                        .forEach(test => {
                            const option = document.createElement('option');
                            option.value = test.master_test_id;
                            option.textContent = `  — ${test.test_name}`;
                            subgroupsOptgroup.appendChild(option);
                        });

                    sg.subgroups?.forEach(ssg => {
                        if (!ssg.tests?.length) return;
                        const ssgOption = document.createElement('option');
                        ssgOption.value = `ssg_${ssg.test_group_id}`;
                        ssgOption.textContent = `  — ${ssg.test_group_name}`;
                        ssgOption.disabled = true;
                        subgroupsOptgroup.appendChild(ssgOption);

                        ssg.tests.forEach(test => {
                            const option = document.createElement('option');
                            option.value = test.master_test_id;
                            option.textContent = `    — ${test.test_name}`;
                            subgroupsOptgroup.appendChild(option);
                        });
                    });
                });

                if (subgroupsOptgroup.children.length) selectElement.appendChild(subgroupsOptgroup);

                if (response.data.individual_tests?.length) {
                    const indivOpt = document.createElement('optgroup');
                    indivOpt.label = 'Individual Tests';
                    response.data.individual_tests.forEach(test => {
                        const option = document.createElement('option');
                        option.value = test.master_test_id || '';
                        option.textContent = test.test_name || '';
                        indivOpt.appendChild(option);
                    });
                    selectElement.appendChild(indivOpt);
                }

                if (window.$ && $.fn.select2) {
                    $('#test').select2('destroy').select2({
                        width: '100%',
                        placeholder: 'Select Test',
                        allowClear: true
                    });
                }
            }
        });
    };
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
    const prefillDepartments = (departments = []) => {
        const departmentIds = departments.map(dep => dep.hl1_id);
        $('#department').val(departmentIds).trigger('change');
    };
    const prefillEmployeeTypes = (employeeTypes = []) => {
        const employeeTypeIds = employeeTypes.map(emp => emp.employee_type_id);
        $('#employee_type').val(employeeTypeIds).trigger('change');
    };
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
    // 3. Wait until all dropdowns are loaded
    const waitUntilLoaded = () => {
        return new Promise(resolve => {
            const check = setInterval(() => {
                if (loaded.department && loaded.employee_type && loaded.test) {
                    clearInterval(check);
                    resolve();
                }
            }, 100);
        });
    };


    // 4. Prefill the form with event data
    const prefillForm = async (data) => {
        const event = data.event || {};
        const departments = data.departments || [];
        const employeeTypes = data.employeeTypes || [];
        const tests = data.tests || [];

        // Set basic fields
        document.getElementById('event_name').value = event.event_name || '';
        document.getElementById('from_date').value = event.from_datetime || '';
        document.getElementById('to_date').value = event.to_datetime || '';
        document.getElementById('event_description').value = event.event_description || '';
        document.getElementById('guest_name').value = event.guest_name || '';

        await waitUntilLoaded();

        // Extract IDs and set dropdown values
        const departmentIds = departments.map(dep => dep.hl1_id);
        const employeeTypeIds = employeeTypes.map(emp => emp.employee_type_id);
        const testIds = tests.map(test => test.master_test_id);

        $('#department').val(departmentIds).trigger('change');
        $('#employee_type').val(employeeTypeIds).trigger('change');
        $('#test').val(testIds).trigger('change');
    };

    // 5. Initialize dropdown loaders
    loadTestsDropdown();
    // Load your department and employee type dropdowns similarly here

    // 6. Fetch event data
    fetch(`/mhc/events/modify-events/${EVENT_ID}`)
        .then(res => res.json())
        .then(res => {
            if (res.result && res.data) {
                const data = res.data;
                // Fill basic fields
                document.getElementById('event_name').value = data.event?.event_name || '';
                document.getElementById('from_date').value = data.event?.from_datetime || '';
                document.getElementById('to_date').value = data.event?.to_datetime || '';
                document.getElementById('event_description').value = data.event?.event_description || '';
                document.getElementById('guest_name').value = data.event?.guest_name || '';

                waitUntilLoaded().then(() => {
                    prefillForm(data);
                    // other dropdowns will be handled separately
                });
            } else {
                Swal.fire('Error', 'Unable to fetch event data', 'error');
            }
        })
        .catch(() => {
            Swal.fire('Error', 'An error occurred while fetching event data', 'error');
        });
});
document.getElementById('eventForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = {
        event_name: document.getElementById('event_name').value,
        from_date: document.getElementById('from_date').value,
        to_date: document.getElementById('to_date').value,
        event_description: document.getElementById('event_description').value,
        guest_name: document.getElementById('guest_name').value,
        department: $('#department').val() || [],
        employee_type: $('#employee_type').val() || [],
        test: $('#test').val() || []
    };

    // Optional: log what you're submitting
    console.log('Submitting:', formData);

    fetch(`/mhc/events/update-events/${EVENT_ID}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify(formData)
    })
        .then(response => response.json())
        .then(res => {
            if (res.result) {
                Swal.fire('Success', 'Event updated successfully!', 'success');
                // Optionally redirect:
                // window.location.href = '/mhc/events';
            } else {
                Swal.fire('Error', res.message || 'Update failed.', 'error');
            }
        })
        .catch(() => {
            Swal.fire('Error', 'Something went wrong while submitting the form.', 'error');
        });
});
