@extends('layouts/layoutMaster')
@section('title', 'Assign Healthplan List')
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
<style>
    .certification-badge {
        font-size: 0.75rem !important;
        padding: 0.25rem 0.5rem !important;
        border-radius: 0.375rem !important;
        font-weight: 500 !important;
        transition: all 0.2s ease-in-out;
        border: 1px solid rgba(0, 0, 0, 0.1);
        text-decoration: none !important;
        cursor: pointer;
        background-color: #f8f9fa !important;
        color: #495057 !important;
    }

    .certification-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        opacity: 0.9;
        text-decoration: none !important;
        background-color: #e9ecef !important;
    }

    .certification-badge:active {
        transform: translateY(0);
    }

    .certification-badge.certified {
        color: white !important;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .modal-header {
        border-bottom: 1px solid #dee2e6;
        padding-top: 1rem;
        padding-bottom: 1rem;
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .modal-footer {
        border-top: 1px solid #dee2e6;
        padding-top: 1rem;
        padding-bottom: 1rem;
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .readonly-field {
        background-color: #f8f9fa;
        border-color: #e9ecef;
    }

    .badge-preview {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
        margin-top: 0.5rem;
    }

    .date-group {
        margin-bottom: 0.5rem;
    }

    .date-group:last-child {
        margin-bottom: 0;
    }

    .date-label {
        font-size: 0.75rem;
        color: #6c757d;
        margin-bottom: 0.125rem;
        font-weight: 500;
    }

    .date-value {
        font-size: 0.875rem;
        color: #495057;
    }

    .date-with-icon {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .calendar-icon {
        cursor: pointer;
        color: #6c757d;
        font-size: 0.875rem;
        padding: 0.25rem;
        border-radius: 0.25rem;
        transition: all 0.2s ease;
        border: 1px solid #dee2e6;
        background-color: #f8f9fa;
    }

    .calendar-icon:hover {
        color: #495057;
        background-color: #e9ecef;
        transform: scale(1.1);
    }

    .dates-modal .date-item {
        padding: 0.75rem;
        border: 1px solid #e9ecef;
        border-radius: 0.375rem;
        margin-bottom: 0.5rem;
        background-color: #f8f9fa;
        transition: all 0.2s ease;
    }

    .dates-modal .date-item:hover {
        background-color: #e9ecef;
        border-color: #adb5bd;
    }

    .dates-modal .date-item:last-child {
        margin-bottom: 0;
    }

    .date-item-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .date-item-value {
        font-size: 0.875rem;
        font-weight: 500;
        color: #495057;
    }

    .dates-modal-body {
        max-height: 400px;
        overflow-y: auto;
    }

    .modal-backdrop.dates-modal-backdrop {
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        background-color: rgba(0, 0, 0, 0.6) !important;
    }

    .dates-modal .modal-dialog {
        backdrop-filter: none;
    }

    .dates-modal .modal-content {
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        border: none;
    }

    .dates-modal .modal-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #dee2e6;
        border-radius: 12px 12px 0 0;
        padding: 1.25rem 1.5rem;
    }

    .dates-modal .modal-title {
        font-weight: 600;
        color: #495057;
        font-size: 1.125rem;
    }

    .dates-modal-body {
        padding: 1.5rem;
        max-height: 400px;
        overflow-y: auto;
        background-color: #fff;
    }

    .date-entry {
        margin-bottom: 0.25rem;
        padding: 0;
        border: none;
        background: none;
    }

    .date-entry:last-child {
        margin-bottom: 0;
    }

    .date-entry-content {
        font-size: 0.95rem;
        line-height: 1.4;
        color: #495057;
    }

    .date-label-bold {
        font-weight: 700;
        color: #212529;
    }

    .date-label-normal {
        font-weight: 400;
        color: #495057;
    }

    .date-value {
        color: #6c757d;
        font-weight: 500;
    }

    .dates-modal .modal-footer {
        border-top: 1px solid #dee2e6;
        padding: 1rem 1.5rem;
        background-color: #f8f9fa;
        border-radius: 0 0 12px 12px;
    }

    .square {
        width: 50px;
        height: 50px;
    }
</style>
<style>
    #advancedFiltersRow {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        transform-origin: top;
    }

    #advancedFiltersRow.sliding-down {
        animation: slideDown 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    #advancedFiltersRow.sliding-up {
        animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            max-height: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            max-height: 200px;
            transform: translateY(0);
        }
    }

    @keyframes slideUp {
        from {
            opacity: 1;
            max-height: 200px;
            transform: translateY(0);
        }

        to {
            opacity: 0;
            max-height: 0;
            transform: translateY(-10px);
        }
    }

    #toggleAdvancedFilters {
        transition: all 0.3s ease;
        border-radius: 6px;
        font-weight: 500;
        padding: 0.5rem 1rem;
    }

    #toggleAdvancedFilters:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.2);
    }

    #toggleIcon {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    #toggleIcon.rotated {
        transform: rotate(180deg);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        transition: all 0.15s ease-in-out;
    }

    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #495057;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        #advancedFiltersRow {
            margin-top: 1rem;
        }

        @keyframes slideDown {
            to {
                max-height: 400px;
            }
        }

        @keyframes slideUp {
            from {
                max-height: 400px;
            }
        }
    }
