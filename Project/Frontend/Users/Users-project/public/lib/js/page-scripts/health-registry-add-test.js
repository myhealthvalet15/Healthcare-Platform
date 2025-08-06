document.addEventListener("DOMContentLoaded", function () {
    const now = new Date();
    const offset = now.getTimezoneOffset();
    const localDateTime = new Date(now.getTime() - offset * 60 * 1000)
        .toISOString()
        .slice(0, 16);
    const dateInput = document.getElementById('html5-datetime-local-input');
    dateInput.value = localDateTime;
    dateInput.max = localDateTime;
    const selectElement = document.getElementById('select2Primary_tests');
    const addButton = document.getElementById('addTest');
    const employeeId = employeeData['employee_id'];
    const backToHealthReg = document.getElementById('backToHealthReg');
    const testNamesByID = new Map();
    const addedTestIds = new Map();
    let selectedTestIds = new Set();
    const style = document.createElement('style');
    style.textContent = `
        .select2-results__option--group-header:hover {
            color: white !important;
        }
        .select2-results__options {
            scrollbar-width: auto;
            scrollbar-color: #007bff #f0f0f0;
        }
        .select2-results__options::-webkit-scrollbar {
            width: 12px;
        }
        .select2-results__options::-webkit-scrollbar-track {
            background: #f0f0f0;
        }
        .select2-results__options::-webkit-scrollbar-thumb {
            border-radius: 6px;
            border: 3px solid #f0f0f0;
        }
        .select2-container--open .select2-dropdown {
            max-height: 400px;
            overflow-y: auto;
        }
    `;
    document.head.appendChild(style);
    const pathSegments = window.location.pathname.split('/');
    const employeeIdIndex = pathSegments.indexOf('add-test') + 1;
    const employeeIds = pathSegments[employeeIdIndex];
    let mode = 'op'; // default mode is now 'op'
    let opId = '0'; // default to '0'
    let prescriptionId = null;

    // Determine if it's op mode or prescription mode
    if (pathSegments.length > employeeIdIndex + 1) {
        if (pathSegments[employeeIdIndex + 1] === 'op') {
            mode = 'op';
            opId = pathSegments[employeeIdIndex + 2] || '0';
        } else if (pathSegments[employeeIdIndex + 1] === 'prescription') {
            mode = 'prescription';
            prescriptionId = pathSegments[employeeIdIndex + 2];
        }
    }
    if (mode === 'prescription' && backToHealthReg) {
        backToHealthReg.style.display = 'none';
    }
    apiRequest({
        url: 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllSubGroup',
        method: 'GET',
        onSuccess: function (response) {
            if (response.result && response.data) {
                const selectElement = document.getElementById('select2Primary_tests');
                const subgroupsOptgroup = document.createElement('optgroup');
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
                        addedTestIds.clear();
                        const subgroupOption = document.createElement('option');
                        subgroupOption.value = `sg_${subgroup.test_group_id}`;
                        subgroupOption.textContent = `${subgroup.mother_group}: ${subgroup.test_group_name}`;
                        subgroupOption.classList.add('group-header');
                        subgroupsOptgroup.appendChild(subgroupOption);
                        const subgroupTestIds = [];
                        if (Array.isArray(subgroup.tests) && subgroup.tests.length > 0) {
                            const filteredTests = subgroup.tests.filter(test =>
                                !testsInSubSubgroups.has(test.master_test_id.toString())
                            );
                            filteredTests.forEach(test => {
                                const testId = test.master_test_id.toString();
                                subgroupTestIds.push(testId);
                                testNamesByID.set(testId, test.test_name);
                                addedTestIds.set(testId, 'direct');
                                const option = document.createElement('option');
                                option.value = testId;
                                option.textContent = `  — ${test.test_name}`;
                                option.dataset.parentGroup = subgroup.test_group_id;
                                option.dataset.originalGroup = 'subgroup';
                                subgroupsOptgroup.appendChild(option);
                            });
                        }
                        subgroupOption.dataset.testIds = JSON.stringify(subgroupTestIds);
                        if (Array.isArray(subgroup.subgroups)) {
                            subgroup.subgroups.forEach(subSubgroup => {
                                if (!Array.isArray(subSubgroup.tests) || subSubgroup.tests.length === 0) {
                                    return;
                                }
                                const subSubgroupOption = document.createElement('option');
                                subSubgroupOption.value = `ssg_${subSubgroup.test_group_id}`;
                                subSubgroupOption.textContent = `  — ${subSubgroup.test_group_name}`;
                                subSubgroupOption.classList.add('group-header');
                                subSubgroupOption.dataset.parentGroup = subgroup.test_group_id;
                                subgroupsOptgroup.appendChild(subSubgroupOption);
                                const subSubgroupTestIds = [];
                                subSubgroup.tests.forEach(test => {
                                    const testId = test.master_test_id.toString();
                                    subSubgroupTestIds.push(testId);
                                    testNamesByID.set(testId, test.test_name);
                                    const option = document.createElement('option');
                                    option.value = testId;
                                    option.textContent = `    — ${test.test_name}`;
                                    option.dataset.parentGroup = subSubgroup.test_group_id;
                                    option.dataset.originalGroup = 'subsubgroup';
                                    subgroupsOptgroup.appendChild(option);
                                    addedTestIds.set(testId, 'subsubgroup');
                                });
                                subSubgroupOption.dataset.testIds = JSON.stringify(subSubgroupTestIds);
                                subgroupTestIds.push(...subSubgroupTestIds);
                            });
                        }
                        subgroupOption.dataset.testIds = JSON.stringify([...new Set(subgroupTestIds)]);
                    });
                }
                if (subgroupsOptgroup.children.length > 0) {
                    selectElement.appendChild(subgroupsOptgroup);
                }
                if (Array.isArray(response.data.individual_tests) && response.data.individual_tests.length > 0) {
                    const individualOptgroup = document.createElement('optgroup');
                    individualOptgroup.label = 'Individual Tests';
                    response.data.individual_tests.forEach(test => {
                        const testId = test.master_test_id.toString();
                        testNamesByID.set(testId, test.test_name);
                        const option = document.createElement('option');
                        option.value = testId;
                        option.textContent = test.test_name;
                        option.dataset.originalGroup = 'individual';
                        individualOptgroup.appendChild(option);
                    });
                    if (individualOptgroup.children.length > 0) {
                        selectElement.appendChild(individualOptgroup);
                    }
                }
                if (window.jQuery && window.jQuery().select2) {
                    $('#select2Primary_tests').select2({
                        templateResult: formatOption,
                        templateSelection: formatSelection,
                        width: '100%',
                        dropdownCssClass: 'select2-dropdown-improved',
                    });
                    $('#select2Primary_tests').on('select2:select', function (e) {
                        const id = e.params.data.id;
                        if (id.startsWith('sg_') || id.startsWith('ssg_')) {
                            const groupOption = $(this).find(`option[value="${id}"]`)[0];
                            try {
                                const testIds = JSON.parse(groupOption.dataset.testIds || '[]');
                                testIds.forEach(testId => {
                                    selectedTestIds.add(testId);
                                });
                                updateSelectedTests();
                                $(this).find(`option[value="${id}"]`).prop('selected', false);
                                $(this).trigger('change');
                            } catch (error) {
                                console.error('Error parsing test IDs:', error);
                            }
                        }
                        else {
                            selectedTestIds.add(id);
                            updateSelectedTests();
                        }
                    });
                    $('#select2Primary_tests').on('select2:unselect', function (e) {
                        const id = e.params.data.id;
                        if (!id.startsWith('sg_') && !id.startsWith('ssg_')) {
                            selectedTestIds.delete(id);
                            updateSelectedTests();
                        }
                    });
                    function updateSelectedTests() {
                        $(selectElement).find('option').prop('selected', false);
                        Array.from(selectedTestIds).forEach(testId => {
                            const option = $(selectElement).find(`option[value="${testId}"]`).first();
                            if (option.length) {
                                option.prop('selected', true);
                            }
                        });
                        $(selectElement).trigger('change');
                    }
                }
            } else {
                console.warn('Unexpected response structure:', response);
            }
        },
        onError: function (error) {
        }
    });
    function formatOption(option) {
        if (!option.id) {
            return option.text;
        }
        if (option.id.startsWith('sg_') || option.id.startsWith('ssg_')) {
            return $('<span class="select2-results__option--group-header">' + option.text + ' [Select All]</span>');
        }
        const $option = $(option.element);
        if ($option.data('originalGroup') === 'subsubgroup') {
            return $('<span style="padding-left: 36px;">' + option.text + '</span>');
        } else if ($option.data('originalGroup') === 'subgroup') {
            return $('<span style="padding-left: 12px;">' + option.text + '</span>');
        }
        return $('<span>' + option.text + '</span>');
    }
    function formatSelection(option) {
        if (!option.id) {
            return option.text;
        }
        if (option.id.startsWith('sg_') || option.id.startsWith('ssg_')) {
            return '';
        }
        return testNamesByID.get(option.id) || option.text.replace(/^[\s—]+/, '');
    }
    if (employeeIds === null || !/^[a-zA-Z0-9]+$/.test(employeeIds)) {
        showToast("error", "Invalid Employee ID");
        return;
    }
    let apiUrl = 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllMasterTests/' + employeeIds;
    if (mode === 'op') {
        apiUrl += '/op/' + opId;
    } else if (mode === 'prescription') {
        apiUrl += '/prescription/' + prescriptionId;
    }
    apiRequest({
        url: apiUrl,
        method: 'GET',
        onSuccess: function (response) {
            if (response.result && Array.isArray(response.data)) {
                const preselectedTestIds = response.data.map(String);
                selectedTestIds = new Set(preselectedTestIds);
                const interval = setInterval(() => {
                    if ($(selectElement).find('option').length > 0) {
                        $(selectElement).find('option').prop('selected', false);
                        Array.from(selectedTestIds).forEach(testId => {
                            if (testId.startsWith('sg_') || testId.startsWith('ssg_')) {
                                return;
                            }
                            const option = $(selectElement).find(`option[value="${testId}"]`).first();
                            if (option.length) {
                                option.prop('selected', true);
                            }
                        });
                        $(selectElement).trigger('change');
                        clearInterval(interval);
                    }
                }, 100);
            } else if (response.message === "Employee ID does not exist") {
                showToast("warning", "Employee ID not found.");
            } else if (response.message === "No tests found for this employee") {
                showToast("info", "No existing tests assigned to this employee.");
            } else {
                console.warn('Unexpected response structure:', response);
            }
        },
        onError: function (error) {
        }
    });
    addButton.addEventListener('click', function () {
        const testIds = Array.from(selectedTestIds).filter(id =>
            !id.startsWith('sg_') && !id.startsWith('ssg_')
        );
        if (testIds.length === 0) {
            showToast("info", "Please select at least one test.");
            return;
        }
        const dateTimeInput = document.getElementById('html5-datetime-local-input');
        const selectedDateTime = dateTimeInput.value;
        let apiUrl;
        if (mode === 'prescription') {
            apiUrl = `/ohc/health-registry/add-test/${employeeIds}/prescription/${prescriptionId}`;
        } else {
            apiUrl = `/ohc/health-registry/add-test/${employeeIds}/op/${opId}`;
        }
        Swal.fire({
            title: 'Confirm Add Tests?',
            text: "Do you want to add the selected test(s)?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Yes, add tests',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                apiRequest({
                    url: apiUrl,
                    method: 'POST',
                    data: {
                        test_ids: testIds,
                        selected_datetime: selectedDateTime
                    },
                    onSuccess: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Tests added successfully!',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(() => {
                            if (mode === 'prescription') {
                                window.location.reload();
                            } else if (isOpRegistryIdIsthere == null) {
                                window.location.reload();
                            } else {
                                window.location.href = '/ohc/health-registry/edit-registry/edit-outpatient/' + employeeIds.toString().toLowerCase() + '/op/' + isOpRegistryIdIsthere;
                            }
                        }, 1500);
                    },
                    onError: function (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'Failed to add tests: ' + error
                        });
                    }
                });
            }
        });
    });
    const isOutPatientAddedAndOpen = document.getElementById('isOutPatientAddedAndOpen').value;
    if (isOutPatientAddedAndOpen == 0) {
        $('#select2Primary_tests').prop('disabled', true);
        $('#addTest').prop('disabled', true);
    }
    if (backToHealthReg) {
        backToHealthReg.addEventListener('click', function () {
            Swal.fire({
                title: 'Are you sure?',
                text: "Any unsaved changes will be lost!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, go back',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/ohc/health-registry/edit-registry/edit-outpatient/' + employeeIds.toString().toLowerCase() + '/op/' + isOpRegistryIdIsthere;
                }
            });
        });
    }
});
