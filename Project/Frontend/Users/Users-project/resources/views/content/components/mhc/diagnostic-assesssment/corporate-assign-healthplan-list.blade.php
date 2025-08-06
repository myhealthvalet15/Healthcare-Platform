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
<link rel="stylesheet" href="/lib/css/page-styles/corporate-assign-healthplan-list.css">
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