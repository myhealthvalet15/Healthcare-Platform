$(document).ready(function () {
    $('#cancelEditBtn').addClass('d-none').hide();
    $('.select2').select2({
        placeholder: 'Select an option',
        allowClear: true
    });
    $('.select2-multi').select2({
        placeholder: 'Select options',
        allowClear: true,
        multiple: true
    });
    $('.flatpickr-date').flatpickr({
        dateFormat: 'Y-m-d',
        allowInput: true
    });
    var allAssignedTemplates = [],
        templateNames = {},
        locationNames = {},
        allEmployeeTypes = [],
        allDepartments = [],
        allDesignations = [];
    loadHRATemplates();
    loadLocations();
    loadEmployeeTypes();
    loadDepartments();
    loadDesignations();
    loadAssignedHRATemplates();
    $('#employeeType,#department,#designation').on('change', function () {
        handleAllToggle(this.id, 'all')
    }).each(function () {
        $(this).data('previousValues', $(this).val() || []);
    });
    $('#saveHRATemplateBtn').on('click', function (e) {
        e.preventDefault();
        $('#editMode').val() === 'true' ? updateHRATemplate() : saveHRATemplate()
    });
    $('#cancelEditBtn').on('click', resetForm);
    function handleAllToggle(selectId, allValue) {
        var $select = $('#' + selectId);
        var selectedValues = $select.val() || [];
        var previousValues = $select.data('previousValues') || [];
        $select.data('previousValues', selectedValues);
        if (selectedValues.includes(allValue) && !previousValues.includes(allValue)) {
            if (selectedValues.length > 1) {
                $select.val([allValue]).trigger('change.select2');
            }
        } else if (selectedValues.includes(allValue) && selectedValues.length > 1) {
            var filteredValues = selectedValues.filter(function (value) {
                return value !== allValue;
            });
            $select.val(filteredValues).trigger('change.select2');
        }
    }
    function loadHRATemplates() {
        apiRequest({
            url: 'https://login-users.hygeiaes.com/mhc/health-risk-assessment/getAllHRATemplates',
            method: 'GET',
            dataType: 'json',
            onSuccess: function (response) {
                if (response.result && response.data) {
                    var options = '<option value="">Select HRA Template</option>';
                    response.data.forEach(function (template) {
                        options += '<option value="' + template.template_id + '">' + template.template_name + '</option>';
                        templateNames[template.template_id] = template.template_name
                    });
                    $('#hraTemplate').html(options)
                } else showToast('warning', 'No HRA templates found')
            },
            onError: function (error) {
                showToast('error', 'Failed to load HRA templates')
            }
        });
    }
    function loadLocations() {
        apiRequest({
            url: 'https://login-users.hygeiaes.com/mhc/health-risk-assessment/getAllLocations',
            method: 'GET',
            dataType: 'json',
            onSuccess: function (response) {
                if (response.result && response.data) {
                    var options = '<option value="">Select Location</option><option value="all">All Locations</option>';
                    response.data.forEach(function (location) {
                        options += '<option value="' + location.location_id + '">' + location.display_name + '</option>';
                        locationNames[location.location_id] = location.display_name
                    });
                    $('#location').html(options)
                } else showToast('warning', 'No locations found')
            },
            onError: function (error) {
                showToast('error', 'Failed to load locations')
            }
        })
    }
    function loadEmployeeTypes() {
        apiRequest({
            url: 'https://login-users.hygeiaes.com/corporate/getEmployeeType',
            method: 'GET',
            dataType: 'json',
            onSuccess: function (response) {
                if (response.result && response.data) {
                    allEmployeeTypes = response.data;
                    var options = '<option value="all">All Employee Types</option>';
                    response.data.forEach(function (empType) {
                        options += '<option value="' + empType.employee_type_id + '">' + empType.employee_type_name + '</option>'
                    });
                    $('#employeeType').html(options).select2('destroy').select2({
                        placeholder: 'Select Employee Types',
                        allowClear: true,
                        multiple: true
                    })
                } else showToast('warning', 'No employee types found')
            },
            onError: function (error) {
                showToast('error', 'Failed to load employee types')
            }
        })
    }
    function loadDepartments() {
        apiRequest({
            url: 'https://login-users.hygeiaes.com/corporate/getDepartments',
            method: 'GET',
            dataType: 'json',
            onSuccess: function (response) {
                if (response.result && response.data) {
                    allDepartments = response.data;
                    var options = '<option value="all">All Departments</option>';
                    response.data.forEach(function (dept) {
                        options += '<option value="' + dept.hl1_id + '">' + dept.hl1_name + '</option>'
                    });
                    $('#department').html(options).select2('destroy').select2({
                        placeholder: 'Select Departments',
                        allowClear: true,
                        multiple: true
                    })
                } else showToast('warning', 'No departments found')
            },
            onError: function (error) {
                showToast('error', 'Failed to load departments')
            }
        })
    }
    function loadDesignations() {
        apiRequest({
            url: 'https://login-users.hygeiaes.com/corporate/getDesignation',
            method: 'GET',
            dataType: 'json',
            onSuccess: function (response) {
                if (response.result && response.data) {
                    allDesignations = response.data;
                    var options = '<option value="all">All Designations</option>';
                    response.data.forEach(function (designation) {
                        options += '<option value="' + designation + '">' + designation + '</option>'
                    });
                    $('#designation').html(options).select2('destroy').select2({
                        placeholder: 'Select Designations',
                        allowClear: true,
                        multiple: true
                    })
                } else showToast('warning', 'No designations found')
            },
            onError: function (error) {
                showToast('error', 'Failed to load designations')
            }
        })
    }
    function loadAssignedHRATemplates() {
        apiRequest({
            url: 'https://login-users.hygeiaes.com/mhc/health-risk-assessment/getAllAssignedHraTemplates',
            method: 'GET',
            dataType: 'json',
            onSuccess: function (response) {
                if (response.result && response.data) {
                    if (response.data.length > 0) {
                        allAssignedTemplates = response.data;
                        populateTemplatesTable(response.data);
                        showToast('success', response.data.length + ' assigned templates loaded successfully');
                    } else {
                        $('#hraTemplatesTableBody').html('<tr><td colspan="8" class="text-center py-4"><em>No assigned templates found</em></td></tr>');
                        showToast('info', 'No assigned templates are currently available');
                    }
                } else if (response.result === false) {
                    $('#hraTemplatesTableBody').html('<tr><td colspan="8" class="text-center py-4"><em>Failed to retrieve assigned templates</em></td></tr>');
                    showToast('warning', 'Failed to retrieve assigned templates');
                } else {
                    $('#hraTemplatesTableBody').html('<tr><td colspan="8" class="text-center py-4"><em>No assigned templates found</em></td></tr>');
                    showToast('warning', 'No assigned templates found');
                }
            },
            onError: function (error) {
                $('#hraTemplatesTableBody').html('<tr><td colspan="8" class="text-center py-4 text-danger"><em>Failed to load templates</em></td></tr>');
                showToast('error', 'Failed to load assigned templates');
            }
        });
    }
    function getLocationDisplayName(locationId) {
        return locationId === 'all' ? 'All Locations' : locationNames[locationId] || locationId
    }
    function createClickableBadge(items, type) {
        if (!items || items.length === 0) return '<span class="text-muted">-</span>';
        var commaSeparatedText = items.join(', '),
            truncatedText = commaSeparatedText.length > 30 ? commaSeparatedText.substring(0, 30) + '...' : commaSeparatedText;
        return '<span class="clickable-menu-badge" data-type="' + type + '" data-items="' + items.join('|||') + '" title="Click to view all ' + type.toLowerCase() + '"><span class="menu-icon"><span class="line"></span><span class="line"></span><span class="line"></span></span><span class="menu-text">' + truncatedText + '</span></span>'
    }
    function populateTemplatesTable(data) {
        var tableBody = $('#hraTemplatesTableBody'),
            rows = '';
        data.forEach(function (template, index) {
            var templateName = templateNames[template.template_id] || 'Health Risk Assessment - Generic',
                fromDate = formatDate(template.from_date),
                toDate = formatDate(template.to_date),
                locationDisplay = getLocationDisplayName(template.location);
            rows += '<tr><td>' + (index + 1) + '</td><td>' + templateName + '</td><td>' + locationDisplay + '</td><td>' + createClickableBadge(template.employee_type, 'Employee Types') + '</td><td>' + createClickableBadge(template.department, 'Departments') + '</td><td>' + createClickableBadge(template.designation, 'Designations') + '</td><td>' + fromDate + '<br>' + toDate + '</td><td><button class="btn btn-sm btn-outline-primary edit-btn" data-index="' + index + '"><i class="ti ti-edit"></i></button></td></tr>'
        });
        tableBody.html(rows);
        $('.clickable-menu-badge').on('click', function () {
            showMultiValueModal($(this).data('type'), $(this).data('items').split('|||'))
        });
        $('.edit-btn').on('click', function () {
            editTemplate($(this).data('index'))
        })
    }
    function editTemplate(index) {
        var template = allAssignedTemplates[index];
        if (!template) {
            showToast('error', 'Template data not found');
            return
        }
        $('#editMode').val('true');
        $('#editIndex').val(index);
        $('#formTitle').text('Edit HRA Template');
        $('#btnText').text('Update Template');
        $('#saveHRATemplateBtn').find('i').removeClass('ti-device-floppy').addClass('ti-refresh');
        $('#cancelEditBtn').removeClass('d-none').show();
        $('#hraTemplate').val(template.template_id).trigger('change').prop('disabled', true).next('.select2-container').addClass('disabled');
        $('#location').val(template.location).trigger('change');
        $('#fromDate').val(formatDateForInput(template.from_date));
        $('#toDate').val(formatDateForInput(template.to_date));
        setTimeout(function () {
            if (template.employee_type && template.employee_type.length > 0) {
                var employeeTypeNames = template.employee_type,
                    allEmpTypeNames = allEmployeeTypes.map(et => et.employee_type_name);
                if (employeeTypeNames.length === allEmpTypeNames.length && employeeTypeNames.every(name => allEmpTypeNames.includes(name))) {
                    $('#employeeType').val(['all']).trigger('change');
                } else {
                    var employeeTypeIds = getEmployeeTypeIds(employeeTypeNames);
                    $('#employeeType').val(employeeTypeIds).trigger('change')
                }
            }
        }, 500);
        setTimeout(function () {
            if (template.department && template.department.length > 0) {
                var departmentNames = template.department,
                    allDeptNames = allDepartments.map(d => d.hl1_name);
                if (departmentNames.length === allDeptNames.length && departmentNames.every(name => allDeptNames.includes(name))) {
                    $('#department').val(['all']).trigger('change');
                } else {
                    var departmentIds = getDepartmentIds(departmentNames);
                    $('#department').val(departmentIds).trigger('change')
                }
            }
        }, 600);
        setTimeout(function () {
            if (template.designation && template.designation.length > 0) {
                if (template.designation.length === allDesignations.length && template.designation.every(name => allDesignations.includes(name))) {
                    $('#designation').val(['all']).trigger('change');
                } else {
                    $('#designation').val(template.designation).trigger('change')
                }
            }
        }, 700);
        $('html, body').animate({
            scrollTop: $('#hraTemplateForm').offset().top - 100
        }, 500)
    }
    function getEmployeeTypeIds(employeeTypeNames) {
        var ids = [];
        employeeTypeNames.forEach(function (name) {
            var empType = allEmployeeTypes.find(et => et.employee_type_name === name);
            if (empType) ids.push(empType.employee_type_id)
        });
        return ids
    }
    function getDepartmentIds(departmentNames) {
        var ids = [];
        departmentNames.forEach(function (name) {
            var dept = allDepartments.find(d => d.hl1_name === name);
            if (dept) ids.push(dept.hl1_id)
        });
        return ids
    }
    function resetForm() {
        $('#editMode').val('false');
        $('#editIndex').val('');
        $('#formTitle').text('Add HRA Template');
        $('#btnText').text('Save HRA Template');
        $('#saveHRATemplateBtn').find('i').removeClass('ti-refresh').addClass('ti-device-floppy');
        $('#cancelEditBtn').addClass('d-none').hide();
        $('#hraTemplate').prop('disabled', false).next('.select2-container').removeClass('disabled');
        $('#hraTemplateForm')[0].reset();
        $('.select2').val(null).trigger('change');
    }
    function showMultiValueModal(type, items) {
        $('#multiValueModalLabel').text(type);
        var content = '<div class="list-group list-group-flush">';
        items.forEach(function (item, index) {
            content += '<div class="list-group-item border-0 px-0 py-2">' + (index + 1) + '. ' + item.trim() + '</div>';
        });
        content += '</div>';
        $('#multiValueContent').html(content);
        $('#multiValueModal').modal('show');
    }
    function formatDate(dateString) {
        return dateString ? new Date(dateString).toLocaleDateString('en-GB') : ''
    }
    function formatDateForInput(dateString) {
        return dateString ? new Date(dateString).toISOString().split('T')[0] : ''
    }
    function validateForm() {
        var isValid = true,
            errors = [];
        if (!$('#hraTemplate').val()) {
            isValid = false;
            errors.push('HRA Template is required')
        }
        if (!$('#location').val()) {
            isValid = false;
            errors.push('Location is required')
        }
        var employeeTypeValues = $('#employeeType').val();
        if (!employeeTypeValues || employeeTypeValues.length === 0) {
            isValid = false;
            errors.push('Employee Type is required')
        }
        var departmentValues = $('#department').val();
        if (!departmentValues || departmentValues.length === 0) {
            isValid = false;
            errors.push('Department is required')
        }
        if (!$('#fromDate').val()) {
            isValid = false;
            errors.push('From Date is required')
        }
        if (!$('#toDate').val()) {
            isValid = false;
            errors.push('To Date is required')
        }
        if ($('#fromDate').val() && $('#toDate').val()) {
            var fromDate = new Date($('#fromDate').val()),
                toDate = new Date($('#toDate').val());
            if (fromDate >= toDate) {
                isValid = false;
                errors.push('From Date must be before To Date')
            }
        }
        if (!isValid) errors.forEach(function (error, index) {
            setTimeout(function () {
                showToast('error', error)
            }, index * 500)
        });
        return isValid
    }
    function updateHRATemplate() {
        if (!validateForm()) return;
        var editIndex = parseInt($('#editIndex').val()),
            templateToUpdate = allAssignedTemplates[editIndex];
        if (!templateToUpdate) {
            showToast('error', 'Template data not found for update');
            return
        }
        var employeeTypeValues = $('#employeeType').val(),
            departmentValues = $('#department').val(),
            designationValues = $('#designation').val();
        var formData = {
            id: templateToUpdate.id,
            hra_template_id: $('#hraTemplate').val(),
            location_id: $('#location').val(),
            employee_type_id: employeeTypeValues,
            department_id: departmentValues,
            designation: designationValues && designationValues.length > 0 ? designationValues : null,
            from_date: $('#fromDate').val(),
            to_date: $('#toDate').val()
        };
        Swal.fire({
            title: 'Update HRA Template?',
            text: 'Are you sure you want to update this HRA template configuration?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Updating...',
                    text: 'Please wait while we update your HRA template',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
                apiRequest({
                    url: '/mhc/health-risk-assessment/updateAssignedHraTemplate',
                    method: 'PUT',
                    data: formData,
                    dataType: 'json',
                    onSuccess: function (response) {
                        Swal.close();
                        if (response.result) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'HRA Template updated successfully!',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                resetForm();
                                loadAssignedHRATemplates()
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'Failed to update HRA Template',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            })
                        }
                    },
                    onError: function (error) {
                        Swal.close();
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to update HRA Template',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        })
                    }
                })
            }
        })
    }
    function saveHRATemplate() {
        if (!validateForm()) return;
        var employeeTypeValues = $('#employeeType').val(),
            departmentValues = $('#department').val(),
            designationValues = $('#designation').val();
        var formData = {
            hra_template_id: $('#hraTemplate').val(),
            location_id: $('#location').val(),
            employee_type_id: employeeTypeValues,
            department_id: departmentValues,
            designation: designationValues && designationValues.length > 0 ? designationValues : null,
            from_date: $('#fromDate').val(),
            to_date: $('#toDate').val()
        };
        Swal.fire({
            title: 'Save HRA Template?',
            text: 'Are you sure you want to save this HRA template configuration?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, save it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Saving...',
                    text: 'Please wait while we save your HRA template',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
                apiRequest({
                    url: '/mhc/health-risk-assessment/assignHraTemplate',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    onSuccess: function (response) {
                        Swal.close();
                        if (response.result) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'HRA Template saved successfully!',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                $('#hraTemplateForm')[0].reset();
                                $('.select2').val(null).trigger('change');
                                $('#cancelEditBtn').addClass('d-none').hide();
                                $('#formTitle').text('Add HRA Template');
                                $('#btnText').text('Save HRA Template');
                                $('#saveHRATemplateBtn').find('i').removeClass('ti-refresh').addClass('ti-device-floppy');
                                loadAssignedHRATemplates();
                                showToast('info', 'Form has been reset')
                            });
                        } else {
                            showToast('error', response.message || 'Failed to save HRA template')
                        }
                    },
                    onError: function (error) {
                        Swal.close();
                        showToast('error', 'Failed to save HRA template. Please try again.')
                    }
                })
            }
        })
    }
});
