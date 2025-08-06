document.addEventListener('DOMContentLoaded', function (e) {
    'use strict';
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
        const searchValue = $('#searchEmployees').val().trim();
        if (searchValue === '') {
            showToast("error", "Please enter a search term")
            return;
        }
        if (searchValue.length < 3) {
            showToast("error", "Please enter at least 3 characters for search")
            return;
        }
        $('#searchSpinner').show();
        $('#resultsCard').hide();
        const spinnerStartTime = Date.now();
        const minSpinnerTime = 1500;
        fetch(`https://login-users.hygeiaes.com/ohc/health-registry/add-registry/search/${searchValue}`)
            .then(response => response.json())
            .then(data => {
                employeeTable.clear();
                const elapsedTime = Date.now() - spinnerStartTime;
                const remainingTime = Math.max(0, minSpinnerTime - elapsedTime);
                setTimeout(() => {
                    $('#searchSpinner').hide();
                    if (data.result && data.message && data.message.length > 0) {
                        data.message.forEach(employee => {
                            const fullName = `${employee.first_name} ${employee.last_name}`;
                            const initials = getInitials(fullName);
                            employeeTable.row.add([
                                createEmployeeIdCell(employee.employee_id, initials),
                                createEmployeeNameCell(employee.first_name, employee.last_name, employee.designation),
                                employee.hl1_name || 'N/A',
                                createContactDetailsCell(employee.email, employee.mob_num),
                                employee.employee_type_name || 'N/A',
                                '<button class="btn btn-primary btn-sm add-employee" data-employee-id="' + employee.employee_id + '">Add</button>'
                            ]);
                        });
                        employeeTable.draw();
                        $('#resultsCard').show();
                        showToast("success", `Data retrieved successfully. Found ${data.message.length} employee(s).`);
                    } else {
                        $('#resultsCard').show();
                        employeeTable.clear().draw();
                        showToast("info", "No employees found matching your search criteria.");
                    }
                }, remainingTime);
            })
            .catch(error => {
                console.error('Error fetching employee data:', error);
                const elapsedTime = Date.now() - spinnerStartTime;
                const remainingTime = Math.max(0, minSpinnerTime - elapsedTime);
                setTimeout(() => {
                    $('#searchSpinner').hide();
                    showToast('error', 'An error occurred while searching for employees. Please try again.');
                }, remainingTime);
            });
    });
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
        const fullName = `${firstName} ${lastName}`;
        const initials = getInitials(fullName);
        return `<div class="d-flex justify-content-start align-items-center user-name">
                    <div class="d-flex flex-column">
                        <span class="emp_name text-truncate">${fullName}</span>
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
        const matches = name.match(/\b\w/g) || [];
        return ((matches.shift() || '') + (matches.pop() || '')).toUpperCase();
    }
    $(document).on('click', '.add-employee', function () {
        const employeeId = $(this).data('employee-id').toString().toLowerCase();
        window.location = '/prescription/add-employee-prescription/' + employeeId;
    });

    $('#searchEmployees').on('keypress', function (e) {
        if (e.which === 13) {
            $('#searchBtn').click();
        }
    });
});
