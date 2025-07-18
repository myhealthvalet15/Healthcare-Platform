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
<script src="/lib/js/page-scripts/corporate-assign-healthplan-list.js"></script>
@endsection