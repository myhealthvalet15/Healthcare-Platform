document.addEventListener('DOMContentLoaded', function (e) {
    'use strict';
    var healthRegistryPermission = (typeof ohcRights !== 'undefined' && ohcRights.out_patient !== undefined)
        ? parseInt(ohcRights.out_patient)
        : 1;
    let employeeTable = $('#employeeTable').DataTable({
        responsive: true,
        dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-6 pt-md-0"B>><"row bg-violet text-white py-2 px-3"<"col-12 d-flex justify-content-between align-items-center"<"ml-auto"f><"ms-3"l>>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: {
            paginate: {
                next: '<i class="ti ti-chevron-right ti-sm"></i>',
                previous: '<i class="ti ti-chevron-left ti-sm"></i>'
            }
        },
        buttons: [],
        initComplete: function (settings, json) {
            $('.card-header').after('<hr class="my-0">');
            $('.row.bg-violet').css({
                'background-color': '#6B1BC7',
                'position': 'relative',
                'color': 'white',
                'padding': '20px 15px',
                'display': 'flex',
                'justify-content': 'space-between',
                'align-items': 'center',
                'min-height': '70px'
            });
            $('.row.bg-violet .col-12').prepend('<span class="sample-text" style="color: #fff">&nbsp;&nbsp;&nbsp;&nbsp;Employee Search Results</span>');
            $('.dataTables_filter').css({
                'margin': '0',
                'text-align': 'left',
                'float': 'left'
            });
            $('.dataTables_length').css({
                'margin': '0',
                'text-align': 'right',
                'float': 'right',
                'width': 'auto',
                'display': 'inline-flex',
                'justify-content': 'flex-end',
                'align-items': 'center',
                'position': 'relative',
                'right': '0'
            });
            $('.sample-text').css({
                'margin-right': '20px',
                'font-weight': 'bold'
            });
            $('.dataTables_filter input').css({
                'background-color': 'rgba(255, 255, 255, 0.9)',
                'border-color': '#6B1BC7',
                'border-width': '1px',
                'border-radius': '4px',
                'height': '38px',
                'padding': '5px 10px'
            }).on('focus', function () {
                $(this).css({
                    'border-color': '#6B1BC7',
                    'box-shadow': '0 0 5px rgba(107, 27, 199, 0.5)'
                });
            }).on('blur', function () {
                $(this).css({
                    'border-color': '#6B1BC7',
                    'box-shadow': 'none'
                });
            });
            $('.dataTables_length select').css({
                'background-color': 'rgba(255, 255, 255, 0.9)',
                'border-color': 'transparent',
                'border-radius': '4px',
                'color': '#333',
                'height': '38px'
            });
            $('.dataTables_length label, .dataTables_filter label').css('color', 'white');
            $('.dataTables_filter').hide();
        }
    });
    $('#searchBtn').on('click', function () {
        performSearch();
    });
    $('#searchEmployees').on('keypress', function (e) {
        if (e.which === 13) {
            performSearch();
        }
    });
    function performSearch() {
        const searchValue = $('#searchEmployees').val().trim();
        if (searchValue === '') {
            showToast("error", "Please enter a search term");
            return;
        }
        if (searchValue.length < 3) {
            showToast("error", "Please enter at least 3 characters for search");
            return;
        }
        $('#searchSpinner').show();
        $('#resultsCard').hide();
        const spinnerStartTime = Date.now();
        const minSpinnerTime = 1500;
        employeeTable.clear().draw();
        fetch(`https://login-users.hygeiaes.com/ohc/health-registry/add-registry/search/${encodeURIComponent(searchValue)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    throw new Error("Response is not JSON");
                }
                return response.json();
            })
            .then(data => {
                const elapsedTime = Date.now() - spinnerStartTime;
                const remainingTime = Math.max(0, minSpinnerTime - elapsedTime);
                setTimeout(() => {
                    $('#searchSpinner').hide();
                    handleSearchResponse(data);
                }, remainingTime);
            })
            .catch(error => {
                console.error('Error fetching employee data:', error);
                const elapsedTime = Date.now() - spinnerStartTime;
                const remainingTime = Math.max(0, minSpinnerTime - elapsedTime);
                setTimeout(() => {
                    $('#searchSpinner').hide();
                    handleSearchError(error);
                }, remainingTime);
            });
    }
    function handleSearchResponse(data) {
        try {
            if (!data.hasOwnProperty('result')) {
                throw new Error("Invalid response format");
            }
            if (!data.result) {
                $('#resultsCard').show();
                showToast("error", data.message || "Search failed");
                return;
            }
            if (data.result === true) {
                if (Array.isArray(data.message)) {
                    if (data.message.length > 0) {
                        populateEmployeeTable(data.message);
                        $('#resultsCard').show();
                        showToast("success", `Data retrieved successfully. Found ${data.message.length} employee(s).`);
                    } else {
                        $('#resultsCard').show();
                        showToast("info", "No employees found matching your search criteria.");
                    }
                }
                else if (typeof data.message === 'string') {
                    $('#resultsCard').show();
                    if (data.message.toLowerCase().includes('no matching') ||
                        data.message.toLowerCase().includes('not found') ||
                        data.message.toLowerCase().includes('no data') ||
                        data.message.toLowerCase().includes('no employee')) {
                        showToast("info", data.message);
                    } else {
                        showToast("warning", data.message);
                    }
                }
                else {
                    $('#resultsCard').show();
                    showToast("warning", "Unexpected response format. Please try again.");
                }
            }
        } catch (error) {
            console.error('Error handling search response:', error);
            $('#resultsCard').show();
            showToast("error", "Error processing search results. Please try again.");
        }
    }
    function handleSearchError(error) {
        $('#resultsCard').show();
        if (error.message.includes('Failed to fetch')) {
            showToast('error', 'Network error. Please check your internet connection and try again.');
        } else if (error.message.includes('HTTP error')) {
            showToast('error', 'Server error. Please try again later.');
        } else if (error.message.includes('not JSON')) {
            showToast('error', 'Invalid server response. Please try again.');
        } else {
            showToast('error', 'An error occurred while searching for employees. Please try again.');
        }
    }
    function populateEmployeeTable(employees) {
        employeeTable.clear();
        employees.forEach(employee => {
            try {
                const fullName = `${employee.first_name || ''} ${employee.last_name || ''}`.trim();
                const initials = getInitials(fullName);
                const addButton = (healthRegistryPermission === 2)
                    ? `<button class="btn btn-primary btn-sm add-employee" data-employee-id="${employee.employee_id || ''}">Add</button>`
                    : '<span class="text-muted">No Permission</span>';
                employeeTable.row.add([
                    createEmployeeIdCell(employee.employee_id || 'N/A', initials),
                    createEmployeeNameCell(employee.first_name || '', employee.last_name || '', employee.designation || ''),
                    employee.hl1_name || 'N/A',
                    createContactDetailsCell(employee.email || '', employee.mob_num || ''),
                    employee.employee_type_name || 'N/A',
                    addButton
                ]);
            } catch (error) {
                console.error('Error adding employee row:', error, employee);
            }
        });
        employeeTable.draw();
    }
    function createEmployeeIdCell(userId, initials) {
        const stateNum = Math.floor(Math.random() * 6);
        const states = ['success', 'danger', 'warning', 'info', 'primary', 'secondary'];
        const state = states[stateNum];
        return `<div class="d-flex justify-content-start align-items-center">
                    <div class="avatar-wrapper">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded-circle bg-label-${state}">${initials}</span>
                        </div>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="emp_id">${userId}</span>
                    </div>
                </div>`;
    }
    function createEmployeeNameCell(firstName, lastName, designation) {
        const fullName = `${firstName} ${lastName}`.trim();
        return `<div class="d-flex justify-content-start align-items-center user-name">
                    <div class="d-flex flex-column">
                        <span class="emp_name text-truncate">${fullName || 'N/A'}</span>
                        <small class="text-muted">${designation || 'N/A'}</small>
                    </div>
                </div>`;
    }
    function createContactDetailsCell(email, mobile) {
        return `<div class="d-flex flex-column">
                    <span><i class="ti ti-mail me-1"></i>${email || 'N/A'}</span>
                    <span><i class="ti ti-phone me-1"></i>${mobile || 'N/A'}</span>
                </div>`;
    }
    function getInitials(name) {
        if (!name || name.trim() === '') {
            return 'NA';
        }
        const matches = name.match(/\b\w/g) || [];
        return ((matches.shift() || '') + (matches.pop() || '')).toUpperCase();
    }
    $(document).on('click', '.add-employee', function () {
        const employeeId = $(this).data('employee-id');
        if (!employeeId) {
            showToast("error", "Employee ID not found");
            return;
        }
        const employeeIdString = employeeId.toString().toLowerCase();
        window.location = '/ohc/add-test/' + employeeIdString;
    });
});