</style>
@endsection
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Assign Healthplan List</h5>
        <div class="d-flex gap-2">
            <button type="button" id="toggleAdvancedFilters" class="btn btn-danger btn-sm">
                <i class="fas fa-chevron-down me-1" id="toggleIcon"></i>
                <span id="toggleText">Advanced Filters</span>
            </button>
            <button type="button" id="applyFilters" class="btn btn-sm btn-primary">Apply Filters</button>
            <button type="button" id="clearFilters" class="btn btn-sm btn-outline-secondary">Clear</button>
        </div>
    </div>
    <div class="card-body border-bottom">
        <div class="row g-3" id="mainFilters">
            <div class="col-md-6 col-lg-3">
                <label class="form-label" for="filterHealthplan">Healthplan</label>
                <select id="filterHealthplan" class="form-select">
                    <option value>Select Healthplan</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-3">
                <label class="form-label" for="filterStatus">Status</label>
                <select id="filterStatus" class="form-select">
                    <option value>Select Status</option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label for="filterFromDate" class="form-label">
                    From Date <span class="text-danger">*</span>
                </label>
                <input type="text" id="filterFromDate" class="form-control flatpickr-date"
                    placeholder="Select from date">
            </div>
            <div class="col-md-3 mb-3">
                <label for="filterToDate" class="form-label">
                    To Date <span class="text-danger">*</span>
                </label>
                <input type="text" id="filterToDate" class="form-control flatpickr-date" placeholder="Select to date">
            </div>
        </div>
        <div class="row g-3 mt-2" id="advancedFiltersRow" style="display: none; overflow: hidden;">
            <div class="col-md-6 col-lg-3">
                <label class="form-label" for="filterEmployeeType">Employee
                    Type</label>
                <select id="filterEmployeeType" class="form-select">
                    <option value>Select Employee Type</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-3">
                <label class="form-label" for="filterName">Name / Employee
                    ID</label>
                <input type="text" id="filterName" class="form-control" placeholder="Search by name or employee ID">
            </div>
            <div class="col-md-6 col-lg-3">
                <label class="form-label" for="filterDesignation">Designation</label>
                <select id="filterDesignation" class="form-select">
                    <option value>Select Designation</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-3">
                <label class="form-label" for="filterDepartment">Department</label>
                <select id="filterDepartment" class="form-select">
                    <option value>Select Department</option>
                </select>
            </div>
        </div>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Assigned Date</th>
                    <th>Name - ID / Designation</th>
                    <th>Department / Location</th>
                    <th>Healthplan <br> Doctor - Diagnostic Center</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="healthPlanTableBody">
                <tr>
                    <td colspan="5" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="datesModal" tabindex="-1" aria-labelledby="datesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content dates-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="datesModalLabel">
                    <i class="fas fa-calendar-alt me-2"></i>All Dates for <span id="employeeName"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body dates-modal-body" id="datesModalBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="certificationModal" tabindex="-1" aria-labelledby="certificationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="certificationModalLabel">
                    <span id="modalMode">Certification</span>: <span id="certificationTitle"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="certifiedInfo" class="alert alert-success" style="display: none;">
                    <strong>Certified On:</strong> <span id="certifiedOn"></span>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="conditionSelect" class="form-label">Condition <span
                                class="text-danger">*</span></label>
                        <select id="conditionSelect" class="form-select">
                            <option value>Select Condition</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="issueDateInput" class="form-label">Issue
                            Date <span class="text-danger">*</span></label>
                        <input type="date" id="issueDateInput" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="nextAssessmentInput" class="form-label">Next
                            Assessment Date <span class="text-danger">*</span></label>
                        <input type="date" id="nextAssessmentInput" class="form-control">
                    </div>
                    <div class="col-12">
                        <label for="remarksInput" class="form-label">Remarks</label>
                        <textarea id="remarksInput" class="form-control" rows="3"
                            placeholder="Enter any remarks or notes..."></textarea>
                    </div>
                    <div class="col-12" id="badgePreview" style="display: none;">
                        <label class="form-label">Badge Preview:</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveCertification">Save Certification</button>
            </div>
        </div>
    </div>
</div>
<script>
    let allHealthPlansData = [];
    let filteredData = [];
    let currentCertificationData = null;
    let colorData = [];
    const certificationBadgeCSS = `
            <style>
            .certification-badge {
                display: inline-block;
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
                font-weight: 500;
                line-height: 1.2;
                border-radius: 0.375rem;
                margin: 0.125rem;
                text-decoration: none;
                transition: all 0.15s ease-in-out;
                white-space: nowrap;
            }
            .certification-badge:hover:not([style*="opacity: 0.6"]) {
                transform: translateY(-1px);
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .certification-badge[style*="opacity: 0.6"] {
                cursor: not-allowed !important;
            }
            </style>
            `;
    const additionalModalCSS = `
                <style>
                    #certificationViewContent .alert {
                        border-left: 4px solid #6c757d;
                        background-color: #f8f9fa;
                    }
                    #certificationViewContent .badge {
                        font-size: 0.875rem !important;
                        padding: 0.375rem 0.75rem !important;
                        border-radius: 0.375rem !important;
                    }
                    #certificationViewContent .bg-light {
                        background-color: #f8f9fa !important;
                        border: 1px solid #dee2e6 !important;
                        border-radius: 0.375rem !important;
                        padding: 0.75rem !important;
                        font-style: italic;
                        color: #495057 !important;
                    }
                    .modal-content {
                        border-radius: 0.5rem;
                    }
                    .modal-header {
                        background-color: #f8f9fa;
                        border-bottom: 1px solid #dee2e6;
                    }
                    .modal-title {
                        color: #495057;
                        font-weight: 600;
                    }
                    #certificationViewContent .text-dark {
                        color: #495057 !important;
                    }
                </style>
            `;
    function bindFilterEvents() {
        $('#clearFilters').on('click', function () {
            $('#filterHealthplan').val('').trigger('change');
            $('#filterStatus').val('').trigger('change');
            $('#filterFromDate').val('');
            $('#filterToDate').val('');
            $('#filterEmployeeType').val('').trigger('change');
            $('#filterName').val('');
            $('#filterDesignation').val('').trigger('change');
            $('#filterDepartment').val('').trigger('change');
            if ($('#filterFromDate')[0]._flatpickr) {
                $('#filterFromDate')[0]._flatpickr.clear();
            }
            if ($('#filterToDate')[0]._flatpickr) {
                $('#filterToDate')[0]._flatpickr.clear();
            }
            filteredData = [...allHealthPlansData];
            populateHealthPlanTable(filteredData);
            showToast('info', 'Filters Cleared', 'All filters have been cleared. Showing all records.');
        });
        $('#applyFilters').on('click', applyFilters);
    }
    function bindModalEvents() {
        $('#conditionSelect').on('change', function () {
            updateBadgePreview();
        });
        $('#saveCertification').on('click', saveCertification);
    }
    function loadHealthPlans() {
        $('#healthPlanTableBody').html('<tr><td colspan="5" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
        apiRequest({
            url: 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllAssignHealthPlans',
            method: 'GET',
            dataType: 'json',
            onSuccess: function (response) {
                if (response.result && Array.isArray(response.data)) {
                    allHealthPlansData = response.data;
                    filteredData = [...allHealthPlansData];
                    populateFilterDropdowns();
                    populateHealthPlanTable(filteredData);
                } else {
                    showToast('info', 'Notice', response.message || 'No health plans found.');
                    $('#healthPlanTableBody').html('<tr><td colspan="5" class="text-center text-muted">No health plans found</td></tr>');
                }
            },
            onError: function (error) {
                showToast('error', 'Error', 'Failed to load health plans');
                $('#healthPlanTableBody').html('<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>');
            }
        });
    }
    function populateFilterDropdowns() {
        let employeeTypes = [...new Set(allHealthPlansData.map(item => item.employee_type_name).filter(Boolean))];
        populateDropdown('#filterEmployeeType', employeeTypes, 'Select Employee Type');
        let designations = [...new Set(allHealthPlansData.map(item => item.designation).filter(Boolean))];
        populateDropdown('#filterDesignation', designations, 'Select Designation');
        let departments = [...new Set(allHealthPlansData.map(item => item.hl1_name).filter(Boolean))];
        populateDropdown('#filterDepartment', departments, 'Select Department');
        let healthplans = [...new Set(allHealthPlansData.map(item => item.healthplan_title).filter(Boolean))];
        populateDropdown('#filterHealthplan', healthplans, 'Select Healthplan');
        let predefinedStatuses = ['Pending', 'Schedule', 'In Process', 'Test Completed', 'Result Ready', 'No Show', 'Certified', 'Cancelled'];
        populateDropdown('#filterStatus', predefinedStatuses, 'Select Status');
    }
    function populateDropdown(selector, items, placeholder) {
        let dropdown = $(selector);
        dropdown.empty();
        dropdown.append(`<option value="">${placeholder}</option>`);
        items.forEach(function (item) {
            dropdown.append(`<option value="${item}">${item}</option>`);
        });
    }
    function getItemStatus(item) {
        if (item.cancelled) return 'Cancelled';
        if (item.certified) return 'Certified';
        if (item.no_show) return 'No Show';
        if (item.result_ready) return 'Result Ready';
        if (item.test_completed) return 'Test Completed';
        if (item.in_process) return 'In Process';
        if (item.schedule) return 'Schedule';
        if (item.pending) return 'Pending';
        return 'Pending';
    }
    function openDatesModal(element) {
        let itemId = $(element).data('item-id');
        let currentItem = allHealthPlansData.find(item => item.id == itemId);
        if (!currentItem) return;
        $('#employeeName').text(`${currentItem.first_name} ${currentItem.last_name}`);
        let datesData = [
            {
                label: 'Assigned Date',
                value: formatDateToDDMMYYYY(currentItem.test_date),
                rawValue: currentItem.test_date,
                isBold: true
            },
            {
                label: 'Due Date',
                value: formatDateToDDMMYYYY(currentItem.test_due_date),
                rawValue: currentItem.test_due_date,
                isBold: true
            },
            {
                label: 'Diagnosis Date',
                value: currentItem.in_process ? formatDateToDDMMYYYY(currentItem.in_process) : 'Not Set',
                rawValue: currentItem.in_process,
                isBold: true
            },
            {
                label: 'Assessment Date',
                value: currentItem.result_ready ? formatDateToDDMMYYYY(currentItem.result_ready) : 'Not Set',
                rawValue: currentItem.result_ready,
                isBold: true
            }
        ];
        if (currentItem.certified) {
            datesData.push({
                label: 'Certification Date',
                value: formatDateToDDMMYYYY(currentItem.certified),
                rawValue: currentItem.certified,
                isBold: true
            });
        }
        let modalBody = $('#datesModalBody');
        modalBody.empty();
        datesData.forEach(function (dateItem) {
            let labelClass = dateItem.isBold ? 'date-label-bold' : 'date-label-normal';
            let dateHtml = `
            <div class="date-entry">
                <div class="date-entry-content">
                    <span class="${labelClass}">${dateItem.label}</span> - <span class="date-value">${dateItem.value}</span>
                </div>
            </div>
        `;
            modalBody.append(dateHtml);
        });
        $('#datesModal').modal('show');
        $('#datesModal').on('shown.bs.modal', function () {
            $('.modal-backdrop').addClass('dates-modal-backdrop');
        });
        $('#datesModal').on('hidden.bs.modal', function () {
            $('.modal-backdrop').removeClass('dates-modal-backdrop');
        });
    }
    function applyFilters() {
        let filters = {
            employeeType: $('#filterEmployeeType').val(),
            name: $('#filterName').val().toLowerCase(),
            designation: $('#filterDesignation').val(),
            department: $('#filterDepartment').val(),
            healthplan: $('#filterHealthplan').val(),
            status: $('#filterStatus').val(),
            fromDate: $('#filterFromDate').val(),
            toDate: $('#filterToDate').val()
        };
        filteredData = allHealthPlansData.filter(function (item) {
            if (filters.employeeType && item.employee_type_name !== filters.employeeType) {
                return false;
            }
            if (filters.name) {
                let nameMatch = item.first_name.toLowerCase().includes(filters.name) ||
                    item.last_name.toLowerCase().includes(filters.name) ||
                    item.employee_id.toLowerCase().includes(filters.name);
                if (!nameMatch) return false;
            }
            if (filters.designation && item.designation !== filters.designation) {
                return false;
            }
            if (filters.department && item.hl1_name !== filters.department) {
                return false;
            }
            if (filters.healthplan && item.healthplan_title !== filters.healthplan) {
                return false;
            }
            if (filters.status) {
                let itemStatus = getItemStatus(item);
                if (itemStatus !== filters.status) {
                    return false;
                }
            }
            if (filters.fromDate || filters.toDate) {
                let assignedDate = new Date(item.test_date);
                if (filters.fromDate) {
                    let fromDate = parseDate(filters.fromDate);
                    if (assignedDate < fromDate) return false;
                }
                if (filters.toDate) {
                    let toDate = parseDate(filters.toDate);
                    if (assignedDate > toDate) return false;
                }
            }
            return true;
        });
        populateHealthPlanTable(filteredData);
        showToast('success', 'Filters Applied', `Found ${filteredData.length} records matching your criteria.`);
    }
    function capitalizeWords(str) {
        return str.replace(/\b\w/g, char => char.toUpperCase()).toLowerCase().replace(/\b\w/g, char => char.toUpperCase());
    }
    function populateHealthPlanTable(data) {
        let tableBody = $('#healthPlanTableBody');
        tableBody.empty();
        if (data.length === 0) {
            tableBody.html('<tr><td colspan="5" class="text-center text-muted">No health plans found matching the selected criteria</td></tr>');
            return;
        }
        data.forEach(function (item) {
            let currentStatus = getItemStatus(item);
            let statusBadgeClass = getStatusBadgeClass(currentStatus);
            let formattedTestDate = formatDateToDDMMYYYY(item.test_date);
            let healthplanUrl = `https://login-users.hygeiaes.com/mhc/diagnostic-assessment/health-plan/${item.id}/prescription-test/${item.test_id}/${item.test_code}`;
            let certificationBadges = '';
            if (item.certifications && Array.isArray(item.certifications)) {
                certificationBadges = item.certifications.map(function (cert) {
                    let isResultReady = item.result_ready && item.result_ready !== null;
                    let isMainCertified = item.certified && item.certified !== null;
                    let badgeClass = 'certification-badge';
                    let badgeStyle = '';
                    let clickHandler = '';
                    let cursorStyle = 'cursor: default;';
                    let titleText = '';
                    if (isResultReady) {
                        clickHandler = `onclick="openCertificationModal(this)"`;
                        cursorStyle = 'cursor: pointer;';
                        let hasHealthplanCert = cert.healthplan_certification &&
                            cert.healthplan_certification.condition !== null &&
                            cert.healthplan_certification.condition !== '';
                        if (hasHealthplanCert) {
                            badgeClass += ' certified';
                            let badgeColor = cert.healthplan_certification.color_condition || '#6c757d';
                            badgeStyle = `background-color: ${badgeColor} !important; color: white !important;`;
                            titleText = `Click to view ${capitalizeWords(cert.certification_title)} certification`;
                        } else {
                            badgeStyle = 'background-color: #f8f9fa !important; color: #495057 !important; border: 1px solid #dee2e6 !important;';
                            titleText = `Click to manage ${capitalizeWords(cert.certification_title)} certification`;
                        }
                    } else {
                        clickHandler = '';
                        cursorStyle = 'cursor: default;';
                        badgeStyle = 'background-color: #e9ecef !important; color: #6c757d !important; opacity: 0.6 !important; border: 1px solid #dee2e6 !important;';
                        titleText = `${capitalizeWords(cert.certification_title)} - Available after result is ready`;
                    }
                    return `<span class="${badgeClass}" 
                    style="${badgeStyle} ${cursorStyle}" 
                    data-item-id="${item.id}"
                    data-cert-id="${cert.certificate_id}"
                    data-test-id="${item.test_id}"
                    ${clickHandler}
                    title="${titleText}">
                    ${capitalizeWords(cert.certification_title)}
                </span>`;
                }).join(' ');
            }
            let row = `
                <tr>
                    <td>
                        <div class="date-with-icon">
                            <span class="fw-medium">${formattedTestDate}</span>
                            <i class="fas fa-calendar-alt calendar-icon" 
                            onclick="openDatesModal(this)" 
                            data-item-id="${item.id}"
                            title="View all dates"></i>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <span class="fw-medium">${item.first_name} ${item.last_name}</span>
                            <small class="text-muted">${item.employee_id} - ${item.designation}</small>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <span class="fw-medium">${capitalizeWords(item.hl1_name)}</span>
                            <small class="text-muted">${capitalizeWords(item.display_name)}</small>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column" style="min-width: 200px;">
                            <div class="text-truncate" style="max-width: 200px;" title="${item.healthplan_title || '-'}">
                                <a href="${healthplanUrl}" class="text-primary text-decoration-none fw-medium" 
                                style="cursor: pointer;">
                                    ${item.healthplan_title || '-'}
                                </a>
                            </div>
                            <div class="mb-1" style="max-width: 200px;" title="${item.doctor_name || '-'}-${item.diagnosis_center || '-'}">
                                <span>${item.doctor_name || '-'} - ${item.diagnosis_center || '-'}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column align-items-start" style="max-width: 300px;">
                            <span class="badge ${statusBadgeClass} mb-2">${currentStatus}</span>
                            <div class="d-flex flex-wrap gap-2">
                                ${certificationBadges}
                            </div>
                        </div>
                    </td>
                </tr>
            `;
            tableBody.append(row);
        });
    }
    function parseDate(dateString) {
        let parts = dateString.split('/');
        return new Date(parts[2], parts[1] - 1, parts[0]);
    }
    function clearAllFilters() {
        $('#filterHealthplan').val('').trigger('change');
        $('#filterStatus').val('').trigger('change');
        $('#filterFromDate').val('');
        $('#filterToDate').val('');
        $('#filterEmployeeType').val('').trigger('change');
        $('#filterName').val('');
        $('#filterDesignation').val('').trigger('change');
        $('#filterDepartment').val('').trigger('change');
        if ($('#filterFromDate')[0]._flatpickr) {
            $('#filterFromDate')[0]._flatpickr.clear();
        }
        if ($('#filterToDate')[0]._flatpickr) {
            $('#filterToDate')[0]._flatpickr.clear();
        }
        filteredData = [...allHealthPlansData];
        populateHealthPlanTable(filteredData);
        showToast('info', 'Filters Cleared', 'All filters have been cleared. Showing all records.');
    }
    function checkAdvancedFiltersOnLoad() {
        const advancedFilters = [
            '#filterEmployeeType',
            '#filterName',
            '#filterDesignation',
            '#filterDepartment'
        ];
        let hasAdvancedValues = false;
        advancedFilters.forEach(selector => {
            if ($(selector).val() && $(selector).val().trim() !== '') {
                hasAdvancedValues = true;
            }
        });
        if (hasAdvancedValues && !isAdvancedVisible) {
            $('#toggleAdvancedFilters').trigger('click');
        }
    }
    function openCertificationModal(element) {
        let itemId = $(element).data('item-id');
        let certId = $(element).data('cert-id');
        let testId = $(element).data('test-id');
        let currentItem = allHealthPlansData.find(item => item.id == itemId);
        if (!currentItem) return;
        if (!currentItem.result_ready || currentItem.result_ready === null) {
            showToast('warning', 'Not Available', 'Certification is only available after test results are ready.');
            return;
        }
        let currentCert = currentItem.certifications.find(cert => cert.certificate_id == certId);
        if (!currentCert) return;
        currentCertificationData = {
            item: currentItem,
            certification: currentCert,
            itemId: itemId,
            certId: certId,
            testId: testId
        };
        populateCertificationModal(currentCert);
        $('#certificationModal').modal('show');
    }
    function populateCertificationModal(certification) {
        $('#certificationTitle').text(capitalizeWords(certification.certification_title));
        let currentItem = currentCertificationData.item;
        let isMainCertified = currentItem.certified && currentItem.certified !== null;
        let conditionSelect = $('#conditionSelect');
        let remarksInput = $('#remarksInput');
        let saveCertificationBtn = $('#saveCertification');
        let closeBtnModal = $('.modal-footer .btn-secondary');
        conditionSelect.empty();
        conditionSelect.append('<option value="">Select Condition</option>');
        let conditions = [];
        try {
            conditions = typeof certification.condition === 'string'
                ? JSON.parse(certification.condition)
                : (Array.isArray(certification.condition) ? certification.condition : []);
        } catch (e) {
            console.error('Error parsing conditions:', e);
            conditions = [];
        }
        conditions.forEach(function (condition) {
            conditionSelect.append(`<option value="${condition}">${condition}</option>`);
        });
        let healthplanCert = certification.healthplan_certification;
        if (isMainCertified) {
            $('#modalMode').text('View Certification Details');
            if (healthplanCert && healthplanCert.condition) {
                $('#conditionSelect').closest('.col-md-4').hide();
                $('#issueDateInput').closest('.col-md-4').hide();
                $('#nextAssessmentInput').closest('.col-md-4').hide();
                $('#remarksInput').closest('.col-12').hide();
                $('#certificationViewContent').remove();
                let conditionIndex = conditions.indexOf(healthplanCert.condition);
                let badgeColor = certification.color_condition &&
                    certification.color_condition.length > conditionIndex &&
                    conditionIndex >= 0
                    ? certification.color_condition[conditionIndex].replace(/`/g, '')
                    : '#6c757d';
                const allConditions = colorData.filter(cd =>
                    cd.certification_title.toLowerCase() === certification.certification_title.toLowerCase()
                );
                let allConditionsHtml = '';
                if (allConditions.length > 0) {
                    allConditionsHtml = `
                <div class="mb-3">
                    <strong class="text-dark d-block mb-1">Available Conditions for "${certification.certification_title}" Certificate:</strong>
                    <div class="d-flex flex-wrap align-items-center">
                        ${allConditions.map(cd => `
                            <div class="d-flex align-items-center me-3 mb-2">
                                <span style="width: 12px; height: 12px; background-color: ${cd.color}; display: inline-block; border-radius: 2px; margin-right: 6px;"></span>
                                <span>${cd.condition}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
                }
                let viewContent = `
            <div id="certificationViewContent" class="row g-3">
                <div class="col-12">
                    <div class="alert alert-light border">
                        <h6 class="mb-3 text-dark"><i class="fas fa-certificate me-2"></i>Certification Details</h6>
                        <div class="mb-3 d-flex align-items-center">
                            <strong class="text-dark me-2">Condition:</strong>
                            <span class="me-2" style="width: 12px; height: 12px; background-color: ${badgeColor}; display: inline-block; border-radius: 2px;"></span>
                            <span>${healthplanCert.condition}</span>
                        </div>
                        ${allConditionsHtml}
                        ${healthplanCert.remarks ? `
                            <div class="mb-3">
                                <strong class="text-dark">Remarks:</strong>
                                <div class="mt-1 p-2 bg-light border rounded text-dark">
                                    ${healthplanCert.remarks}
                                </div>
                            </div>
                        ` : ''}
                        <div class="mb-3">
                            <strong class="text-dark">Certified On:</strong>
                            <span class="text-dark fw-medium">${formatDateToDDMMYYYY(currentItem.certified)}</span>
                        </div>
                        ${healthplanCert.next_assessment_date ? `
                            <div class="mb-0">
                                <strong class="text-dark">Next Assessment Date:</strong>
                                <span class="text-dark fw-medium">${formatDateToDDMMYYYY(healthplanCert.next_assessment_date)}</span>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
                $('#badgePreview').before(viewContent);
                $('#certifiedInfo').hide();
                saveCertificationBtn.hide();
                closeBtnModal.hide();
                $('.modal-footer').hide();
            } else {
                $('#certifiedInfo').show();
                $('#certifiedInfo').html(`
            <div class="alert alert-success">
                <strong><em>No specific certification details available for this category.</em></strong>
            </div>
        `);
                $('#conditionSelect').closest('.col-md-4').hide();
                $('#issueDateInput').closest('.col-md-4').hide();
                $('#nextAssessmentInput').closest('.col-md-4').hide();
                $('#remarksInput').closest('.col-12').hide();
                conditionSelect.prop('disabled', true);
                remarksInput.prop('disabled', true);
                saveCertificationBtn.hide();
                closeBtnModal.hide();
                $('.modal-footer').hide();
            }
        }
        else {
            $('#conditionSelect').closest('.col-md-4').show();
            $('#issueDateInput').closest('.col-md-4').show();
            $('#nextAssessmentInput').closest('.col-md-4').show();
            $('#remarksInput').closest('.col-12').show();
            const issueDateTime = healthplanCert?.certified_on;
            const issueDateOnly = issueDateTime ? issueDateTime.split(' ')[0] : '';
            $('#issueDateInput').val(issueDateOnly);
            $('#nextAssessmentInput').val(healthplanCert?.next_assessment_date || '');
            $('#certificationViewContent').remove();
            $('.modal-footer').show();
            closeBtnModal.show();
            let hasExistingData = healthplanCert && (healthplanCert.condition || healthplanCert.remarks);
            if (hasExistingData) {
                $('#modalMode').text('Edit Certification');
                conditionSelect.val(healthplanCert.condition || '');
                remarksInput.val(healthplanCert.remarks || '');
                saveCertificationBtn.text('Update Certification').show();
            } else {
                $('#modalMode').text('Add Certification');
                conditionSelect.val('');
                remarksInput.val('');
                saveCertificationBtn.text('Save Certification').show();
            }
            $('#certifiedInfo').hide();
            conditionSelect.prop('disabled', false);
            remarksInput.prop('disabled', false);
        }
        updateBadgePreview();
    }
    function updateBadgePreview() {
        let selectedCondition = $('#conditionSelect').val();
        let badgePreview = $('#badgePreview');
        if (selectedCondition && currentCertificationData) {
            let cert = currentCertificationData.certification;
            let currentItem = currentCertificationData.item;
            let isMainCertified = currentItem.certified && currentItem.certified !== null;
            let conditions = [];
            try {
                conditions = typeof cert.condition === 'string'
                    ? JSON.parse(cert.condition)
                    : (Array.isArray(cert.condition) ? cert.condition : []);
            } catch (e) {
                conditions = [];
            }
            let colorIndex = conditions.indexOf(selectedCondition);
            let badgeColor = cert.color_condition &&
                cert.color_condition.length > colorIndex &&
                colorIndex >= 0
                ? cert.color_condition[colorIndex]
                : '#6c757d';
            if (!isMainCertified) {
                badgePreview.html(`
                <div class="badge-preview" style="background-color: ${badgeColor}; color: white;">
                    ${capitalizeWords(cert.certification_title)}: ${selectedCondition}
                </div>
            `).show();
            } else {
                badgePreview.html(`
                <div class="badge-preview" style="background-color: ${badgeColor}; color: white;">
                    ${capitalizeWords(cert.certification_title)}: ${selectedCondition}
                </div>
            `).show();
            }
        } else {
            badgePreview.hide();
        }
    }
    function saveCertification() {
        if (!currentCertificationData) return;
        let condition = $('#conditionSelect').val();
        let remarks = $('#remarksInput').val();
        let issueDate = $('#issueDateInput').val();
        let nextAssessmentDate = $('#nextAssessmentInput').val();
        if (!condition) {
            showToast('error', 'Validation Error', 'Please select a condition');
            return;
        }
        if (!issueDate || !nextAssessmentDate) {
            showToast('error', 'Validation Error', 'Please select Issue Date and Next Assessment Date');
            return;
        }
        if (new Date(issueDate) > new Date(nextAssessmentDate)) {
            showToast('error', 'Validation Error', 'Issue Date cannot be after Next Assessment Date');
            return;
        }
        if (new Date(nextAssessmentDate) < new Date()) {
            showToast('error', 'Validation Error', 'Next Assessment Date cannot be in the past');
            return;
        }
        if (remarks && remarks.length > 500) {
            showToast('error', 'Validation Error', 'Remarks cannot exceed 500 characters');
            return;
        }
        $('#saveCertification').prop('disabled', true).text('Saving...');
        let saveData = {
            healthplan_assigned_status_id: currentCertificationData.itemId,
            certificate_id: currentCertificationData.certId,
            test_id: currentCertificationData.testId,
            condition: condition,
            remarks: remarks,
            user_id: currentCertificationData.item.user_id,
            issue_date: issueDate,
            next_assessment_date: nextAssessmentDate
        };
        apiRequest({
            url: '/mhc/diagnostic-assessment/saveCertification',
            method: 'POST',
            data: saveData,
            dataType: 'json',
            onSuccess: function (response) {
                if (response.result) {
                    showToast('success', 'Success', 'Certification saved successfully');
                    $('#certificationModal').modal('hide');
                    loadHealthPlans();
                } else {
                    showToast('error', 'Error', response.message || 'Failed to save certification');
                }
                $('#saveCertification').prop('disabled', false).text('Save Certification');
            },
            onError: function (error) {
                console.error('Error saving certification:', error);
                showToast('error', 'Error', 'Failed to save certification');
                $('#saveCertification').prop('disabled', false).text('Save Certification');
            }
        });
    }
    function loadColorConditions() {
        let colorData = []
        apiRequest({
            url: 'https://login-users.hygeiaes.com/mhc/diagnostic-assessment/getAllColorCodes',
            method: 'GET',
            dataType: 'json',
            onSuccess: function (response) {
                if (response.result && Array.isArray(response.data)) {
                    response.data.forEach(function (item) {
                        const title = item.certification_title?.trim() || '';
                        const conditions = Array.isArray(item.condition) ? item.condition : [];
                        const colors = Array.isArray(item.color_condition) ? item.color_condition : [];
                        for (let i = 0; i < Math.min(conditions.length, colors.length); i++) {
                            const condition = conditions[i]?.trim();
                            const color = colors[i]?.trim();
                            if (condition && color && title) {
                                colorData.push({
                                    certification_title: title,
                                    condition,
                                    color
                                });
                            }
                        }
                    });
                }
            },
            onError: function (error) {
                console.error('Error loading color codes:', error);
            }
        });
        return colorData;
    }
    function getStatusBadgeClass(status) {
        switch (status.toLowerCase()) {
            case 'pending':
                return 'bg-label-warning';
            case 'schedule':
                return 'bg-label-info';
            case 'in process':
                return 'bg-label-primary';
            case 'test completed':
                return 'bg-label-success';
            case 'result ready':
                return 'bg-label-success';
            case 'no show':
                return 'bg-label-danger';
            case 'certified':
                return 'bg-label-success';
            case 'cancelled':
                return 'bg-label-dark';
            default:
                return 'bg-label-secondary';
        }
    }
    function formatDateToDDMMYYYY(dateString) {
        if (!dateString) return '-';
        try {
            let date = new Date(dateString);
            if (isNaN(date.getTime())) return '-';
            let day = String(date.getDate()).padStart(2, '0');
            let month = String(date.getMonth() + 1).padStart(2, '0');
            let year = date.getFullYear();
            return `${day}/${month}/${year}`;
        } catch (e) {
            return '-';
        }
    }
    $(document).ready(function () {
        flatpickr(".flatpickr-date", {
            dateFormat: "d/m/Y",
            allowInput: true
        });
        $('#certificationModal').on('click', '.btn-close', function () {
            $('#certificationModal').modal('hide');
        });
        if (!$('#certificationBadgeCSS').length) {
            $('head').append(certificationBadgeCSS);
        }
        if (!$('#additionalModalCSS').length) {
            $('head').append(additionalModalCSS);
        }
        $('#certificationModal').on('hidden.bs.modal', function () {
            $('#certificationViewContent').remove();
            $('#conditionSelect').closest('.col-md-4').show();
            $('#issueDateInput').closest('.col-md-4').show();
            $('#nextAssessmentInput').closest('.col-md-4').show();
            $('#remarksInput').closest('.col-12').show();
            $('.modal-footer').show();
            $('.modal-footer .btn-secondary').show();
            $('#saveCertification').show();
            $('#conditionSelect').prop('disabled', false);
            $('#remarksInput').prop('disabled', false);
            $('#conditionSelect').val('');
            $('#remarksInput').val('');
            $('#issueDateInput').val('');
            $('#nextAssessmentInput').val('');
            $('#badgePreview').hide();
        });
        $('#clearFilters').off('click').on('click', clearAllFilters);
        let isAdvancedVisible = false;
        $('#toggleAdvancedFilters').on('click', function () {
            const $advancedRow = $('#advancedFiltersRow');
            const $icon = $('#toggleIcon');
            const $text = $('#toggleText');
            const $button = $(this);
            $button.prop('disabled', true);
            if (!isAdvancedVisible) {
                $advancedRow.removeClass('sliding-up').addClass('sliding-down');
                $advancedRow.css('display', 'flex');
                $icon.addClass('rotated');
                $text.text('Hide Advanced Filters');
                $button.removeClass('btn-danger').addClass('btn-danger');
                isAdvancedVisible = true;
                setTimeout(() => {
                    $button.prop('disabled', false);
                }, 400);
            } else {
                $advancedRow.removeClass('sliding-down').addClass('sliding-up');
                $icon.removeClass('rotated');
                $text.text('Advanced Filters');
                $button.removeClass('btn-danger').addClass('btn-danger');
                setTimeout(() => {
                    $advancedRow.css('display', 'none');
                    $advancedRow.removeClass('sliding-up');
                    $button.prop('disabled', false);
                }, 400);
                isAdvancedVisible = false;
            }
        });
        colorData = loadColorConditions();
        loadHealthPlans();
        bindFilterEvents();
        bindModalEvents();
    });
</script>
@endsection